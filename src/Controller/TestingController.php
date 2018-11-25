<?php

namespace App\Controller;

use App\Form\VFileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Employee;
use App\Entity\User;
use App\Entity\Address;
use App\Entity\Warehouse;
use App\Entity\Office;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\Argon2iPasswordEncoder;


class TestingController extends AbstractController
{

    public function index(Request $request)
    {
        $form = $this->get('form.factory');
        $formFiles = $form->createNamedBuilder("Files", VFileType::class, [])->getForm();
        $formFiles->handleRequest($request);
        if($formFiles->isSubmitted() && $formFiles->isValid()){
            var_dump($formFiles->getData());die;
        }
        return $this->render('credit_notes/index.html.twig',[
            'formFiles' => $formFiles->createView()
        ]);
    }
    
    public function home()
    {
        //$date = new \DateTime(null, new \DateTimeZone('America/New_York'));
        //var_dump($date);
        return $this->render('home.html.twig');
    }
    
    public function login()
    {
        return $this->render('login.html.twig');
    }
    
    public function generateUser(){
        $em = $this->getDoctrine()->getManager();
        $employee = $em->getRepository('App\Entity\Employee')->findOneBy(array('email' => "fer@vicsa.com"));
        var_dump($employee->getPassword());
    }
    
    public function generateData(){
        $em = $this->getDoctrine()->getManager();

        //---------------address------------------------
        $address = new Address();
        $address->setStreet("Violetas");
        $address->setExtNumber(100);
        $address->setState("Oaxaca");
        $address->setCity("Oaxaca de Juárez");
        $address->setColony("Reforma");
        $address->setTownship("Oaxaca de Juárez");
        $address->setPc(68000);
        $em->persist($address);

        //-------------------office---------------------

        $office = new Office();
        $office -> setAddress($address);
        $office -> setName("Vicsa Reforma");
        
        $em->persist($office);

        //-------------------Warehouse----------------------
        $warehouse = new Warehouse();
        $warehouse -> setOffice($office);
        $warehouse -> setQuantity(0);
        $warehouse -> setCost(0);
        $em->persist($warehouse);

        $em->flush();

  



    }
}
