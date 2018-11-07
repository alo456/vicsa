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
use App\Form\VFileType;


class ActivationController extends Controller
{
    private $billName = '';
    private $contractName = '';
    public function index(Request $request)
    {
        $message = '';
        $activationNames = [];
        $directory = $this->get('kernel')->getProjectDir() . '/Contracts';
        $form = $this->get('form.factory');
        $formFiles = $form->createNamedBuilder("Files", VFileType::class, [])->getForm();
        $formFiles->handleRequest($request);
        if($formFiles->isSubmitted() && $formFiles->isValid()){
            $activation = $formFiles->getData(); 
            foreach ($activation['files'] as $file) {
                $file->move(
                    $directory, $file->getClientOriginalName()
                );
                $activationNames[] = $file->getClientOriginalName();
            }
            $message = $this->extractActivation($activationNames);           
            //var_dump($activationNames);
        }
        $files = scandir($directory);
        for($i=2;$i<sizeof($files);$i++){
            //--------------------------------Extracting specific data for existing files-------------------
            $file = $directory.'/'. $files[$i];
            $PDFParser = new Parser();
            $pdf = $PDFParser->parseFile($file);
            $pages = $pdf->getPages();
            $text = $pages[0]->getText();
            $text = preg_replace('/\s{2,}|\t{1,}|\n/',' ',$text);
            //var_dump($text);
            //-----------------------line number ------------------------------
            preg_match('/(?<=Línea: ).{10}/', $text, $matches);
            $lineNumber = $matches[0];
            //var_dump($lineNumber);
            $savedFiles[$files[$i]] = $lineNumber;
        }
        //var_dump($savedFiles);
        
        return $this->render('activation/index.html.twig', [
                    'formFiles' => $formFiles->createView(),
                    'message' => $message,
                    'savedFiles' => $savedFiles
        ]);
    }

    public function extractActivation($activationNames){
        $message = [];
        $em = $this->getDoctrine()->getManager();
        ini_set('xdebug.var_display_max_data', '100000');

        for($i=0;$i<sizeof($activationNames);$i++){
            $file = $this->get('kernel')->getProjectDir() . '/Contracts/'. $activationNames[$i];
            $PDFParser = new Parser();
            $pdf = $PDFParser->parseFile($file);
            $text = $pdf->getText();
            $text = preg_replace('/\s{2,}|\t{1,}|\n/',' ',$text);
            $client = $this->generateClient($text);
            //var_dump($text);

    //---------------------------Generating Activation Info----------------------
        
            //-----------------------line number ------------------------------
            preg_match('/(?<=Línea: ).{10}/', $text, $matches);
            $lineNumber = $matches[0];
            

                //-----------------------Activation Date -------------------------------
            preg_match('/(?<=Enteradas las Partes de su alcance y contenido, lo aceptan y firman en ).* ./', $text, $matches);
            $matches = $matches[0];
            $limit = $client->getSurname();
            $limit = explode(' ',$limit);
            $limit = $limit[0];
            $actDate = $this->extraer($matches,$limit);
            preg_match('/\d{1,}/', $actDate, $matches);
            $day = $matches[0];
            preg_match('/\d{4}/', $actDate, $matches);
            $year = $matches[0];
            preg_match('/(?<=mes de ).* de/', $actDate, $matches);
            $matches = $matches[0];
            $month =  $this->extraer($matches,'de');
            $month = $this->getMonthNumber($month);
            
            $actDate = new \DateTime($year.'-'.$month.'-'.$day);

            //var_dump($text);
                //------------------------IMEI--------------------------------------
            preg_match('/(?<=IMEI: )\d{15}/', $text, $matches);
            $imei = $matches[0];
            //var_dump($imei);
                //------------------------ICCID---------------------------------------
            preg_match('/(?<=ICCID \(SIM Card\): )\d{18,}/', $text, $matches);
            
            $iccid = $matches[0];
            //var_dump($iccid);
            
        

            try{
                    //-------------------------------generating activation entity-------------------------------
                $activation = new Activation();
                $activation->setLineNumber($lineNumber);
                $activation->setActDate($actDate);

                //-------------------device & sim tests----------------------------
                $device = $em->getRepository('App\Entity\Device')->findOneBy(array('imei' => $imei));
                $sim = $em->getRepository('App\Entity\Sim')->findOneBy(array('iccid' => $iccid));
                
               
                
                if($device && $sim){
                    $activation -> setDevice($device);
                    $activation -> setSim($sim);

                    //--------------------------updating warehouse data--------------------
                    $warehouse = $device -> getWarehouse();
                    $warehouse -> removeDevice($device);
                    $warehouse -> setQuantity($warehouse->getQuantity()-1);
                    $warehouse -> setCost($warehouse->getCost() - $device -> getPrice());
    
    
                    $warehouse = $sim -> getWarehouse();
                    $warehouse -> removeSim($sim);
                    $warehouse -> setQuantity($warehouse->getQuantity()-1);
                    $warehouse -> setCost($warehouse->getCost() - $sim -> getPrice());
    
                    $em -> persist($activation);
    
                //---------------------------generating Contract---------------------------------------
                    $contract = new Contract();
                    
                    //---------------------------Account Number----------------------------------------
                    preg_match('/(?<=Cuenta: ).* CONTRATO DE PRESTACIÓN/', $text, $matches);
                    $matches = $matches[0];
                    $accNum = $this->extraer($matches,'CONTRATO');
    
                    //--------------------------deadlines---------------------------------------
                    preg_match('/(?<=Forzoso: ).* Plan Tarifario:/', $text, $matches);
                    $matches = $matches[0];
                    $deadline = $this->extraer($matches,'Plan');
    
                    //---------------------------plan name----------------------------------------
                    preg_match('/(?<=Tarifario: ).* Cargo Fijo/', $text, $matches);
                    $matches = $matches[0];
                    $planName = $this->extraer($matches,'Cargo');

                     //-------------------------------total price for the client-------------------
                     preg_match('/(?<=Costo Total: \$ ).* Pago Inicial/', $text, $matches);
                     $matches = $matches[0];
                     $totalPrice = $this->extraer($matches,'Pago');
                     $totalPrice = str_replace(',','',$totalPrice);
                     $totalPrice = floatval($totalPrice);
    
    
                    
                    //------------employee test------------------
                    $employee = $em->getRepository('App\Entity\Employee')->findOneBy(array('email' => "jorge@vicsa.com"));
    
                    
                    $contract ->setEmployee($employee);
                    $contract -> setActivation($activation);
                    $contract -> setAccountNumber($accNum);
                    $contract -> setDeadlines($deadline);
                    $contract -> setPlanName($planName);
                    $contract -> setTotalPrice($totalPrice);
    
                    $em->persist($contract);
    
                    $client -> addContract($contract);
                    $em->flush();
                    $message[$client->getRfc()] = "OK";
                }
                else{
                    $message[$client->getRfc()] = "No existe dicho dispositivo/sim en almacén.";
                }
               
            }
            catch(\Exception $ex){
                    $message[$client->getRfc()] = $ex->getMessage();
            }
        }
        //var_dump($message);
        return $message;
    }

    public function generateClient($text){
        $em = $this->getDoctrine()->getManager();
        $rfc = $this->getRFC($text);
        $client = $em->getRepository('App\Entity\Client')->findOneBy(array('rfc' => $rfc));
        //var_dump($client);
        if($client==null){
            $client = new Client();
            $data = $this->extractClientInfo($text, $rfc);
            $client ->setAddress($data['address']);
            $client ->setRfc($rfc);
            $client ->setName($data['name']);
            $client ->setSurname($data['surname']);
            $client ->setBirthdate($data['birthdate']);
            $client ->setPhoneNumber($data['phone']);
            $client ->setEmail($data['email']);
            $em->persist($client);
            $em->flush();
        }
        
        return $client;
    }

    public function getRFC($text){
        preg_match('/(?<=RFC: ).{13}/', $text, $matches);
        $rfc = $matches[0];
        return $rfc;
    }

    public function extractClientInfo($text, $rfc){
        $em = $this->getDoctrine()->getManager();
        $data = [];

        //-------------------Full Name------------------------------
        preg_match('/(?<=Nombre: ).* Fecha de Nacimiento o RFC/', $text, $matches);
        $matches = $matches[0];
        $fullName = $this->extraer($matches,'Fecha');
        $fullName = explode(' ',$fullName);
        $surname = $fullName[0].' '.$fullName[1];
        $name='';
        for($i=2;$i<sizeof($fullName);$i++){
            $name = $name.$fullName[$i].' ';
        }
        $name = trim($name);

        $data['name'] = $name;
        $data['surname'] = $surname;

        //-----------------------------birthdate-------------------------
        $year = $rfc[4].$rfc[5];
        $month = $rfc[6].$rfc[7];
        $day = $rfc[8].$rfc[9];

        $birthdate = new \DateTime($year.'-'.$month.'-'.$day);
        $data['birthdate'] = $birthdate;
        //var_dump($birthdate->format('m/d/Y'));
        

        //-------------------------phone number-------------------------------
        preg_match('/(?<=Particular: ).* Teléfono de Oficina/', $text, $matches);
        $matches = $matches[0];
        $phoneNumber = $this->extraer($matches,'Teléfono');
        $data['phone'] = $phoneNumber;

        //------------------------email--------------------------------------
        preg_match('/(?<=Electrónico: ).* Teléfono Particular/', $text, $matches);
        $matches = $matches[0];
        $email = $this->extraer($matches,'Teléfono');
        $data['email'] = $email;

        //var_dump($data);

        //--------------------------------address-----------------------------
        $address = new Address();

            //--------------------------street-----------------------------------
        preg_match('/(?<=Calle: ).* Número Exterior/', $text, $matches);
        $matches = $matches[0];
        $street = $this->extraer($matches,'Número');
        
        
            //-------------------------Exterior Number-----------------------
        preg_match('/(?<=Exterior: ).* Colonia/', $text, $matches);
        $matches = $matches[0];
        $extNum = $this->extraer($matches,'Colonia');


            //---------------------------Interior Number----------------------
        preg_match('/(?<=Interior: ).* Entre la Calle/', $text, $matches);
        if($matches){
            $matches = $matches[0];
            $intNum = $this->extraer($matches,'Entre');
        } 
        
        else $intNum = "S/N";
        

            //---------------------------State-------------------------
         preg_match('/(?<=Federativa: ).* Delegación/', $text, $matches);
         $matches = $matches[0];
         $state = $this->extraer($matches,'Delegación');
 
 
            //-------------------------City--------------------------
          preg_match('/(?<=Ciudad: ).* Entidad/', $text, $matches);
          $matches = $matches[0];
          $city = $this->extraer($matches,'Entidad');
          
            //-------------------------P.C.------------------------------
          preg_match('/(?<=C.P.: ).* Ciudad/', $text, $matches);
          $matches = $matches[0];
          $pc = $this->extraer($matches,'Ciudad:');
 
 
            //--------------------------Township---------------------------
         preg_match('/(?<=Municipio: ).* Correo Electrónico/', $text, $matches);
         $matches = $matches[0];
         $township = $this->extraer($matches,'Correo');

 
            //--------------------------Colony----------------------------
         preg_match('/(?<=Colonia: ).* Número Interior/', $text, $matches);
         $matches = $matches[0];
         $colony = $this->extraer($matches,'Número');

        $address ->setStreet($street);
        $address ->setExtNumber($extNum);
        $address ->setInNumber($intNum);
        $address ->setState($state);
        $address ->setCity($city);
        $address ->setColony($colony);
        $address ->setPc($pc);
        $address ->setTownship($township);
        $em->persist($address);

        $data['address'] = $address;
        
        return $data;

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

    public function getMonthNumber($month){
        $name['Enero'] = '01';
        $name['Febrero'] = '02';
        $name['Marzo'] = '03';
        $name['Abril'] = '04';
        $name['Mayo'] = '05';
        $name['Junio'] = '06';
        $name['Julio'] = '07';
        $name['Agosto'] = '08';
        $name['Septiembre'] = '09';
        $name['Octubre'] = '10';
        $name['Noviembre'] = '11';
        $name['Diciembre'] = '12';

        return $name[$month];

    }

}
