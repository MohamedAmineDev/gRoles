<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthenteficationController extends AbstractController
{
    /**
     * @Route("/authentefication", name="authentefication")
     */
    public function index(): Response
    {
        return $this->render('authentefication/index.html.twig', [
            'controller_name' => 'AuthenteficationController',
        ]);
    }


    /**
     * @Route("/connexion",name="app_login")
     */
    public function login()
    {
        return $this->render('authentefication/login.html.twig');
    }

    /**
     * @Route("/deconnexion",name="app_logout")
     */
    public function logout(){
        
    }
}
