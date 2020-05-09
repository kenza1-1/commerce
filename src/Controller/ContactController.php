<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request,\Swift_Mailer $mailer)
    {
         $form= $this->createForm(ContactType::class);
         $form->handleRequest($request); //analyse la requet http (les entrees dans les champs d'inscription)
         if($form->isSubmitted() && $form->isValid()){
            $contactFormData = $form->getData();
            $message = (new \Swift_Message('message reçu du site de vélo '))// (swiftmessage)methode dans swifmailer
               ->setFrom($contactFormData['email'])
               ->setTo('iderkenza1@gmail.com')
               ->setBody(
                '<html>' .
                ' <body>' .      
                'Message reçu de :'.
                $contactFormData['name'].
                '<br>'.
                '. Adresse mail est :'.
               $contactFormData['email'].
               '<br>'.
               'Objet :'.
               $contactFormData['object'].
               '<br>'.
               'Message :'.
               $contactFormData['message'].
                ' </body>' .
                '</html>',
                   'text/html'
             )
                ;
                // on envoie le email
                $mailer->send($message);
                $this->addFlash(
                    'message',
                    '    un mail de confirmation vous a bien été envoyé !     '
                );
            return $this->redirectToRoute('contact');

         }
        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'form'=>$form->createView()

        ]);
    }
}
