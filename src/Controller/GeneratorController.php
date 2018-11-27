<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Smalot\PdfParser\Parser;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GeneratorController extends Controller {

    private $billName = '';
    private $contractName = '';
    public function index(Request $request) {
        $message = "";
        $form = $this->createFormBuilder([])
                /*
                ->add('bill', FileType::class, array(
                    'label' => 'Factura',
                    'attr' => array(
                        'class' => 'form-control',
                        'accept' => '.pdf'
                    )
                ))*/
                ->add('contract', FileType::class, array(
                    'label' => 'Contrato',
                    'attr' => array(
                        'class' => 'form-control',
                        'accept' => '.pdf'
                    )
                ))
                ->add('submit', SubmitType::class, array(
                    'label' => 'Guardar',
                    'attr' => array(
                        'class' => 'btn btn-primary px-4',
                        'form' => 'form'
                    )
                ))
                ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $directory = $this->get('kernel')->getProjectDir() . '/Contracts';
            try{
                /*$file = $form['bill']->getData();
                $file->move(
                        $directory, $file->getClientOriginalName()
                );
                $this->billName = $file->getClientOriginalName();*/            
                $file = $form['contract']->getData();
                $file->move(
                    $directory, $file->getClientOriginalName()
                );
                $this->contractName = $file->getClientOriginalName();
                $message = "OK";
                $this->generarExcel();
            } catch (Exception $ex) {
                $message = $ex->getMessage();
            }
            
            
        }
        return $this->render('generator/index.html.twig', [
                    'form' => $form->createView(),
                    'message' => $message
        ]);
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


    public function generateExcel(Request $contracts){
        if($contracts->isMethod('get')){
            $fileName = 'aclaraciones.xlsx';
            $publicDirectory = $this->get('kernel')->getProjectDir() . '/public/Reports';
            $excelFilepath = $publicDirectory . '/' . $fileName;
            return $this->file($excelFilepath, $fileName , ResponseHeaderBag::DISPOSITION_INLINE);
        }
        $em = $this->getDoctrine()->getManager();
        //$contracts = ['F-74064258', 'F-74064257'];
        $contracts = $contracts->request->get('accounts');
        $i = 0; 
        //-------------------------------looking for excel info into each device's IMEI---------------
        foreach($contracts as $contractNumber){
            $contract = $em->getRepository('App\Entity\Contract')->findOneBy(array('accountNumber' => $contractNumber));
            $client = $contract->getClient();
            $address = $client->getAddress();
            $activation = $contract->getActivation();
            $device = $activation->getDevice();
            $sim = $activation -> getSim();
            
            $punit = $contract->getTotalPrice();
            $deadlines = $contract->getDeadlines();
            
            $email = $client->getEmail();

            $state = $address->getState();
            $city = $address->getCity();
            $pc = $address->getPc();
            $township = $address->getTownship();
            $colony = $address->getColony();
            $extNum = $address->getExtNumber();
            $inNum = $address->getInNumber();
            $street = $address->getStreet();
            
            $name = $client->getSurname().' '.$client->getName();
            $rfc = $client->getRfc();

            $line = $activation->getLineNumber();

            $imei = $device->getImei();
            $iccid = $sim->getIccid();

            $psi = $device->getPrice();
            $description = $device->getDescription();
            $matNum = $device->getMatKey();


            //------------------GENERATE EXCEL-------------------------------
            $arrayData[$i++] = [NULL, NULL, "MX07", NULL, $imei, $matNum, $description, NULL, NULL, NULL, NULL, $line, "VICSA", NULL, NULL, $psi, $rfc, $name, NULL, $street, $inNum, $extNum,$colony, $township, $pc, $city, $state, $email, "M47", $deadlines, NULL, $punit, NULL, NULL, NULL, NULL, "PUE", "01", "P01"];
        }
        
        $spreadsheet = new Spreadsheet();
        
        /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
        $sheet = $spreadsheet->getActiveSheet()
        ->fromArray(
            $arrayData,
            NULL, 
            'A2'
        );

        for($j=0;$j<$i;$j++){
            $idx = $j+2;
            $sheet->setCellValueExplicit(
                'E'.$idx,
                $arrayData[$j][4],
                \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
            );
        }


        $sheet->setTitle("REVISION");

        $sheet->setCellValue('A1', 'No.');
        $sheet->setCellValue('B1', 'No.');
        $sheet->setCellValue('C1', 'Region');
        $sheet->setCellValue('D1', "Fecha del Proceso");
        $sheet->setCellValue('E1', "Numero de Serie (IMEI)");
        $sheet->setCellValue('F1', "Numero de Material");
        $sheet->setCellValue('G1', "Descripcion de Material");
        $sheet->setCellValue('H1', "Tipo de Error");
        $sheet->setCellValue('I1', "Observaciones");
        $sheet->setCellValue('J1', "Archivo Origen");
        $sheet->setCellValue('K1', "Posicion en el Doc.");
        $sheet->setCellValue('L1', "Linea");
        $sheet->setCellValue('M1', "Fuerza de Venta");
        $sheet->setCellValue('N1', "Fecha de Activacion");
        $sheet->setCellValue('O1', "Fecha de Envio");
        $sheet->setCellValue('P1', "Precio (Sin IVA)");
        $sheet->setCellValue('Q1', "RFC");
        $sheet->setCellValue('R1', "Nombre 1");
        $sheet->setCellValue('S1', "Nombre 2");
        $sheet->setCellValue('T1', "Calle");
        $sheet->setCellValue('U1', "Número Interior");
        $sheet->setCellValue('V1', "Número Exterior");
        $sheet->setCellValue('W1', "Colonia");
        $sheet->setCellValue('X1', "Delegacion/Municipio");
        $sheet->setCellValue('Y1', "C.P.");
        $sheet->setCellValue('Z1', "Ciudad");
        $sheet->setCellValue('AA1', "Estado");
        $sheet->setCellValue('AB1', "e-Mail");
        $sheet->setCellValue('AC1', "Motivo de pedido");
        $sheet->setCellValue('AD1', "Meses de plazo");
        $sheet->setCellValue('AE1', "Clave de condiciones de pago");
        $sheet->setCellValue('AF1', "Precio Unitario");
        $sheet->setCellValue('AG1', "Bloqueo DT03");
        $sheet->setCellValue('AH1', "TIPO RFC");
        $sheet->setCellValue('AI1', "ENGANCHE");
        $sheet->setCellValue('AJ1', "CARGO FINAN");
        $sheet->setCellValue('AK1', "CveMetPago");
        $sheet->setCellValue('AL1', "FormaPago");
        $sheet->setCellValue('AM1', "CveUsoCFDI");
        $sheet->setCellValue('AN1', "CTA SAP");
        $sheet->setCellValue('AO1', "MATERIAL");
        $sheet->setCellValue('AP1', "FACTURA ORIGEN");
        $sheet->setCellValue('AQ1', "MONTO");
        $sheet->setCellValue('AR1', "COMENTARIO");
        $sheet->setCellValue('AS1', "COMENTARIO1");
        $sheet->setCellValue('AT1', "NOTA CREDITO");
        $sheet->setCellValue('AU1', "IMPORTE S/ IVA");
        $sheet->setCellValue('AV1', "CTA SAP");
        $sheet->setCellValue('AW1', "FACTURA FINAL");
        $sheet->setCellValue('AX1', "IMPORTE C/ IVA");
        $sheet->setCellValue('AY1', "POLIZA");
        $sheet->setCellValue('AZ1', "");
        $sheet->setCellValue('BA1', "");
        $sheet->setCellValue('BB1', "");
        $sheet->setCellValue('BC1', "");
        $sheet->setCellValue('BD1', "");
        $sheet->setCellValue('BE1', "");
        $sheet->setCellValue('BF1', "");
        $sheet->setCellValue('BG1', "");
        $sheet->setCellValue('BH1', "");
        $sheet->setCellValue('BI1', "");
        $sheet->setCellValue('BJ1', "");
        $sheet->setCellValue('BK1', "");
        $sheet->setCellValue('BL1', "");
        $sheet->setCellValue('BM1', "DIFERENCIAS");

        

         // Create your Office 2007 Excel (XLSX Format)
         $writer = new Xlsx($spreadsheet);
        
         // Create a Temporary file in the system
         $fileName = 'aclaraciones.xlsx';
         $publicDirectory = $this->get('kernel')->getProjectDir() . '/public/Reports';
         $excelFilepath =  $publicDirectory.'/'.$fileName;
         
         // Create the excel file in the tmp directory of the system
         $writer->save($excelFilepath);
         
         // Return the excel file as an attachment
         $response = new BinaryFileResponse($excelFilepath);
         $response->headers->set(
            'Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $fileName
        );
        return new JsonResponse("OK"
        );
         //return $this->file($excelFilepath, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
         

    }


}
