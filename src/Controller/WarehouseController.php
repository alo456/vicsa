<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Warehouse;
use App\Entity\Device;
use App\Entity\DeviceBill;



class WarehouseController extends AbstractController
{
    
    public function index()
            
    {
        $message = "";
        $valorTotalWH = 0;        
        
        $wh = $this->getDoctrine()->getRepository(Warehouse::class)->findAll();
        if($wh){
            $costoNeto = $wh[0]->getCost();
            $devices = $wh[0]->getDevices();
            $sims = $wh[0]->getSims();
            $valorTotalWH = $wh[0]->getCost();
            $descriptions = [];
            //sacamos cada device del warehouse
            foreach ($devices as $dev) {
                    if(!$dev->getExitDate()){
                        $desc = $dev->getDescription();
                    if (isset($descriptions[$desc])){
                        $descriptions[$desc]['quantity'] ++;
                        $descriptions[$desc]['price'] += $dev->getPrice();
                    }
                    else{
                        $descriptions[$desc]['quantity'] = 1;
                        $descriptions[$desc]['price'] = $dev->getPrice();
                    }
                }       
            }

            //ahora sim
            foreach ($sims as $sim) {
                if(!$dev->getExitDate()){
                        $desc = $sim->getDescription();
                        if (isset($descriptions[$desc])){
                            $descriptions[$desc]['quantity'] ++;
                            $descriptions[$desc]['price'] += $sim->getPrice();
                        }
                        else{
                            $descriptions[$desc]['quantity'] = 1;
                            $descriptions[$desc]['price'] = $sim->getPrice();
                        }
                    }
                }   
        }

    return $this->render(
        'warehouse/index.html.twig',
        [
            'description' => $descriptions,
            'message' => $message,
            'totalwh' => $valorTotalWH
    
        ]);
    }
    

}
