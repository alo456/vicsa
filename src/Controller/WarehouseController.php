<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Warehouse;
use App\Entity\Device;
use App\Entity\DeviceBill;



class WarehouseController extends AbstractController
{
    
    public function getAll()
            
    {
        $id=1;
        $cantidad=[];
        $descripcion=[];
        $importe=[];
       $valorTotal=[];
                
        
        $wh = $this->getDoctrine()->getRepository(Warehouse::class)->findOneBy(array('id' => $id));
        if (!$wh) {
        return new Response("No hay items en el almacen");
        }
        //echo $wh->getId();
        
        
        $costoNeto=$wh->getCost();
        $devices= $wh->getDevices();
        $sims= $wh->getDevices();
        
        $d_desc=[];
        $d_imp=[];
        $c=0;
        $i_q=1;
        //sacamos cada device del warehouse
         foreach($devices as $dev){
              $d_desc[]=$dev->getDescription();
              $d_imp[]=$dev->getPrice();
             $c++;
         }
         //echo $c;
       
         //sumamos los iguales de devices
          for($i=0;$i<$c;$i++){

               
              for($j=$i+1;$j<$c;$j++){
                  //echo "comaprando".$d_desc[$i]." con ". $d_desc[$j]."                            ";
                  if($d_desc[$i]=="v"){break;}
                  if($d_desc[$i]==$d_desc[$j]){
                      
                      $d_desc[$j]="v";
                      $i_q++;
                  }
              }
              if($d_desc[$i]!="v"){
               $descripcion[]=$d_desc[$i];
               $valorTotal[]=$d_imp[$i]*$i_q;
               $importe[]=$d_imp[$i];
                $cantidad[]=$i_q;
              }
              //echo "suma de un device".$i_q;
              $i_q=1;
          }
         $ini=$c;
        
          
          //ahora sim
           foreach($sims as $sm){
              $d_desc[]=$sm->getDescription();
              $d_imp[]=$sm->getPrice();
             $c++;
         }
         
         //sumamos los iguales de sim
          for($i=$ini;$i<$c;$i++){

               
              for($j=$i+1;$j<$c;$j++){
                  if($d_desc[$i]=="v"){break;}
                  if($d_desc[$i]==$d_desc[$j]){
                      $d_desc[$j]="v";
                      $i_q++;
                  }
              }
              if($d_desc[$i]!="v"){
               $descripcion[]=$d_desc[$i];
               $valorTotal[]=$d_imp[$i]*$i_q;
               $importe[]=$d_imp[$i];
                $cantidad[]=$i_q;
              
              }
              $i_q=0;
          }
        
       
     
       //echo $descripcion[0]." ". $valorTotal[0]." ". $importe[0]." ".$cantidad[0]."\n";
       //echo $descripcion[1]." ". $valorTotal[1]." ". $importe[1]." ".$cantidad[1]."\n";
    

    return $this->render(
        'waiting',
        [
          'cantidad' => $cantidad,
            'descripcion' => $descripcion,
            'importe'=> $importe,
            'valorTotal' =>$valorTotal
    
        ]);
       
      
        //$records = $em->getRepository("Entity\Warehouse")->findAll();
        return new Response('Done');
    }
    
  
    
    public function dbTest2(){
        $em = $this->getDoctrine()->getManager();
        $wr = new Warehouse();
        $wr->setQuantity(3);
        $wr->setCost(300);
        
        $dev1=new Device();
        $dev1->setImei(123456789012345);
        $dev1->setMatKey(1);
        $dev1->setDescription("Samsung");
        $dev1->setPrice(50);
        $dev1->setWarehouse($wr);
        
        $dev2=new Device();
        $dev2->setImei(123456789012347);
        $dev2->setMatKey(2);
        $dev2->setDescription("Samsung");
        $dev2->setPrice(50);
        $dev2->setWarehouse($wr);
        
        $dev3=new Device();
        $dev3->setImei(123456789012385);
        $dev3->setMatKey(3);
        $dev3->setDescription("Samsung plus");
        $dev3->setPrice(200);
        $dev3->setWarehouse($wr);
        
        $wr->addDevice($dev1);
        $wr->addDevice($dev2);
        $wr->addDevice($dev3);
        
        
        
        $dvb=new DeviceBill();
       

        $em->persist($wr);
         $em->persist($dev1);
         $em->persist($dev2);
          $em->persist($dev3);
        $em->flush();
        return new Response('Saved new item with id '.$wr->getId());

    }
}
