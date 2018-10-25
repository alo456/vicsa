<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class HomeController extends AbstractController
{
    
    public function init()
    {
        $qb = $em->createQueryBuilder();
        $qb->select(array('A')) // string 'A' is converted to array internally
               ->from('Activation', 'A')
               ->where('A.note IS NULL');
        
        $act = $qb->getParameters();
        
        if (!$act) {
        return $this->render(
        'waiting',
        'No existen pendientes'
    );
    }

    return $this->render(
        'waiting',
        array('actv' => $act)
    );

        
    }
    
   
}
