<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    
    
}
