<?php

namespace App\Controller;

use App\Form\VFileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TestingController extends AbstractController
{
    public function index(Request $request)
    {
        $form = $this->get('form.factory');
        $formFiles = $form->createNamedBuilder("Files", VFileType::class, [])->getForm();
        $formFiles->handleRequest($request);
        return $this->render('purchases/index.html.twig',[
            'formFiles' => $formFiles->createView()
        ]);
    }
    
    public function home()
    {
        return $this->render('home.html.twig');
    }
    
    public function login()
    {
        return $this->render('login.html.twig');
    }
    
    
}
