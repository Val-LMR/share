<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\CategorieType;
use App\Entity\Categorie;
use App\Entity\Fichier;
use App\Entity\User;
use App\Form\FichierType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

final class BaseController extends AbstractController
{
    #[Route('/', name: 'app_accueil')] //Route prend en param l'URL de la page ici la racine du projet puis le nom de la route
    public function index(): Response
    {
        return $this->render('base/index.html.twig', []);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, EntityManagerInterface $em): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class,$contact);
        if($request-> isMethod('POST')){
            $form->handleRequest($request);
            if($form -> isSubmitted() && $form -> isValid()){
                $contact -> setDateEnvoi(new \DateTimeImmutable());
                $em-> persist($contact);
                $em-> flush();
                $this-> addflash('notice', 'Message envoyé');
                return $this->redirectToRoute('app_contact');
            }
        }
        return $this->render('base/contact.html.twig', [
            'form'=>$form->createView(),
        ]);
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

    #[Route('/categorie/ajouter', name: 'app_categorie_ajouter')]
    public function ajouterCategorie(Request $request, EntityManagerInterface $em): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($categorie);
                $em->flush();
                $this->addFlash('notice', 'Catégorie ajoutée');
                return $this->redirectToRoute('app_categorie_ajouter');
            }
        }
        return $this->render('categorie/categorie.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/deposer', name: 'app_deposer')]
    public function deposer(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $fichier = new Fichier();
        $form = $this->createForm(FichierType::class, $fichier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fichierUploade = $form->get('fichier')->getData();

            // Nom unique : nom d'origine nettoyé + identifiant aléatoire + extension
            $nomOriginal = pathinfo($fichierUploade->getClientOriginalName(), PATHINFO_FILENAME);
            $nomSecurise = $slugger->slug($nomOriginal);
            $nomFinal = $nomSecurise . '-' . uniqid() . '.' . $fichierUploade->guessExtension();

            try {
                $fichierUploade->move($this->getParameter('uploads_directory'), $nomFinal);
            } catch (FileException $e) {
                $this->addFlash('notice', "Erreur lors de l'envoi du fichier");
                return $this->redirectToRoute('app_deposer');
            }

            $fichier->setNomFichier($nomFinal);
            $fichier->setDateDepot(new \DateTimeImmutable());

            $em->persist($fichier);
            $em->flush();

            $this->addFlash('notice', 'Fichier déposé');
            return $this->redirectToRoute('app_deposer');
        }

        return $this->render('base/deposer.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/profil', name: 'app_profil')]
    public function profil(): Response
    {
        return $this->render('base/profil.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}


