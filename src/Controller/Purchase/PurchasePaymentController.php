<?php

namespace App\Controller\Purchase;

use App\Repository\PurchaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PurchasePaymentController extends AbstractController
{
    /**
     * @Route("/purchase/pay/{id}", name="purchase_payment_form")
     */
    public function showPaymentForm($id, PurchaseRepository $purchaseRepository)
    {
        $purchase = $purchaseRepository->find($id);

        if (! $purchase) {
            return $this->redirectToRoute('homepage');
        }

        \Stripe\Stripe::setApiKey('sk_test_51KOHpyIDdyF0fwsP9CXVHRmXvhgLtDGKBrIwQ1WgHSvqtFZffeguNInaUMHZkdeo8TeNfkP6NKyixTJaq5yC8YZl00xSmHGkVz');

        $intent = \Stripe\PaymentIntent::create([
            'amount' => $purchase->getTotal(),
            'currency' => 'eur',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);


        return $this->render('purchases/payment.html.twig',[
            'clientSecret' => $intent->client_secret,
        ]);
    }
}