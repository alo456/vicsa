<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GeneratorController extends AbstractController {

    /**
     * @Route("/upload", name="upload")
     */
    public function index(Request $request) {
        $message = "";
        $form = $this->createFormBuilder([])
                ->add('bill', FileType::class, array(
                    'label' => 'Factura',
                    'attr' => array(
                        'class' => 'form-control',
                        'accept' => '.pdf'
                    )
                ))
                ->add('contract', FileType::class, array(
                    'label' => 'Contrato',
                    'attr' => array(
                        'class' => 'form-control',
                        'accept' => '.pdf'
                    )
                ))
                ->add('submit', SubmitType::class, array(
                    'label' => 'Guardar',
                    'attr' => array(
                        'class' => 'btn btn-primary px-4',
                        'form' => 'form'
                    )
                ))
                ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            try{
                $file = $form['bill']->getData();
                $file->move(
                        "Files", $file->getClientOriginalName()
                );
                $file = $form['contract']->getData();
                $file->move(
                        "Files", $file->getClientOriginalName()
                    );
                $message = "OK";
            } catch (Exception $ex) {
                $message = $ex->getMessage();
            }
            
            
        }
        return $this->render('generator/index.html.twig', [
                    'form' => $form->createView(),
                    'message' => $message
        ]);
    }

}
