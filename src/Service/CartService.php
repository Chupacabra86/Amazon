<?php

namespace App\Service;

use App\Entity\Books;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class CartService
{
    private $sessionInterface;

    public function __construct(SessionInterface $sessionInterface)     // SessionInterface est une dépendance, pas un paramètre
    {
        $this->sessionInterface = $sessionInterface;
    }

    public function get()
    {
        $cart = $this->sessionInterface->get('cart');           // on récupère le panier s'il existe dans la session,
        if ($cart === null)                                     // sinon on crée nouveau panier
        {
            $cart =[                                            // panier initialisé avec les éléments suivants
                'total' => 0.0,
                'elements' => []
            ];
        }
        return $cart;
    }

    public function add(Books $books): void
    {
        $cart = $this->get();

        $bookId = $books->getId();                              // 2. on vérifie si l'id du produit existe déja dans le panier
        if (!isset($cart['elements'][$bookId]))                  // si l'id n'existe pas
        {
            $cart['elements'][$bookId] = [                       // on ajoute un livre avec la quantité = 0
                'book' => $books,
                'quantity' => 0
            ];
        }

        ++$cart['elements'][$bookId]['quantity'];                // 3. on incrémente la quantité de 1
        $cart['total'] = $cart['total'] + $books->getPrice();   // on calcule le nouveau total
        $this->sessionInterface->set('cart', $cart);            // 4. on sauvegarde le nouveau panier dans la session 
    }

    public function delete(Books $books): void
    {
        $cart = $this->get();
        $bookId = $books->getId();

        if (!isset($cart['elements'][$bookId]))                                      // 2. on vérifie si l'id existe dans le panier
        {
            return;                                                                 // on ne fait rien si le livre n'est pas dans le panier
        }
                      
        $cart['total'] = $cart['total'] - $books->getPrice();                                   // 3. on recalcule le total
        $cart['elements'][$bookId]['quantity'] = $cart['elements'][$bookId]['quantity'] -1;       // on met à jour les quantités

        if ($cart['elements'][$bookId]['quantity'] <= 0)                                         // si la quantité est = 0, on supprime le produit
        {
            unset($cart['elements'][$bookId]);
        }

        $this->sessionInterface->set('cart', $cart);
    }

    public function clear(): void
    {
        $this->sessionInterface->remove('cart');
    }

    public function removeLine(Books $books): void
    {
        $cart = $this->get();

        $bookId = $books->getId();
        if (!isset($cart['elements'][$bookId]))                  // si l'id n'existe pas dans le panier, on ne fait rien
        {
            return;
        }
        $cart['total'] = $cart['total'] - $books->getPrice() * $cart['elements'][$bookId]['quantity'];       // mise à jour du total
        unset($cart['elements'][$bookId]);                                                                   // suppression de la ligne
        $this->sessionInterface->set('cart', $cart); 
    }

}