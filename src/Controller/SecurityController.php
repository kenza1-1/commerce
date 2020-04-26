<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPassType;
use App\Form\RegistrationType;
use App\Form\ChangePasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

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
          //On genere le token d'activation
            $user->setActivationToken(md5(uniqid()));
            $manager->persist($user);
            $manager->flush($user);
           // on cree le message 
            $message = (new \Swift_Message('Activation de votre compte'))// (swiftmessage)methode dans swifmailer
            // On attribue l'exépiditeur
            ->setFrom('iderkenza1@gmail.com')
            //On attribue le destinataire 
                ->setTo($user->getEmail())
                // On cree le contenu 
                ->setBody(
                    $this->renderView( //Pour chercher le fichier twig
                        'emails/activation.html.twig',['token'=> $user->getActivationToken()] //On recupere notre token
                    ),
                    'text/html'
                )
                ;
                // on envoie le email
                $mailer->send($message);
                $this->addFlash(
                    'message',
                    '    un mail de confirmation vous a bien été envoyé !     '
                );
             return $this->redirectToRoute('security_login');
        }
        return $this->render('security/registration.html.twig', [
            'controller_name' => 'SecurityController',
            'form'=>$form->createView()
        ]);
    }
      /**
     * @Route("/rénitialisation", name="security_reset")
     */
    public function resetpass(Request $request,UserRepository $repo,EntityManagerInterface $manager, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenrator){
        $form= $this->createForm(ResetPassType::class);//On va creer un formilaire et cherche le formulaire ResetPassTye
        $form->handleRequest($request); //analyse la requet http (entré dans le chmps e-mail)(traitemenr du formulaire)
        if($form->isSubmitted() && $form->isValid()){ //si le fomrulaire est soumet et les champs son valide
            $donnee = $form->getData();
            $user = $repo->findOneBy(['email'=> $donnee]);
            if(!$user){
                //On envoie un message flash
                $this->addFlash('danger', 'Cette adresse e-mail n\'existe pas');
                //Redirection vers la page connexion
                return $this->redirectToRoute('security_login');
            }
            //Quand l'email existe on génère un token 
            // TokenGeneratorInterface: Interface de génaration de token(integrée a Symfony)
            $token =$tokenGenrator->generateToken();
            //On va essayer d'ecrire dans la base de donnée 
            try{
                $user->setResetToken($token);
                $manager->persist($user);
                $manager->flush($user);
            }catch(\Exception $e){
                $this->addFlash('warning','Une erreur est servenue : '. $e->getMessage());
                 return $this->redirectToRoute('security_login');           
            }
            // on cree le message 
            $message = (new \Swift_Message('rénitialiser votre mot de passe'))// (swiftmessage)methode dans swifmailer
            // On attribue l'exépiditeur
            ->setFrom('iderkenza1@gmail.com')
            //On attribue le destinataire 
                ->setTo($user->getEmail())
                // On cree le contenu 
                ->setBody(
                    $this->renderView( //on cherche le fichier twig
                        'emails/reset.html.twig',['token'=> $user->getResetToken()] //On recupere notre token et on le passe au fichier twig
                    ),
                    'text/html'
                )
                ;
                // on envoie le email
                $mailer->send($message);
                $this->addFlash(
                    'message',
                    '    Un mail de rénitialisation du mot de passe vous a bien été envoyé !     '
                );
             return $this->redirectToRoute('security_login');
           
        }
        return $this->render('security/resetpassword.html.twig', [
            'form'=>$form->createView() //crée la vue du formulaire
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
        //On vérifie si un utilisateur a ce token 
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
   /**
     * @Route("/modifier/{token}", name="resetPassWord")
     */
    public function resetPassWord($token,Request $request,UserPasswordEncoderInterface $encoder, UserRepository $repo,EntityManagerInterface $manager){ //La methode qui va nous rénitialisé le mot de passe
         //On vérifie si un utilisateur a ce token 

         $user = $repo->findOneBy(['Reset_token' => $token]);
         $form = $this->createForm(ChangePasswordType::class,$user);
         $form->handleRequest($request); //analyse la requet http (les entrees dans les champs d'inscription)
         if(!$user){
           $this->addFlash('danger','Token inconnu !');
           return $this->redirectToRoute('security_login');
         }
        if($form->isSubmitted() && $form->isValid()){ //si le fomrulaire est soumet et les champs son valide
         //On supprime le token
            $user->setResetToken(null);
            //On chiffre le mot de passe
            // $hash = $encoder->encodePassword($user, $user->getPassword());
            $hash = $encoder->encodePassword($user, $user->getPassword());
           
            $user->setPassword($hash);
            //On enregistre le noveau mot de passe
            $manager->persist($user);
            $manager->flush($user);
            $this->addFlash('success','Votre mot de passe a bien été modifié!');
            return $this->redirectToRoute('security_login');

        }
          
         return $this->render('security/changepassword.html.twig', [
            'form'=>$form->createView() //crée la vue du formulaire
        ]);
    }

    
}
