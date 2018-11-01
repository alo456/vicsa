<?php

namespace App\Controller;
    
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Device;
use App\Entity\Sim;
use App\Entity\DeviceBill;
use App\Entity\SimBill;




class PurchaseController extends AbstractController
{
    
     public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $inputFileName = 'C:\wamp64\www\vicsa\Excel\ejemplo1.xlsx';  //ruta del archivo
       
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName); //recupera el excel
        $worksheet = $spreadsheet->getActiveSheet(); //obtiene la hoja del excel
        
        
        $highestRow = $worksheet->getHighestRow(); //obtiene la cantidad maxima de columnas con datos
        
        //se definen las columnas donde se encuentra la información para Device/Sim_bill
        $cdoc_number =30 ; //columan del doc_number
        $cbill_date=1;
        $payment_term='PUE';
        $cquantity=36;
        $discount =0.0;
        $c_iccid_imei=38;
        
        //se definen las columnas para Device/Sim
        $cdescripcion=35;
        $cprice=40;
        
        
        //arrays para lo anterior definido;
        $docs_number =array(); //columan del doc_number
        $bills_date=array();
        $quantitys=array();
        $iccid_imei=array();
        $descripcions=array();
        $prices=array();
       
        for ($row = 2; $row <= $highestRow; ++$row) {
            
            $items=$worksheet->getCellByColumnAndRow($cdoc_number, $row)->getValue();
            $docs_number[]=$items;
            
            $items=$worksheet->getCellByColumnAndRow($cbill_date, $row)->getValue();
            $bills_date[]=$items;
            
            $items=$worksheet->getCellByColumnAndRow($cquantity, $row)->getValue();
            $quantitys[]=$items;
            
            $items=$worksheet->getCellByColumnAndRow($c_iccid_imei, $row)->getValue();
            $iccid_imei[]=$items;
            
            $items=$worksheet->getCellByColumnAndRow($cdescripcion, $row)->getValue();
            $descripcions[]=$items;
            
            $items=$worksheet->getCellByColumnAndRow($cprice, $row)->getValue();
            $prices[]=$items;
            
          
            
        }
        
        for($i=0;$i< $highestRow-1;++$i){      
            $lenght =  strlen($iccid_imei[$i]);
            $dateObj = \DateTime::createFromFormat("m.d.Y", $bills_date[$i]);
            if (!$dateObj)
            {
                throw new \UnexpectedValueException("Could not parse the date: $bills_date[$i]");
            }
          
          
           
           
           if($lenght==15){
               //es ime -> Device
               $Bill = $em->getRepository('App\Entity\DeviceBill')->findOneBy(array('docNumber' => $docs_number[$i]));
               //Device Bill
               if($Bill==null){
                   
             
                $Bill= new DeviceBill();
                $Bill->setDocNumber($docs_number[$i]);
                $Bill->setbillDate($dateObj);
                $Bill->setPaymentTerm($payment_term);
                $Bill->setQuantity($quantitys[$i]);
                $Bill->setDiscount($discount);
                
                
                
               }
               
                $Device= new Device();
                $Device->setImei($iccid_imei[$i]);
                $Device->setMatKey(0);
                $Device->setDescription($descripcions[$i]);
                $Device->setPrice($prices[$i]);
                $Device->setEntryDate($dateObj);
                $Device->setExitDate(null);
                $Device->setWarehouse(null);
                $Device->setDeviceBill($Bill);
                $Device->setNote(NULL);
                $Bill->addDevice($Device);
                $em->persist($Device);
                $em->persist($Bill);
                $em->flush();
               
                 
                
                //Device
               
                
           }
           else if($lenght==19){
               // es iccid ->SIM
               $Bill = $em->getRepository('App\Entity\SimBill')->findOneBy(array('docNumber' => $docs_number[$i]));
               //SimBill
               if($Bill==null){
                $Bill= new SimBill();
                $Bill.setdocNumber($docs_number[$i]);
                $Bill.setbillDate($dateObj);
                $Bill.setPaymentTerm($payment_term);
                $Bill.setQuantity($quantitys[$i]);
                $Bill.setDiscount($discount);
               }
               
                
                //SIM
                $Device= new Sim();
                $Device->setIccid($iccid_imei[$i]);
                $Device->setMatKey(0);
                $Device->setDescription($descripcions[$i]);
                $Device->setPrice($prices[$i]);
                $Device->setEntryDate($dateObj);
                $Device->setExitDate(null);
                $Device->setWarehouse(null);
                $Device->setSimBill($Bill);
                $Device->setNote(NULL);
                $Bill->addSim($Device);
                $em->persist($Device);
                $em->persist($Bill);
                $em->flush();
           }
        }
        

      
        return $this->render('purchase/index.html.twig');
    }
    
    
}
