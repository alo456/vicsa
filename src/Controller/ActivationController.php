<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ActivationController extends AbstractController
{
    
    public function index()
    {
        return $this->render('activation/index.html.twig', [
            'controller_name' => 'ActivationController',
        ]);
    }
}
