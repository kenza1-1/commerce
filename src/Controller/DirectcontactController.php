<?php

namespace App\Controller;

use App\Form\DirectcontactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DirectcontactController extends AbstractController
{
    /**
     * @Route("/directcontact", name="directcontact")
     */
    public function index(Request $request,\Swift_Mailer $mailer)
    {
        $form = $this->CreateForm(DirectcontactType::class);
        $form->handleRequest($request); //analyse la requet http (les entrees dans les champs d'inscription)
        if($form->isSubmitted() && $form->isValid()){
            // $id=$request->query->get('id');
            $contactFormData = $form->getData();
            $message = (new \Swift_Message('message reçu du site de vélo '))// (swiftmessage)methode dans swifmailer
               ->setFrom($contactFormData['email'])
               ->setTo('iderkenza1@gmail.com')
               ->setBody(
                $this->renderView( //Pour chercher le fichier twig
                    'emails/directcontact.html.twig',[

                       'name' => $contactFormData['name'],
                       'email' => $contactFormData['email'],
                       'object' => $contactFormData['object'],
                       'message' => $contactFormData['message'],
                    //    'title' => $request->query->get('title'),
                    //    'price' => $request->query->get('price'),
                    //    'image' => $request->query->get('image'),
                    //    'description' =>$request->query->get('description')
                        ]),
                'text/html'
             )
                ;
                // on envoie le email
                $mailer->send($message);
                $this->addFlash(
                    'message',
                    '    Votre message a bien été envoyé !     '
                );
            // return $this->redirectToRoute('contact');

         }

        return $this->render('directcontact/index.html.twig', [
            'controller_name' => 'DirectcontactController',
            'form'=>$form->createView()

        ]);
    }
}
