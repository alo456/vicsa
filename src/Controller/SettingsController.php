<?php

namespace App\Controller;

use App\Form\VFileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Employee;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\Argon2iPasswordEncoder;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class SettingsController extends AbstractController
{
    public function index(Request $request, UserPasswordEncoderInterface $encoder){
        $message = '';
        $formPassword = $this->createFormBuilder([])
        ->add('oldpassword', PasswordType::class, array('label' => 'Contraseña Actual',
            'attr' => array(
                'class' => 'form-control',
                'placeholder' => 'Contraseña'
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
                    'placeholder' => 'Repite la Contraseña'
                ))
        ))
        ->add('submit', SubmitType::class, array('label' => 'Guardar',
            'attr' => array(
                'class' => 'btn btn-primary'
            )
        ))
        ->getForm();
        $formPassword->handleRequest($request);

        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $admin = $em->getRepository('App\Entity\Employee')->findOneBy(array('email' => "fer@vicsa.com"));
            $data = $formPassword->getData();
            $oldpassword = $data['oldpassword'];
            $newpassword = $data['newpassword'];
            if($oldpassword != $newpassword){
                $newpassword = $encoder->encodePassword($admin, $newpassword);

                if($encoder->isPasswordValid($admin,$oldpassword)){
                    $admin->setPassword($newpassword);
        //            $errors = $validator->validate($user);
                    $errors = 0;
                    if ($errors > 0) {
                       $message = "La contraseña no cumple los requisitos";
                    } else {
                        $em->flush();
                        $message = "Contraseña actualizada";
                    }
                }else{
                    $message = "Contraseña incorrecta";
                }


            }
            else{
                $message = "La nueva contraseña es la misma que la anterior";
            }
            
            
           
        }
        return $this->render('settings/index.html.twig', array(
            'formPassword' => $formPassword->createView(),
            'message' => $message
        ));
    }
}
