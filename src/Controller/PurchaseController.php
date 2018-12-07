<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\DeviceBill;
use App\Entity\Sim;
use App\Entity\SimBill;
use App\Form\VFileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Smalot\PdfParser\Parser;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class PurchaseController extends Controller
{
    
    public function index(Request $request)
    {
       
        $em = $this->getDoctrine()->getManager();
        $message = [];
        $device = $em->getRepository('App\Entity\Device')->findAll();
        $sim = $em->getRepository('App\Entity\Sim')->findAll();
        $purchase = array_merge($device,$sim);
        $purchaseFiles =[];
        //file form
        $form = $this->get('form.factory');
        $formFiles = $form->createNamedBuilder("Files", VFileType::class, [])->getForm();
        $formFiles->handleRequest($request);
        $directory = $this->get('kernel')->getProjectDir() . '\public\Excel';
        if($formFiles->isSubmitted() && $formFiles->isValid()){
            //var_dump($formFiles->getData()['files'][0]);
            $purchaseFiles = $formFiles ->getData();
            foreach ($purchaseFiles['files'] as $file) {
                if($file->getClientMimeType()== "application/pdf"){
                    $file->move(
                        $directory, $file->getClientOriginalName()
                    );
                    $message = array_merge($this->extractPurchaseFromPdf($file->getClientOriginalName()),$message);

                }
                else if ($file->getClientMimeType()== "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){
                    $file->move(
                        $directory, $file->getClientOriginalName()
                    );
                    $message = array_merge($this->extractExcel($file->getClientOriginalName()),$message);
                }
                else{
                    $message[$file->getClientOriginalName()] = "Archivo inválido";
                }     
            }
            $message['send'] = "ok";
            //var_dump($message);
            
        }

        return $this->render('purchase/index.html.twig',[ 
                'formFiles' => $formFiles->createView(),
                'purchase' => $purchase,
                'message' => $message
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
                $message[$i] = "No se reconoció una fecha válida. Revisar registro: ".($i+1);
            }
            $em->flush();
        }
        return $message;
    }

    private function extractPurchaseFromPdf($fileName){
        ini_set('xdebug.var_display_max_data', '110000');
        $em = $this->getDoctrine()->getManager();
        $warehouse = $em->getRepository('App\Entity\Warehouse')->findOneBy(array('id' => 6));
        $message = [];

        //--------------------------extracting and checking bills-----------------------------
            $file = $this->get('kernel')->getProjectDir() . '/public/Excel/'. $fileName;
            $PDFParser = new Parser();
            $pdf = $PDFParser->parseFile($file);
            $text = $pdf->getText();
            $text = preg_replace('/\s{2,}|\t{1,}|\n/',' ',$text);
            //var_dump($text);

             //-------------------------checking if it's a bill -------------------------------
            preg_match('/(?<=Tipo de Comprobante: ).* ingreso/', $text, $matches);
            if(!isset($matches[0])) $message[$fileName] = $fileName." no es una factura.";
            else{
                $device = $sim = null;
                //var_dump($text);
                
                //-------------------------doc Number -------------------------------
                preg_match('/(?<=DOCUMENTO ).* FECHA Y HORA/', $text, $matches);
                $matches = $matches[0];
                $doc_number = $this->extraer($matches,'FECHA');
                //$data['phone'] = $phoneNumber;
                //var_dump($doc_number);

                //-------------------------checking for existing note-------------------
                $deviceBill = $em->getRepository('App\Entity\DeviceBill')->findOneBy(array('docNumber' => $doc_number));
                $simBill = $em->getRepository('App\Entity\SimBill')->findOneBy(array('docNumber' => $doc_number));

                //if($note) var_dump($note->getId());
                if($deviceBill == null && $simBill == null){
                //---------------------------general bill data---------------------------------------
                    //-------------------------doc number-------------------------------
                    preg_match('/(?<=DOCUMENTO ).* FECHA Y HORA DE CERTIFICACIÓN/', $text, $matches);
                    $matches = $matches[0];
                    $doc_number = $this->extraer($matches,'FECHA');
                    //var_dump($doc_number);
                    
                    //-------------------------date-------------------------------
                    preg_match('/(?<=FECHA Y HORA DE EXPEDICIÓN ).* SERIE - FOLIO/', $text, $matches);
                    $matches = $matches[0];
                    $billDate = $this->extraer($matches,'SERIE');
                    $billDate = new \DateTime($billDate);
                    //var_dump($billDate);
                    
                    //-------------------------payment term-------------------------------
                    preg_match('/(?<=METODO DE PAGO ).*:Pago en una sola exhibición/', $text, $matches);
                    $matches = $matches[0];
                    $payment = $this->extraer($matches,'en');
                    $code = '';
                    for($i=0;$i<strlen($payment);$i++){
                        if($payment[$i] != ':'){
                            $code =$code . $payment[$i];
                        }
                        else break;
                    }
                    //var_dump($code);
                    
                    //-------------------------quantity-------------------------------
                    preg_match('/(?<=Total de Pzas Factura: ).* SELLO DIGITAL DEL EMISOR:/', $text, $matches);
                    $matches = $matches[0];
                    $quantity = $this->extraer($matches,'SELLO');
                    //var_dump($quantity);
                
                    //----------------------discount, variable set to 0, items will increase it--------------------
                    $discount = 0;
                    

                //---------------------------------Devices/Sims purchased in bill-----------------
                    //-------------------------BLOCKS OF INFO-------------------------------
                    preg_match('/(?<=CLAVE DESCRIPCIÓN CANTIDAD U.M. VALOR UNITARIO DESCUENTO IMPORTE ).* SELLO DIGITAL DEL EMISOR:/', $text, $matches);
                    //var_dump($matches);
                    $fullBlock = $matches[0];
                    $items = explode('IMPUESTOS:',$fullBlock);
                    unset($items[sizeof($items)-1]);
                    //var_dump($items);
                    for($i=1;$i<sizeof($items);$i++){
                        $block = $items[$i];
                        $block = trim($block);
                        $block = explode(' ',$block);
                        unset($block[0]);
                        $items[$i] = implode(' ',$block);
                    }
                    //var_dump($items);
                    
                    foreach($items as $item){
                         //------------------general data from devices/sims----------------
                        $data = $this->extraer($item,'CD09');
                        $description = $this->extraer($data,'PZA');
                        $description = explode(' ',$description);
                        unset($description[sizeof($description)-1]);
                        unset($description[0]);
                        $description = implode(' ', $description);
                        
                        //var_dump($data, $description);
                        $data = explode(' ',$data);
                        $idx = sizeof($data);
                        //------------------------deleting commas from prices for good casting---------
                        //var_dump($data[$idx-3]);
                        $price = explode(',',$data[$idx-3]);
                        $price = implode('',$price);
                        $price = floatval($price);

                        $discount = explode(',',$data[$idx-2]);
                        $discount = implode(' ',$discount);
                        $discount += floatval($discount);
                        //var_dump($price,$discount);

                        $matKey = $data[0];

                        //-------------------------IMEIs/ICCIDs-------------------------------
                        $imei = $iccid = null;
                        preg_match('/(?<=CD09 CAD OAXACA ).* CLAVE PROD O SERV/', $item, $matches);
                        //var_dump($matches);
                        $id = $matches[0];
                        $id = $this->extraer($id,'CLAVE');
                        //var_dump($text);
                        $id = explode(' ',$id);
                        //var_dump($id);
                        $selector = strlen($id[0]);

                        //------------------------Generating Devices and adding them to their bill----------------
                        if($selector == 14 || $selector == 15){
                            //---------------------------generating device bill------------------------------------
                            if(!$deviceBill){
                                $deviceBill = new DeviceBill();
                                $deviceBill -> setDocNumber($doc_number);
                                $deviceBill -> setBillDate($billDate);
                                $deviceBill -> setPaymentTerm($code);
                                $deviceBill -> setQuantity($quantity);
                                $em->persist($deviceBill);
                            }
                            //-----------------------generating devices--------------------------------
                            foreach($id as $imei){
                                $device = new Device();
                                $device -> setImei($imei);
                                $device -> setMatKey($matKey);
                                $device -> setDescription($description);
                                $device -> setPrice($price);
                                $device -> setEntryDate($billDate);
                                $em->persist($device);
                                $deviceBill->addDevice($device);
                                $warehouse->addDevice($device);
                            }
                        
                        }else{
                            //---------------------------generating device bill------------------------------------
                            if(!$simBill){
                                $simBill = new SimBill();
                                $simBill -> setDocNumber($doc_number);
                                $simBill -> setBillDate($billDate);
                                $simBill -> setPaymentTerm($code);
                                $simBill -> setQuantity($quantity);
                                $em->persist($simBill);
                            }
                            //-----------------------generating devices--------------------------------
                            foreach($id as $iccid){
                                $sim = new Sim();
                                $sim -> setIccid($iccid);
                                $sim -> setMatKey($matKey);
                                $sim -> setDescription($description);
                                $sim -> setPrice($price);
                                $sim -> setEntryDate($billDate);
                                $em->persist($sim);
                                $simBill->addSim($sim);
                                $warehouse->addSim($sim);
                            }
                        }
                    }
                    if($deviceBill && $simBill){
                        $deviceBill->setDiscount($discount);
                        $simBill->setDiscount($discount);
                        $em->persist($deviceBill);
                        $em->persist($simBill);
                    }
                    else if($deviceBill){
                        $deviceBill->setDiscount($discount);
                        $em->persist($deviceBill);
                    } 
                    else if($simBill){
                        $simBill->setDiscount($discount);
                        $em->persist($simBill);
                    } 
                    $em->flush();
                }else{
                    $message[$fileName] = "Esta factura de compra ya fue registrada.";
                }

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
