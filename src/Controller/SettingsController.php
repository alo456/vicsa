<?php

namespace App\Controller;

use App\Form\VFileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Employee;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\Argon2iPasswordEncoder;


class SettingsController extends AbstractController
{
    public function index(){
        $formPassword = $this->createFormBuilder([])
        ->add('oldpassword', PasswordType::class, array('label' => 'Contraseña Actual',
            'attr' => array(
                'class' => 'form-control',
                'placeholder' => 'Contraseña Actual'
            )
        ))
        ->add('newpassword', RepeatedType::class, array(
            'type' => PasswordType::class,
            'first_options' => array('label' => 'Nueva Contraseña', 'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Nueva Contraseña'
                )),
            'second_options' => array('label' => 'Confirmación de Contraseña', 'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Confirmación de Contraseña'
                ))
        ))
        ->add('submit', SubmitType::class, array('label' => 'Guardar',
            'attr' => array(
                'class' => 'btn btn-primary px-4'
            )
        ))
        ->getForm();
        $formPassword->handleRequest($request);

        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            $data = $formPassword->getData();
            $body = $this->APICall($data, "changePassword", $cookie);
            if ($body->status == 'OK') {
                $message .= "Cambios realizados";
            } else {
                $message .= $body->message;
            }
        }
        return $this->render('settings/index.html.twig');
    }
}
