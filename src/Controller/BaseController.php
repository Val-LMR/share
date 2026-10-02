<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BaseController extends AbstractController
{
    #[Route('/', name: 'app_accueil')] //Route prend en param l'URL de la page ici la racine du projet puis le nom de la route
    public function index(): Response
    {
        return $this->render('base/index.html.twig', []);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('base/contact.html.twig');
    }

    #[Route('/A-propos', name: 'app_apropos')]
    public function apropos(): Response
    {
        return $this->render('base/Apropos.html.twig');
    }

    #[Route('/mentions-legales', name: 'app_ml')]
    public function mentionslegales(): Response
    {
        return $this->render('base/ml.html.twig');
    }
    
}