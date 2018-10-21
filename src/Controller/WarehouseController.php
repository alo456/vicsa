<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Warehouse;


class WarehouseController extends AbstractController
{
    
    public function getAll()
    {
        $wh = $this->getDoctrine()->getRepository(Warehouse::class)->findAll();
        
        foreach($wh as $item){
            $ec= (string)$item->getId()." ".(string)$item->getQuantity()."\n";
            echo $ec;
        }
     
       
    if (!$wh) {
        throw $this->createNotFoundException(
            'No items found'
        );
    }

    return $this->render(
        'waiting',
        array('items' => $wh)
    );
       
      
        //$records = $em->getRepository("Entity\Warehouse")->findAll();
        
    }
    
    public function dbTest2(){
        $em = $this->getDoctrine()->getManager();
        $wr = new Warehouse();
        $wr->setQuantity(5);
        $wr->setCost(300);
       

        $em->persist($wr);
        $em->flush();
        return new Response('Saved new item with id '.$wr->getId());

    }
}
