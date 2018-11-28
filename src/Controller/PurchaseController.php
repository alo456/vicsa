<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\DeviceBill;
use App\Entity\Sim;
use App\Entity\SimBill;
use App\Form\VFileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;



class PurchaseController extends Controller
{
    
    public function index(Request $request)
    {
       
        $em = $this->getDoctrine()->getManager();
        $message = [];
        $device = $em->getRepository('App\Entity\Device')->findAll();
        $sim = $em->getRepository('App\Entity\Sim')->findAll();
        $purchase = array_merge($device,$sim);
        //file form
        $form = $this->get('form.factory');
        $formFiles = $form->createNamedBuilder("Files", VFileType::class, [])->getForm();
        $formFiles->handleRequest($request);
        $directory = $this->get('kernel')->getProjectDir() . '\public\Excel';
        if($formFiles->isSubmitted() && $formFiles->isValid()){
            //var_dump($formFiles->getData()['files'][0]);
            $file = $formFiles->getData()['files'][0];
            $file->move(
                $directory, $file->getClientOriginalName()
            );
            $message = $this->extractExcel($file->getClientOriginalName());
            $message['send'] = "ok";

        }
        return $this->render('purchase/index.html.twig',[ 
                'formFiles' => $formFiles->createView(),
                'purchase' => $purchase
                ]);
    }
    
    private function extractExcel($fileName){
        $message = [];
        $em = $this->getDoctrine()->getManager();
        $inputFileName = $this->get('kernel')->getProjectDir() . '/public/Excel/'.$fileName;  //ruta del archivo
        
        //var_dump($inputFileName);
        
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName); //recupera el excel
        $worksheet = $spreadsheet->getActiveSheet(); //obtiene la hoja del excel


        $highestRow = $worksheet->getHighestRow(); //obtiene la cantidad maxima de columnas con datos
        //se definen las columnas donde se encuentra la información para Device/Sim_bill
        $cdoc_number = 30; //columan del doc_number
        $cbill_date = 1;
        $payment_term = 'PUE';
        $cquantity = 36;
        $discount = 0.0;
        $c_iccid_imei = 38;
        $c_mat_key = 34;

        //se definen las columnas para Device/Sim
        $cdescripcion = 35;
        $cprice = 40;


        //arrays para lo anterior definido;
        $docs_number = array(); //columan del doc_number
        $bills_date = array();
        $quantitys = array();
        $iccid_imei = array();
        $descripcions = array();
        $prices = array();
        $mat_keys = array();
        $warehouse = $em->getRepository('App\Entity\Warehouse')->findOneBy(array('id' => 6));

        for ($row = 2; $row <= $highestRow; ++$row) {

            $items = $worksheet->getCellByColumnAndRow($cdoc_number, $row)->getValue();
            $docs_number[] = $items;

            $items = $worksheet->getCellByColumnAndRow($cbill_date, $row)->getValue();
            $bills_date[] = $items;

            $items = $worksheet->getCellByColumnAndRow($cquantity, $row)->getValue();
            $quantitys[] = $items;

            $items = $worksheet->getCellByColumnAndRow($c_iccid_imei, $row)->getValue();
            $iccid_imei[] = $items;

            $items = $worksheet->getCellByColumnAndRow($cdescripcion, $row)->getValue();
            $descripcions[] = $items;

            $items = $worksheet->getCellByColumnAndRow($cprice, $row)->getValue();
            $prices[] = $items;

            $items = $worksheet->getCellByColumnAndRow($c_mat_key, $row)->getValue();
            $mat_keys[] = $items;
        }

        for ($i = 0; $i < $highestRow - 1;  ++$i) {
            $lenght = strlen($iccid_imei[$i]);
            $dateObj = \DateTime::createFromFormat("m.d.Y", $bills_date[$i]);
            if ($dateObj) {
                if ($lenght == 15) {
                    //es ime -> Device
                    $Bill = $em->getRepository('App\Entity\DeviceBill')->findOneBy(array('docNumber' => $docs_number[$i]));
                    $Device = $em->getRepository('App\Entity\Device')->findOneBy(array('imei' =>$iccid_imei[$i]));
                    //Device Bill
                    if ($Bill == null) {

                        $Bill = new DeviceBill();
                        $Bill->setDocNumber($docs_number[$i]);
                        $Bill->setbillDate($dateObj);
                        $Bill->setPaymentTerm($payment_term);
                        $Bill->setQuantity(1);
                        $Bill->setDiscount($discount);
                        $em->persist($Bill);
                        $em->flush();
                    }
                    if(!$Device){
                        $Device = new Device();
                        $Device->setImei($iccid_imei[$i]);
                        $Device->setMatKey($mat_keys[$i]);
                        $Device->setDescription($descripcions[$i]);
                        $Device->setPrice($prices[$i]);
                        $Device->setEntryDate($dateObj);
                        $Device->setExitDate(null);
                        $Device->setDeviceBill($Bill);
                        $Device->setNote(NULL);
                        $Bill->addDevice($Device);
                        $Bill->setQuantity($Bill->getQuantity() + 1);
                        $warehouse->addDevice($Device);
                        $em->persist($Device);
                        $em->flush();
                    }
                    

                    //SIM
                } else if ($lenght == 19) {
                    // es iccid ->SIM
                    $Bill = $em->getRepository('App\Entity\SimBill')->findOneBy(array('docNumber' => $docs_number[$i]));
                    $sim = $em->getRepository('App\Entity\Sim')->findOneBy(array('iccid' =>$iccid_imei[$i]));
                    //SimBill
                    if ($Bill == null) {
                        $Bill = new SimBill();
                        $Bill->setdocNumber($docs_number[$i]);
                        $Bill->setbillDate($dateObj);
                        $Bill->setPaymentTerm($payment_term);
                        $Bill->setQuantity(1);
                        $Bill->setDiscount($discount);
                        $em->persist($Bill);
                        $em->flush();
                    }


                    //SIM
                    if (!$sim){
                        $sim = new Sim();
                        $sim->setIccid($iccid_imei[$i]);
                        $sim->setMatKey($mat_keys[$i]);
                        $sim->setDescription($descripcions[$i]);
                        $sim->setPrice($prices[$i]);
                        $sim->setEntryDate($dateObj);
                        $sim->setExitDate(null);
                        $sim->setSimBill($Bill);
                        $sim->setNote(NULL);
                        $Bill->addSim($sim);
                        $Bill->setQuantity($Bill->getQuantity() + 1);
                        $warehouse->addSim($sim);
                        $em->persist($sim);
                        $em->flush();
                    }
                }

                //----------updating warehouse-----------------
                $warehouse ->setQuantity($warehouse->getQuantity() + 1);
                $warehouse ->setCost($warehouse->getCost() + $prices[$i]);
            }
            else{
                $message[$i] = "No se reconoció una fecha válida. Revisar registro.";
            }
            $em->flush();
        }
    }
    
    
}
