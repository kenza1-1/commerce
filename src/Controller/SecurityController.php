<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request,EntityManagerInterface $manager,UserPasswordEncoderInterface $encoder,\Swift_Mailer $mailer)
    {
        $user = new User;
        $form= $this->createForm(RegistrationType::class , $user);
        $form->handleRequest($request); //analyse la requet http (les entrees dans les champs d'inscription)
        if($form->isSubmitted() && $form->isValid()){ //si le fomrulaire est soumet et les champs son valide
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user->setActivationToken(md5(uniqid()));
          //On genere le token d'activation

            $manager->persist($user);
            $manager->flush($user);
        
            // return $this->redirectToRoute('security_login');
           // on cree le message 
            $message = (new \Swift_Message('Activation de votre compte'))// (swiftmessage)methode dans swifmailer
            // On attribue l'exépiditeur
            ->setFrom('iderkenza1@gmail.com')
            //On attribue le destinataire 
                ->setTo($user->getEmail())
                // On cree le contenu 
                ->setBody(
                    $this->renderView(
                        'emails/activation.html.twig',['token'=> $user->getActivationToken()]
                    ),
                    'text/html'
                )
                ;
                // on envoie le email
                $mailer->send($message);

                $this->addFlash(
                    'message',
                    '    un mail vous a été envoyé !     '
                );

             return $this->redirectToRoute('security_login');

                

        }

        return $this->render('security/registration.html.twig', [
            'controller_name' => 'SecurityController',
            'form'=>$form->createView()
        ]);
    }
      /**
     * @Route("/connexion", name="security_login")
     */
    public function login(){
        return $this->render('security/login.html.twig', [
            
        ]);
        return $this->redirectToRoute('produits');


    }
     /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout(){

    }
       /**
     * @Route("/activation/{token}", name="activation")
     */
    public function activation($token, UserRepository $repo,EntityManagerInterface $manager){
        //On verfie si un utilisateur a ce token 
        $user = $repo->findOneBy(['activation_token' => $token]);

        //si aucun utilisateur existe avec ce token
        if(!$user){
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        
        }
        //On supprime le token
        $user->setActivationToken(null);
        $manager->persist($user);
        $manager->flush($user);
        // On envoi un message flash
        $this->addFlash(
            'notice',
            '           Votre compte est maintenant activé !            '
        );
        //On retourne a l'acceuil 
        return $this->redirectToRoute('produits');



    }
    
}
