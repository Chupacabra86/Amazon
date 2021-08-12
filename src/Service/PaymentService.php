<?php

namespace App\Service;
use \Stripe\StripeClient;

class PaymentService
{
    private $stripe;                                            // création de variables utiles pour la suite
    private $cartService;

    public function __construct(CartService $cartService)       // pas besoin de préciser le namespace, car dans le même dossier Service
    {
        $this->cartService = $cartService;
        $this->stripe = new StripeClient('sk_test_51JNEMXIpOXEhtTACKd0YrEMQxIS0NgTRwb1qSjrN6mH9cBh1xOHwCdG2XOq917bMOZl98Wjbh7NfbBrxbNDZnMGB00jfeFyQGB');    // clé privé stripe
    }

    public function create(): string                            // fonction pour créer une session de paiement
    {
        // 1. success URL                                       // on définit des variables pour le dev et la prod
        //http://localhost/symfony/amazon/public/payment/success/22
        $protocol = 'http';                                     // on définit le protocole http ou https
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'])
        {
            $protocol = 'https';
        }

        $serverName = $_SERVER['SERVER_NAME'];                  // on définit le serveur localhost ou autre
        $successUrl = $protocol . '://' . $serverName . '/symfony/amazon/public/payment/success/{CHECKOUT_SESSION_ID}';   // vréation d'une variable réutilisable

        // 2. cancel URL
        $cancelUrl = $protocol . '://' . $serverName . '/symfony/amazon/public/payment/failure/{CHECKOUT_SESSION_ID}';

        // 3. éléments (détails du panier)
        // 1 item :                     (array associatif)
        // amount : prix de l'article   (float)
        // quantity : quantité          (integer)
        // currency : 'eur'             (string)
        // name : 'le nom de l'article' (string)

        $items = [];                                                // on crée un tableau vide pour plus tard
        $panier = $this->cartService->get();                        // on récupère les éléments du panier
        foreach ($panier['elements'] as $element)                   // on boucle sur tous les éléments du panier
        {
            $item = [                                               // on renseigne les infos demandées par Stripe
                'amount' => $element['book']->getPrice() * 100,     // prix en centimes donc x100
                'quantity' => $element['quantity'],
                'currency' => 'eur',
                'name' => $element['book']->getTitle()
            ];
            $items [] = $item;                                      // on push les items dans le tableau vide $items
        }

        $session = $this->stripe->checkout->sessions->create([          // méthode stripe
            'success_url' => $successUrl,                               // chemin de success
            'cancel_url' => $cancelUrl,                                 // chemin de cancel
            'payment_method_types' => ['card'],                         // mode de paiement
            'mode' => 'payment',
            'line_items' => $items
        ]);

        return $session->id;
    }
}