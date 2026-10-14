<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Categorie;

final class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'app_categorie')]
    public function index(): Response
    {
        return $this->render('categorie/index.html.twig', [
            'controller_name' => 'CategorieController',
        ]);
    }

    #[Route('/modif_categorie/{id}', name: 'app_modif_categorie')]  
    public function modifCategorie(Categorie $categorie): Response
    {
        dd($categorie);
        return $this->render('categorie/modif-categorie.html.twig', [
            
        ]);
    }

    
}
