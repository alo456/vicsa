<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class HomeController extends AbstractController
{

    public function init()
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $message = '';
        $pends = [];
        $sales = [];
        $purchases = [];
        for($i=0;$i<8;$i++){
            $sales[$i] = 0;
            $purchases[$i] = 0;
        } 
        $act = $em->getRepository('App\Entity\Activation')->findBy(array('note' => null));

        $monday = date( 'Y-m-d', strtotime( 'monday this week'));
        $sunday = date( 'Y-m-d', strtotime( 'sunday this week'));
        
        $salesWeek = $qb
        ->select ('IDENTITY(a.device), a.actDate')
        ->where('a.actDate > :monday')
        ->andWhere('a.actDate < :sunday')
        ->setParameter('monday', $monday)
        ->setParameter('sunday', $sunday)
        ->from('App\Entity\Activation'::class, 'a')
        ->getQuery()
        ->getResult();

        $qb = $em->createQueryBuilder();

        $devicePurchasesWeek = $qb
        ->select ('d.price, d.entryDate')
        ->where('d.entryDate > :monday')
        ->andWhere('d.entryDate < :sunday')
        ->setParameter('monday', $monday)
        ->setParameter('sunday', $sunday)
        ->from('App\Entity\Device'::class, 'd')
        ->getQuery()
        ->getResult();

            $qb = $em->createQueryBuilder();

        $simPurchasesWeek[] = $qb
        ->select ('s.price, s.entryDate')
        ->where('s.entryDate > :monday')
        ->andWhere('s.entryDate < :sunday')
        ->setParameter('monday', $monday)
        ->setParameter('sunday', $sunday)
        ->from('App\Entity\Sim'::class, 's')
        ->getQuery()
        ->getResult();


        if (!$act) {
            $message = "No hay pendientes.";
        }
        else{
            foreach($act as $pend){
                $pends[$pend->getLineNumber()] = array(
                    $pend->getDevice()->getDescription(), 
                    $pend->getDevice()->getPrice(),
                    $pend->getContract()->getAccountNumber(),
                    $pend->getContract()->getPlanName(),
                    $pend->getActDate()->format('Y-m-d H:i:s')
                );
                
                
            }
        }

        foreach($salesWeek as $sale){
            //var_dump($sale['actDate']);
            $day =  $sale['actDate']->format('Y-m-d');
            $day = date('N', strtotime($day)); //------------número del día 1(lunes), 7(domingo)
            $sales[$day] += $pend->getDevice()->getPrice();
        }

        foreach($devicePurchasesWeek as $purchase){
            $day =  $purchase['entryDate'];
            $day =$day->format('Y-m-d');
            $day = date('N', strtotime($day)); //------------número del día 1(lunes), 7(domingo)
            $purchases[$day] += $purchase['price'];
        }

        foreach($simPurchasesWeek as $purchase){
            $day =  $purchase[0]['entryDate'];
            $day =$day->format('Y-m-d');
            $day = date('N', strtotime($day)); //------------número del día 1(lunes), 7(domingo)
            $purchases[$day] += $purchase[0]['price'];
        }

        //var_dump($purchases);


        return $this->render('home/home.html.twig',[
            //'form' => $form->createView(),
            'message' => $message,
            'pends' => $pends,
            'sales' => $sales,
            'purchases' => $purchases
        ]);

        
    }

    public function test(){
        
    }
    
   
}
