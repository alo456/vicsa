<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\Definition\Exception\Exception;
use Smalot\PdfParser\Parser;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Address;
use App\Entity\Client;
use App\Entity\Activation;
use App\Entity\Contract;
use App\Entity\Employee;
use App\Entity\Device;
use App\Entity\Sim;
use App\Entity\Note;
use App\Form\VFileType;


class NoteController extends Controller
{
    public function index(Request $request)
    {
        ini_set('xdebug.var_display_max_data', '110000');
        $noteNames = $message = [];
        $directory = $this->get('kernel')->getProjectDir() . '/public/Notes';
        $form = $this->get('form.factory');
        $formFiles = $form->createNamedBuilder("Files", VFileType::class, [])->getForm();
        $formFiles->handleRequest($request);
        if($formFiles->isSubmitted() && $formFiles->isValid()){
            $note = $formFiles->getData(); 
            foreach ($note['files'] as $file) {
                $file->move(
                    $directory, $file->getClientOriginalName()
                );
                $noteNames[] = $file->getClientOriginalName();
            }           
            $message = $this->extractNote($noteNames);
        }

        $files = scandir($directory);
        $savedFiles = [];
        for($i=2;$i<sizeof($files);$i++){
            //--------------------------------Extracting specific data for existing files-------------------
            $file = $directory.'/'. $files[$i];
            $PDFParser = new Parser();
            $pdf = $PDFParser->parseFile($file);
            $text = $pdf->getText();
            $text = preg_replace('/\s{2,}|\t{1,}|\n/',' ',$text);
            //var_dump($text);
            //-------------------------quantity-------------------------------
            preg_match('/(?<=Total de Pzas Factura: ).* SELLO DIGITAL DEL EMISOR:/', $text, $matches);
            $matches = $matches[0];
            $quantity = $this->extraer($matches,'SELLO');

            //-------------------------total-------------------------------
            preg_match('/(?<=M.N. \) ).* ELABORADO POR:/', $text, $matches);
            $matches = $matches[0];
            $total = $this->extraer($matches,'ELABORADO');

            $savedFiles[$files[$i]] = array($quantity,$total);
        }

        //var_dump($savedFiles);

        return $this->render('note/index.html.twig',[
            'formFiles' => $formFiles->createView(),
            'savedFiles' => $savedFiles
        ]);
    }

    public function extractNote($noteNames){
        $message = [];
        $em = $this->getDoctrine()->getManager();
        ini_set('xdebug.var_display_max_data', '110000');

        for($i=0;$i<sizeof($noteNames);$i++){
            $file = $this->get('kernel')->getProjectDir() . '/public/Notes/'. $noteNames[$i];
            $PDFParser = new Parser();
            $pdf = $PDFParser->parseFile($file);
            $text = $pdf->getText();
            $text = preg_replace('/\s{2,}|\t{1,}|\n/',' ',$text);
            //var_dump($text);

             //-------------------------checking if it's a note -------------------------------
            preg_match('/(?<=Tipo de Comprobante: ).* egreso/', $text, $matches);
            $matches = $matches[0];
            $docCheck = $this->extraer($matches,'egreso');

            if($docCheck == 'E') $message[$noteNames[$i]] = $this->generateNote($text);
            else $message[$noteNames[$i]] = "Este PDF no pertenece a una nota de crédito.";
        }
        //var_dump($message);
        //die;
        return $message;
    }

    public function generateNote($text){
        $em = $this->getDoctrine()->getManager();
        $message = [];
        $device = $sim = null;
        //var_dump($text);
        
        //-------------------------doc Number -------------------------------
        preg_match('/(?<=DOCUMENTO ).* FECHA Y HORA/', $text, $matches);
        $matches = $matches[0];
        $doc_number = $this->extraer($matches,'FECHA');
        //$data['phone'] = $phoneNumber;
        //var_dump($doc_number);

        //-------------------------checking for existing note-------------------
        $note = $em->getRepository('App\Entity\Note')->findOneBy(array('docNumber' => $doc_number));
        //if($note) var_dump($note->getId());
        if($note == null){
            $origText = $text;
            //-------------------------IMEI/ICCID-------------------------------
            $imei = $iccid = null;
            preg_match('/(?<=CDV7 ).* CLAVE PROD O SERV/', $text, $matches);
            $text = $matches[0];
            preg_match('/\d{15,}/', $text, $matches);
            $matches = $matches[0];
            if(strlen($matches)== 15){
                $imei = $matches;
                $device = $em->getRepository('App\Entity\Device')->findOneBy(array('imei' => $imei));     
                $activation = $em->getRepository('App\Entity\Activation')->findOneBy(array('device' => $device->getId()));
            }else{
                $iccid = $matches;
                $sim = $em->getRepository('App\Entity\Sim')->findOneBy(array('iccid' => $iccid));
                $activation = $em->getRepository('App\Entity\Activation')->findOneBy(array('sim' => $sim->getId()));
            }

            if(($device || $sim) && $activation){
                $text = $origText;
                //-------------------------date-------------------------------
                preg_match('/(?<=FECHA Y HORA DE EXPEDICIÓN ).* SERIE - FOLIO/', $text, $matches);
                $matches = $matches[0];
                $noteDate = $this->extraer($matches,'SERIE');
                //$data['phone'] = $phoneNumber;
                $noteDate = new \DateTime($noteDate);
                //var_dump($noteDate);
                
                //-------------------------payment term-------------------------------
                preg_match('/(?<=CONDICIONES DE PAGO ).* FOLIO FISCAL/', $text, $matches);
                $matches = $matches[0];
                $payment = $this->extraer($matches,'FOLIO');
                //var_dump($payment);
                
                //-------------------------quantity-------------------------------
                preg_match('/(?<=Total de Pzas Factura: ).* SELLO DIGITAL DEL EMISOR:/', $text, $matches);
                $matches = $matches[0];
                $quantity = $this->extraer($matches,'SELLO');
                //var_dump($quantity);

                //-------------------------discount-------------------------------
                preg_match('/(?<=PZA ).* CDV7/', $text, $matches);
                $matches = $matches[0];
                $discount = $this->extraer($matches,'CDV7');
                $discount = explode(' ',$discount);
                $price = $discount[0];
                $discount = $discount[1];
                //var_dump($discount);
                //var_dump($price);

                //-----------------------------generating note----------------------
                $note = new Note();
                $note -> setDocNumber($doc_number);
                $note -> setNoteDate($noteDate);
                $note -> setPaymentTerm($payment);
                $note -> setQuantity($quantity);
                $note -> setDiscount($discount);

                $em->persist($note);

                if($device){
                    $note ->addDevice($device);
                    $activation->setNote($note);
                    $message[$imei] = $note;
                }else{
                    $note ->addSim($sim);
                    $activation->setNote($note);
                    $message[$iccid] = $note;
                }
                $em->flush();
            }else{
                $message[$doc_number] = "El dispositivo/sim de esta nota no está registrado.";
            }

        }else{
            $message[$doc_number] = "Esta nota ya fue registrada.";
        }
        
        
         return $message;
         
    }

    public function extraer($fullString, $limit){
        $fullString = explode(' ',$fullString);
        $result = '';
        foreach($fullString as $word){
            if($word == $limit) break;
            $result = $result . ' ' . $word;
        }
        $result = trim($result);
        return $result; 
    }
}
