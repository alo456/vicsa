<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Address;
use App\Entity\Office;
use App\Entity\Warehouse;
use App\Entity\DeviceBill;
use App\Entity\SimBill;
use App\Entity\Device;
use App\Entity\Sim;
use App\Entity\Employee;
use App\Entity\Activation;
use App\Entity\Contract;
use App\Entity\Client;


use Symfony\Component\HttpFoundation\Response;

class TestingController extends AbstractController
{
    public function index()
    {
        return $this->render('/inventory/inventory.html.twig');
    }
    
    public function test()
    {
        $date = new \DateTime(null, new \DateTimeZone('America/New_York'));
        var_dump($date);
        return $this->render('home.html.twig');
    }

    public function login()
    {
        return $this->render('login.html.twig');
    }
    
    public function dbTest(){
        $em = $this->getDoctrine()->getManager();
        $date = new \DateTime(null, new \DateTimeZone('America/New_York'));
        //var_dump($date); 
        //-------------------address------------------
         
        
        $address = new Address();
        $address->setStreet("Esmeralda");
        $address->setExtNumber(300);
        $address->setState("Oaxaca");
        $address->setCity("Oaxaca de Juárez");
        $address->setColony("Las peñas");
        $address->setTownship("Oaxaca de Juárez");
        $address->setPc(68150);

        $em->persist($address);

        //-------------------office---------------------

        $office = new Office();
        $office -> setAddress($address);
        $office -> setName("Vicsa Reforma");
        
        $em->persist($office);

        //-------------------Warehouse----------------------
        $warehouse = new Warehouse();
        $warehouse -> setOffice($office);
        $warehouse -> setQuantity(50);
        $warehouse -> setCost(5000);

        $em->persist($warehouse);

        //-----------------device_bill----------------------
        $dev_bill = new DeviceBill();
        $dev_bill->setDocNumber("1");
        $dev_bill->setBillDate($date);
        $dev_bill->setPaymentTerm("cash");
        $dev_bill->setQuantity(1500);
        $dev_bill->setDiscount(0.5);

        $em->persist($dev_bill);

        //-----------------sim_bill----------------------
        $sim_bill = new SimBill();
        $sim_bill->setDocNumber("2");
        $sim_bill->setBillDate($date);
        $sim_bill->setPaymentTerm("cash");
        $sim_bill->setQuantity(80);
        $sim_bill->setDiscount(0);

        $em->persist($sim_bill);

        //----------------device----------------------
        $device = new Device();
        $device -> setWarehouse($warehouse);
        $device -> setDeviceBill($dev_bill);
        $device -> setImei("HolaMundo");
        $device -> setMatKey("3");
        $device -> setDescription("Esto es una prueba de bd.");
        $device -> setPrice(500.75);
        $device -> setEntryDate($date);
        $device -> setExitDate($date);

        $em->persist($device);

        //--------------------SIM-------------------------
        $sim = new Sim();
        $sim -> setWarehouse($warehouse);
        $sim -> setSimBill($sim_bill);
        $sim -> setIccid("HolaMundoSim");
        $sim -> setMatKey("4");
        $sim -> setDescription("Esto es una prueba de bd2.");
        $sim -> setPrice(79.75);
        $sim -> setEntryDate($date);
        $sim -> setExitDate($date);

        $em ->persist($sim);

        //--------------------employee------------------------

        $employee = new Employee();
        $employee -> setName("Jorge Marco");
        $employee -> setSurname("Díaz");
        $employee -> setEmail("jorge@vicsa.com");
        $employee -> setJob("Asesor de Ventas");

        $em -> persist($employee);

        //------------------activation------------------------

        $activation = new Activation();
        $activation -> setDevice($device);
        $activation -> setSim($sim);
        $activation -> setLineNumber("9511234567");
        $activation -> setActDate($date);

        $em -> persist($activation);

        //------------------contract-------------------------
        $contract = new Contract();
        $client = $em->getRepository('App\Entity\Client')->findOneBy(array('rfc' => "LOLA840425HH8"));
        $contract -> setClient($client);
        $contract -> setEmployee($employee);
        $contract -> setActivation($activation);
        $contract -> setAccountNumber("Nocuenta");
        $contract -> setDeadlines(24);
        $contract -> setPlanName("Sin límite");

        $em -> persist($contract);
        
        $em->flush();
        return new Response('Base chida.');

    }
    
}
