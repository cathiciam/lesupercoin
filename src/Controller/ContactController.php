<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="app_contact")
     */
    public function index(Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine): Response
    {
        $contact = new Contact();
        $contact->setCreatedAt(new \DateTime());
        $formContact = $this->createForm(ContactType::class, $contact);
        $formContact->handleRequest($request);

        if ($formContact->isSubmitted() && $formContact->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($contact);
            $entityManager->flush();
            // do anything else you need here, like send an email
            $this->addFlash('add_success', "Votre demande a été pris en compte!");

            return $this->redirectToRoute('annonces_home');
        }

        return $this->render('contact/index.html.twig', [
            'formContact' => $formContact->createView()
        ]);
    }
}
