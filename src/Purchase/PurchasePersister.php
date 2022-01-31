<?php

namespace App\Purchase;

use App\Cart\CartService;
use DateTimeImmutable;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister 
{

    protected $security;
    protected $cartService;
    protected $em;

    public function __construct(Security $security, CartService $cartService, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->cartService = $cartService;
        $this->em = $em;
    }

    public function storePurchase(Purchase $purchase)
    {
        $purchase
        ->setUser($this->security->getUser())
        ->setPurchaseAt(new DateTimeImmutable())
        ->setTotal($this->cartService->getTotal());

    $this->em->persist($purchase);

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

        $this->em->persist($purchaseItem);
    }
    
    $this->em->flush();
   
    }
}