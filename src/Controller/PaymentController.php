<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Commande;
use App\Service\CartService;
use App\Entity\CommandeDetail;
use App\Repository\BooksRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    #[Route('/payment/success/{stripeSessionId}', name: 'payment_success')]
    public function success(string $stripeSessionId, CartService $cartService, BooksRepository $booksRepository): Response
    {
        // $user = $this->getUser();                                    // on récupère l'utilisateur

        $cart = $cartService->get();                                    // on récupère le panier
        $commande = new Commande();                                     // on crée un objet vide Commande
        $commande->setCreatedAt(new DateTimeImmutable());               // on met l'heure et la date
        $commande->setReference(substr($stripeSessionId, -10));         // on garde les 10 derniers caractères
        $em = $this->getDoctrine()->getManager();                       // on enregistre en base de données
        $em->persist($commande);
        $em->flush();

        foreach ($cart['elements'] as $element)                         // On traite les éléménets du panier un par un avec une boucle
        {
            $commandeDetail = new CommandeDetail();                     // on crée un objet vide CommandeDetail
            $commandeDetail->setQuantity($element['quantity']);         // on récupère la quantité de l'élément
            $book = $booksRepository->find($element['book']->getId());  // on récupère l'entité book depuis le repository
            $commandeDetail->setBookId($book);                          // on récupère le book de l'élémént
            $commandeDetail->setCommandeId($commande);                  // on met $commande dans commande Detail
            $em->persist($commandeDetail);                              // on enregistre en base de données
            $em->flush();
        }

        $cartService->clear();                                          // on vide le panier

        return $this->render('payment/success.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }


    #[Route('/payment/failure/{stripeSessionId}', name: 'payment_failure')]
    public function failure(string $stripeSessionId): Response
    {
        return $this->render('payment/failure.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }
}
