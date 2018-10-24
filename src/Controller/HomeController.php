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
        $qb->select(array('A')) // string 'u' is converted to array internally
               ->from('Activation', 'A')
                ->where('A.sim IS NULL')
                ->andwhere('A.device IS NULL');
        
        $act = $qb->getParameters();
        
        if (!$act) {
        throw $this->createNotFoundException(
            'No pending payments'
        );
    }

    return $this->render(
        'waiting',
        array('actv' => $act)
    );

        
    }
    
   
}
