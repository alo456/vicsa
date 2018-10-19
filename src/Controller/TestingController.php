<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Address;
use Symfony\Component\HttpFoundation\Response;

class TestingController extends AbstractController
{
    public function index()
    {
        return $this->render('/inventory/inventory.html.twig');
    }
    
    public function test()
    {
        return $this->render('login.html.twig');
    }
    
    public function dbTest(){
        $em = $this->getDoctrine()->getManager();
        $address = new Address();
        $address->setStreet("Esmeralda");
        $address->setExtNumber(300);
        $address->setState("Oaxaca");
        $address->setCity("Oaxaca de Juárez");
        $address->setColony("Las peñas");
        $address->setMunicipality("Oaxaca de Juárez");
        $address->setPc(68150);

        $em->persist($address);
        $em->flush();
        return new Response('Saved new product with id '.$address->getId());

    }
    
}
