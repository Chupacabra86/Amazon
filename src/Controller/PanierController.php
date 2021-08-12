<?php

namespace App\Controller;

use App\Entity\Books;
use App\Service\CartService;
use App\Service\PaymentService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'panier_index')]
    public function index(CartService $cartService): Response
    {
        $cart = $cartService->get();                            // on récupère le panier s'il existe dans la session,
        return $this->render('panier/index.html.twig', [        // affichage dans l'index
            'cart' => $cart                                     // on envoie la valeur 'cart' dans l'index
        ]);
    }


    #[Route('/panier/ajouter/{id}', name: 'panier_add')]
    public function add(Books $books, CartService $cartService): Response
    {
        $cartService->add($books);
        return $this->redirectToRoute('panier_index');          // 5. on redirige l'utilisateur vers l'index du panier
    }


    #[Route('/panier/supprimer/{id}', name: 'panier_delete')]
    public function delete(Books $books, CartService $cartService): Response
    {
        $cartService->delete($books);
        return $this->redirectToRoute('panier_index');                                          // 5. on redirige l'utilisateur vers l'index du 
    }                                                                                           // panier


    #[Route('/panier/vider', name: 'panier_empty')]
    public function clear(CartService $cartService): Response
    {
        $cartService->clear();
        return $this->redirectToRoute('panier_index');
    }


    #[Route('/panier/remove/{id}', name: 'panier_remove_line')]
    public function removeLine(Books $books, CartService $cartService): Response
    {
        $cartService->removeLine($books);
        return $this->redirectToRoute('panier_index');                                                      // redirection
    }


    #[Route('/panier/valider', name: 'panier_validate')]
    public function validate(PaymentService $paymentService): Response
    {
        $stripeSessionId = $paymentService->create();

        return $this->render('panier/redirect.html.twig', [                 //render('le destinaire', [l'info qu'on veut lui donner])
            'stripeSessionId' => $stripeSessionId
        ]); 
    }

}
