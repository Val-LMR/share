<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Categorie;
use App\Form\ModifCategorieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CategorieRepository;
use App\Form\SuppCategorieType;


final class CategorieController extends AbstractController
{
    #[Route('/private-categories', name: 'app_categories')]     //On protege la route avec un private
    public function index(CategorieRepository $categorieRepository, Request $request, EntityManagerInterface $em): Response
    {
        $categories = $categorieRepository->findAll();
        $form = $this->createForm(SuppCategorieType::class, null, [
            'categories'=>$categories
        ]);
        if($request->isMethod('POST')){
            $form ->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $selectedCategories = $form->get('categories')->getData();
                foreach($selectedCategories as $categorie){
                    $em->remove($categorie);
                }
                $em->flush();
                $this->addFlash('notice', 'Catégories supprimées avec succés');
                return $this->redirectToRoute('app_categories');
            }

        }
        return $this->render('categorie/liste_categories.html.twig', [
            'categories' => $categories,
            'form'=>$form->createView(),
        ]);
    }

    #[Route('/private-modif-categorie/{id}', name: 'app_modif_categorie')]  
    public function modifCategorie(Categorie $categorie, Request $request, EntityManagerInterface $em): Response    //Gestionnaire de dépendance
    {
        $form = $this->createForm(ModifCategorieType::class, $categorie);
        if($request->isMethod('POST')){
            $form->handleRequest($request);     //Hydrate l'objet request
            if($form->isSubmitted() && $form->isValid()){
                $em->persist($categorie); // ICI : persit -> persist
                $em->flush();
                $this->addFlash('notice', 'Catégorie Modifiée');
                return $this->redirectToRoute('app_categorie'); // ICI : c'était "app_liste_categories" alors que la vraie route est "app_categorie"
            }
        }
        return $this->render('categorie/modif-categorie.html.twig', [
            'form'=>$form->createView(),
        ]);

        
    }

    #[Route('/private-suppr-categorie/{id}', name: 'app_suppr_categorie')]
    public function supprCategorie(Categorie $categorie, EntityManagerInterface $em): Response
    {
        if ($categorie != null) {
            $em->remove($categorie);
            $em->flush();
            $this->addFlash('notice', 'Catégorie supprimée');
        }
        return $this->redirectToRoute('app_categories');
    }





    
    /*
    #[Route('/supprMulti-categorie', name: 'app_suppr_multi_categorie', methods: ['POST'])]
    public function supprMultiCategories(Request $request, CategorieRepository $categorieRepository, EntityManagerInterface $em): Response  //Gestion de dépendance
    {
        $ids = $request->request->all('ids');   //Récup le tableau 'ids' envoyé par les cases name="ids[]"
        foreach ($ids as $id) {     //Boucle sur chaque id : $id prend successivement 3 puis 5 puis 8
            $categorie = $categorieRepository->find($id);       //Cherche en base la catégorie qui a cet id
            if ($categorie) {       //Si la catégorie est présente ou différent de null (!=)
                $em->remove($categorie);    //Alors on prepare la suppression
            }
        }
        $em->flush();       //Execute toutes les suppressions préparées
        $this->addFlash('notice', 'Catégories supprimées');     //Affiche un message quand l'action est faite

        return $this->redirectToRoute('app_categories');        //Redirige vers la route de la liste des catégories
    }*/


    
}   