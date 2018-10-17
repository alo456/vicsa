<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestingController extends AbstractController
{
    /**
     * @Route("/testing", name="testing")
     */
    public function index()
    {
        return $this->render('admin/home.html.twig');
    }
    /**
     * @Route("/testing2", name="testing2")
     */
    public function test()
    {
        return $this->render('admin/login.html.twig');
    }
}
