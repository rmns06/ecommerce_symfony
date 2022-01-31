<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PurchaseConfirmationController extends AbstractController
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour confirmer une commande")
     */
    public function confirm(Request $request, EntityManagerInterface $em): Response
    {   
        //création du formulaire et vérifier sa soummission
        $form = $this->createForm(CartConfirmationType::class);

        $form->handleRequest($request);

        //Vérifier qu'il y'a des articles dans le panier sinon redirection
        $cartItems = $this->cartService->getDetailledCartItems();
        
        if (!$form->isSubmitted()) 
        {
            $this->addFlash('warning', "Validez votre confirmation de commande en remplissant le formulaire");
            $this->redirectToRoute('cart_show');
        }
        
        if (count($cartItems) === 0) {
            $this->addFlash("warning", "Vous ne pouvez pas confirmer une commande sans article");
            $this->redirectToRoute('homepage');
        }

        // création de la commande et des lignes article(purchaseItem)
        if ($form->isSubmitted() && $form->isValid())
        {
            /**@var Purchase */
            $purchase = $form->getData();
            $purchase
                ->setUser($this->getUser())
                ->setPurchaseAt(new DateTimeImmutable())
                ->setTotal($this->cartService->getTotal());

            $em->persist($purchase);

            foreach ($this->cartService->getDetailledCartItems() as $item) 
            {
                $purchaseItem = new PurchaseItem;
                $purchaseItem
                    ->setPurchase($purchase)
                    ->setProduct($item->product)
                    ->setProductName($item->product->getName())
                    ->setQuantity($item->qty)
                    ->setProductPrice($item->product->getPrice())
                    ->setTotal($item->GetTotal());

                $em->persist($purchaseItem);
            }
            
            $em->flush();
           

        }

        $this->cartService->clear();

        $this->addFlash('success', "La commande à bien été enregistrée");

        return $this->redirectToRoute('purchases_index');
    }
}
