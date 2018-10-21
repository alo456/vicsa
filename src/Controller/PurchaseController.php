<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PurchaseController extends AbstractController
{
    
     public function upload()
    {
        $inputFileName = './sampleData/example1.xls'; //por modificar
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $column =5; //por modificar
        $items=array();
        echo '<table>' . "\n";
for ($row = 1; $row <= $highestRow; ++$row) {
    
   
        $items[row-1]=$worksheet->getCell($column . $row)
                        ->getValue();
   
    
}
//tenemos todos los datos de una columna, se debe hacer por el numero de columnas
// que tengamos y se mandara a base;
        
    
    }
    
    
}
