<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Form\AnnoncesType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnnoncesController extends AbstractController
{
    /**
     * @Route("/admin/annonces/liste", name="annonces_liste")
     * @IsGranted("ROLE_ADMIN", message="No access! Get out!")
     */
    public function liste(ManagerRegistry $doctrine): Response
    {
        $annonce = $doctrine->getRepository(Annonces::class)->findAll();
        #Etape 4 : Rediriger vers une page ou afficher une page
        return $this->render("annonces/listAnnonces.html.twig", [
            'annonces' => $annonce
        ]);
    }
    /**
     * @Route("", name="annonces_home")
     */
    public function home(ManagerRegistry $doctrine): Response
    {
        $annonce = $doctrine->getRepository(Annonces::class)->findAll();
        #Etape 4 : Rediriger vers une page ou afficher une page
        return $this->render("home/index.html.twig", [
            'annonces' => $annonce
        ]);
    }
    /**
     * @Route("/deposer", name="annonces_deposer")
     */
    public function deposer(ManagerRegistry $doctrine, Request $request)
    {
        #Etape 1 : On créé un objet vide
        $annonces = new Annonces;

        #Etape 2 : on alimente l'objet avec des valeurs
        $annonces->setCreatedAt(new \DateTime());


        $formAnnonces = $this->createForm(AnnoncesType::class, $annonces);

        $formAnnonces->handleRequest($request);
        #On vérifie si le bouton submit a été cliqué et si c'est valide
        if ($formAnnonces->isSubmitted() && $formAnnonces->isValid()) {
            #Etape 4bis : Appeler l'entityManager de doctrine pour l'enregistrement
            $entityManager = $doctrine->getManager();
            $entityManager->persist($annonces);
            $entityManager->flush();

            #Créer un message flash
            $this->addFlash('add_success', "Votre annonce a été bien ajouté !");

            return $this->redirectToRoute('annonces_home');
        }

        #Etape 4 : Rediriger vers une page ou afficher une page
        return $this->render("annonces/formDeposer.html.twig", [
            'formAnnonces' => $formAnnonces->createView()
        ]);
    }
    /**
     * @Route("/annonces/show/{id}", name="annonces_show")
     */
    public function show($id, ManagerRegistry $doctrine)
    {
        $annonce = $doctrine->getRepository(Annonces::class)->find($id);

        return $this->render("annonces/show.html.twig", [
            'annonces' => $annonce
        ]);
    }
    /**
     * @Route("annonces/modifier/{id}", name="annonces_modifier")
     */
    public function modifier($id, ManagerRegistry $doctrine, Request $request)
    {
        // etape 1: on recupère l'objet qui a l'id : $id
        $annonces = $doctrine->getRepository(Annonces::class)->find($id);
        // etape 2 on modifie les valeurs souhaitées
        $annonces->setUpdatedAt(new \DateTime());
        $formAnnonces = $this->createForm(AnnoncesType::class, $annonces);
        $formAnnonces->handleRequest($request);
        if ($formAnnonces->isSubmitted() && $formAnnonces->isValid()) {

            // etape 3 on appel l'entitymanager de la doctrine pour enregistrer
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            // crer un message flash
            $this->addFlash('edit_success', "l'annonce a été bien modifié");
            return $this->redirectToRoute('annonces_liste');
        }

        // etape 4: Rediriger vers une page
        return $this->render('Annonces/formModifier.html.twig', ['formAnnonces' => $formAnnonces->createView()]);
    }
    /**
     * @Route("/annonces/delete/{id}", name="annonces_delete")
     */
    public function delete($id, ManagerRegistry $doctrine)
    {
        #Etape 1 : Récuperer l'objet qui a l'id : $id
        $annonces = $doctrine->getRepository(Annonces::class)->find($id);

        #Etape 2 : On appele l'entity manager de doctrine pour supprimer
        $entityManager = $doctrine->getManager();
        $entityManager->remove($annonces);
        $entityManager->flush();

        #Créer un message flash
        $this->addFlash('delete_success', "L'annonces a bien été supprimé !");

        #Etape 3 : Rediriger vers une page ou afficher une page
        return $this->redirectToRoute('annonces_liste');
    }
}
