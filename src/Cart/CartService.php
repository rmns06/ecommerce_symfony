<?php

namespace App\Cart;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService extends AbstractController
{
    protected $session;
    protected $productRepository;
    protected $requestStack;

    public function __construct(SessionInterface $session, ProductRepository $productRepository, RequestStack $requestStack)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
        $this->requestStack = $requestStack;
    }

    protected function getCart() :array
    {
        // before 5.3 
        // return $this->session->get('cart', []);
        
        return $this->requestStack->getSession()->get('cart', []);
        
    }

    protected function saveCart(array $cart) :void
    {
        $this->session->set('cart', $cart);
    }

    //Fnt to add product on cart
    public function Add(int $id)
    {
        $cart = $this->getCart();

        $product = $this->productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("Le produit ${product} n'existe pas");
        }

        // if (array_key_exists($id, $cart)) {
        //     $cart[$id]++;
        // } else {
        //     $cart[$id] = 1;
        // }
        //refacto de la ligne au dessus
        if (!array_key_exists($id, $cart)) {
            $cart[$id] = 0;
        }
        $cart[$id]++;

        $this->saveCart($cart);
    }

    public function getTotal(): int
    {
        $total = 0;
        $items = $this->getCart();
        foreach ($items as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $total += $product->getPrice() * $qty;
        }
        return $total;
    }

    // get all item in session to create CartItem array
    public function getDetailledCartItems(): array
    {
        $detailedCart = [];
        $items = $this->getCart();

        foreach ($items as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $detailedCart[] = new CartItem($qty, $product);
        }

        return $detailedCart;
    }

    //add function to count all product in cart and return the total
    public function getCount(): int
    {
        $count = 0;
        $items = $this->getCart();
        foreach ($items as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $count += $qty;
        }
        return $count;
    }

    //function to remove product from cart
    public function remove(int $id): void
    {
        $cart = $this->getCart();

        unset($cart[$id]);

        $this->saveCart($cart);
    }

    //function for decrement item
    public function decrement(int $id): void
    {
        $cart = $this->getCart();

        if (!array_key_exists($id, $cart)) {
            return;
        }

        if ($cart[$id] === 1) {
            $this->remove($id);
            return;
        }
        $cart[$id]--;

        $this->saveCart($cart);
    }
}
