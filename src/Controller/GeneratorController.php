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
                ->add('bill', FileType::class, array(
                    'label' => 'Factura',
                    'attr' => array(
                        'class' => 'form-control',
                        'accept' => '.pdf'
                    )
                ))
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
            try{
                $file = $form['bill']->getData();
                $file->move(
                        "Files", $file->getClientOriginalName()
                );
                $this->billName = $file->getClientOriginalName();
                $file = $form['contract']->getData();
                $file->move(
                        "Files", $file->getClientOriginalName()
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

    public function generarExcel()
    {
        ini_set('xdebug.var_display_max_data', '5000');
        //$file = 'C:\wamp64\www\tetris\public\test.pdf';
        $file = $this->get('kernel')->getProjectDir() . '/public/files/'. $this->contractName;
        $PDFParser = new Parser();
        $pdf = $PDFParser->parseFile($file);
        $text = $pdf->getText();
        $text = preg_replace('/\s{2,}|\t{1,}|\n/',' ',$text);
        

        $file = $this->get('kernel')->getProjectDir() . '/public/files/' . $this->billName;
        $pdf = $PDFParser->parseFile($file);
        $textNc = $pdf->getText();
        $textNc = preg_replace('/\s{2,}|\t{1,}|\n/',' ',$textNc);

        var_dump($text);
        var_dump($textNc);
        die;
        //-------------Precio Unitario-------------
        preg_match('/(?<=Costo Total: \$ ).* Pago Inicial/', $text, $matches);
        $matches = $matches[0];
        //var_dump($matches);
        $punit = $this->extraer($matches,'Pago');
        //var_dump($punit);



        //-------------plazo-------------
        preg_match('/(?<=Forzoso: ).* Plan Tarifario:/', $text, $matches);
        $matches = $matches[0];
        //var_dump($matches);
        $plazo = $this->extraer($matches,'Plan');
        //var_dump($plazo);


        //-------------email-------------
        preg_match('/(?<=Electrónico: ).* Teléfono Particular/', $text, $matches);
        $matches = $matches[0];
        //var_dump($matches);
        $email = $this->extraer($matches,'Teléfono');
        //var_dump($email);


        //-------------Estado-------------
        preg_match('/(?<=Federativa: ).* Delegación/', $text, $matches);
        $matches = $matches[0];
        //var_dump($matches);
        $estado = $this->extraer($matches,'Delegación');
        //var_dump($estado);


         //-------------Ciudad-------------
         preg_match('/(?<=Ciudad: ).* Entidad/', $text, $matches);
         $matches = $matches[0];
         //var_dump($matches);
         $ciudad = $this->extraer($matches,'Entidad');
         //var_dump($ciudad);


         //-------------C.P.-------------
         preg_match('/(?<=C.P.: ).* Ciudad/', $text, $matches);
         $matches = $matches[0];
         $cp = $this->extraer($matches,'Ciudad:');
         //var_dump($cp);


        //-------------Municipio-------------
        preg_match('/(?<=Municipio: ).* Correo Electrónico/', $text, $matches);
        $matches = $matches[0];
        $municipio = $this->extraer($matches,'Correo');
        //var_dump($municipio);

        //-------------Colonia-------------
        preg_match('/(?<=Colonia: ).* Número Interior/', $text, $matches);
        $matches = $matches[0];
        $colonia = $this->extraer($matches,'Número');
        //var_dump($colonia);
        


        //-----------Número Exterior----------
        preg_match('/(?<=Exterior: ).* Colonia/', $text, $matches);
        $matches = $matches[0];
        $numExt = $this->extraer($matches,'Colonia');
        //var_dump($numExt);


        //-----------Número Interior----------
        preg_match('/(?<=Interior: ).* Entre la Calle/', $text, $matches);
        $matches = $matches[0];
        $numInt = $this->extraer($matches,'Entre');
        //var_dump($numInt);


        //-------------Calle------------
        preg_match('/(?<=Calle: ).* Número Exterior/', $text, $matches);
        $matches = $matches[0];
        $calle = $this->extraer($matches,'Número');
        //var_dump($calle);


        //------------Nombre--------------
        preg_match('/(?<=Nombre: ).* Fecha de Nacimiento/', $text, $matches);
        $matches = $matches[0];
        $name = $this->extraer($matches,'Fecha');
        //var_dump($name);
  
        
        //---------------RFC----------------
        preg_match('/(?<=RFC: ).{13}/', $text, $matches);
        $rfc = $matches[0];
        //var_dump($matches);


        //-----------PRECIO SIN IVA----------------
        preg_match('/(?<=SUBTOTAL \$) .* DESCUENTO/', $textNc, $matches);
        $psi = $matches[0];
        $psi = trim($psi);
        $psi = explode(' ',$psi);
        $psi = $psi[0];
        //var_dump($psi);


        //------------------LÍNEA----------------
        preg_match('/(?<=Línea: )\d{10}/', $text, $matches);
        $linea = $matches[0];
        //var_dump($matches);


        //------------------IMEI----------------
        preg_match('/(?<=IMEI: )\d{15}/', $text, $matches);
        $imei = $matches[0];
        //var_dump($matches);


        //--------------DESCRIPCIÓN: en lastDesc   NÚMERO DE MATERIAL: en words[0]---------------
        preg_match('/(?<=IMPORTE ).* PZA/', $textNc, $matches);
        $desc = $matches[0];
        $words = explode(' ',$desc);
        //var_dump($matches);
        $lastDesc = '';
        for($i=1;$i<sizeof($words)-2;$i++){
            $lastDesc= $lastDesc.$words[$i].' ';
        }
        $lastDesc = trim($lastDesc);
        //var_dump($lastDesc);
        $numMaterial = $words[0];
        //var_dump($numMaterial);
        //var_dump($lastDesc);
        

        //preg_match($pattern, $text, $matches);

        //var_dump($matches);
        //print_r($text);
        

        //------------------GENERAR EXCEL-------------------------------
        $arrayData = [NULL, NULL, "MX07", NULL, $imei, $numMaterial, $lastDesc, NULL, NULL, NULL, NULL, $linea, "VICSA", NULL, NULL, $psi, $rfc, $name, NULL, $calle, $numInt, $numExt,$colonia, $municipio, $cp, $ciudad, $estado, $email, "M47", $plazo, NULL, $punit, NULL, NULL, NULL, NULL, "PUE", "01", "P01"];
        
        $spreadsheet = new Spreadsheet();
        
        /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
        $sheet = $spreadsheet->getActiveSheet()
        ->fromArray(
            $arrayData,
            NULL, 
            'A2'
        );
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
         $fileName = 'ACLARACIONES.xlsx';
         $publicDirectory = $this->get('kernel')->getProjectDir() . '/public';
         $excelFilepath =  $publicDirectory . '/aclaraciones.xlsx';
         
         // Create the excel file in the tmp directory of the system
         $writer->save($excelFilepath);
         
         // Return the excel file as an attachment
         return new Response("Excel generated succesfully");
    }



}
