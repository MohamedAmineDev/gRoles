<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Form\SignInType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/registration", name="registration")
     */
    public function index(): Response
    {
        return $this->render('registration/index.html.twig', [
            'controller_name' => 'RegistrationController',
        ]);
    }


    /**
     * @Route("/inscription",name="app_signin")
     */
    public function signin(Request $request,UserPasswordEncoderInterface $encoder,EntityManagerInterface $manager,TokenGeneratorInterface $tokenGenerator,MailerInterface $mailer):Response
    {
        $user = new User();
        $form=$this->createForm(SignInType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $encodedPassword=$encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($encodedPassword);
            $token=$tokenGenerator->generateToken();
            $user->setConfirmationToken($token);
            $user->setRoles([$user->getRole()]);
            $user->setCreatedAt(new DateTime());
            $manager->persist($user);
            $manager->flush();
            // envoi de mail 
            $mail=(new TemplatedEmail())
            ->from('webSite@gmail.com')
            ->to($user->getEmail())
            ->subject('Thanks for signing up!')
        
            // path of the Twig template to render
            ->htmlTemplate('emails/singup.html.twig')
        
            // pass variables (name => value) to the template
            ->context([
                'expiration_date' => new \DateTime('+7 days'),
                'username' => $user->getUsername(),
                'token'=>$token
            ])
            ;
            $mailer->send($mail);
            $this->addFlash('message','You account is created !');
            return $this->redirectToRoute('registration');
        }
        return $this->render('registration/signin.html.twig',[
            'form'=>$form->createView()
        ]);
    }


    /**
     * @Route("/activate/{token}",name="app_activete_account")
     */
    public function activateAccount(UserRepository $userRepository,$token,EntityManagerInterface $manager):Response
    {
        if($token){
            $user=$userRepository->findOneBy(['confirmationToken'=>$token]);
            if($user){
                $user->setConfirmationToken(null);
                $manager->flush();
                #$this->redirectToRoute('app_login');
                $this->redirectToRoute('registration');

            }
        }
        return $this->redirectToRoute('registration');
    }
}
