<?php

namespace App\Controller;

use DateTime;
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
        $em = $this->getDoctrine()->getManager();    
        $commande = new Commande();
        $datetime = new DateTime('NOW');
            
        $commande->setCreatedAt($datetime);
        $commande->setReference($stripeSessionId);
        // dd ($commande);

        $panier = $cartService->get();

        foreach ($panier['elements'] as $element){
            $commandeDetail = new CommandeDetail();
            $commandeDetail->setQuantity($element['quantity']);
            $commande->addCommandeDetail($commandeDetail);
            $book = $booksRepository->find($element['book']->getId());
            $book->addCommandeDetail($commandeDetail);
            $em->persist($book);
        }

        // $commande->setTotal($total);
        // $commande->setUser($this->getUser());
            
        $em->persist($commande);
        $em->flush();

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
