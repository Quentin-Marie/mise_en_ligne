<?php

namespace App\Controller;

use App\Service\CartService;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripePayController extends AbstractController
{
    #[Route('/stripe/pay', name: 'app_stripe_pay')]
    public function index(CartService $cs): Response
    {

        $fullCart = $cs->getCartWithData();

        $line_items = [];


        foreach ($fullCart as $item) {
            $line_items[] = [
                'price_data' => [
                    'unit_amount' => $item['activity']->getPrice() * 10, 'currency' => 'EUR',
                    'product_data' => [
                        'name' => $item['activity']->getName()
                    ]
                ],
                'quantity' => $item['quantity']


            ];
        }

        \Stripe\Stripe::setApiKey('sk_test_51NwgsJIQPJJkuiXBepJX6I9xLi5YYo3JjY6VezvSyaQCX40N1LbF0bMrtp5izUM0o1NQ3KBrh88a8YRMHQLzpJMp00wJcuaLU0');

        $session = Session::create([
            // 'success_url' => 'http://127.0.1.8000/commande/success',
            // 'cancel_url' => 'http://127.0.1.8000/wishList',
            'success_url' => 'https://www.quentin.lock.cezdigit.com//commande/success',
            'cancel_url' => 'https://www.quentin.lock.cezdigit.com//wishList',
            'payment_method_types' => ['card'],
            'line_items' => $line_items,
            'mode' => 'payment'
        ]);

        return $this->redirect($session->url, 303);
    }

    #[Route('/commande/{success}', name: 'commande')]
    public function commande($success = null): Response
    {

        if ($success) {
            $this->addFlash(
                'success',
                'Merci pour votre confiance'
            );

            return $this->redirectToRoute('app_front');
        } else {
            $this->addFlash(
                'danger',
                'Un problème est survenu, merci de recommencer votre paiement'
            );
        }


        return $this->redirectToRoute('RouteName');
    }
}
