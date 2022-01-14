<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AbstractController
{
    protected $cartService;
    protected $productRepository;

    public function __construct(CartService $cartService, ProductRepository $productRepository) {
        $this->cartService = $cartService;
        $this->productRepository = $productRepository;
    }
    //  show tab detailled cart with the session storage
    /**
     * @Route("/cart", name="cart_show")
     */
    public function show(SessionInterface $session, ProductRepository $productRepository) :Response
    {   
        // $detailedCart = [];
        // $totalPrice = 0;
        // $items = $session->get('cart', []);

        // foreach ($items as $id => $qty) {
        //     $product = $productRepository->find($id);
        //     $detailedCart[] = [
        //         'product' => $product,
        //         'qty' => $qty
        //     ];
        //     $totalPrice += ($product->getPrice() * $qty);
        // }

        // $totalPrice /= 100;
        $detailedCart = $this->cartService->getDetailledCartItems();

        $totalPrice = $this->cartService->getTotal();

        return $this->render('cart/index.html.twig',[
            'detailedCart'=> $detailedCart,
            'totalPrice' => $totalPrice 
        ]);
    }

    // Add products on sesssion cart
    /**
     * @Route("/cart/{id<\d+>}", name="cart_add")
     */
    public function add($id, Request $request, ProductRepository $productRepository)
    {
        // $cart = $session->get('cart', []);

        // /** @var Product */
        // $product = $productRepository->find($id);
        // $productName = $product->getName();

        // if (!$product){
        //     throw $this->createNotFoundException("Le produit ${product} n'existe pas");
        // }
        
        // if(array_key_exists($id, $cart)){
        //     $cart[$id]++;
        // }else{
        //     $cart[$id] = 1;
        // }
        
        // $session->set('cart', $cart);

        $this->cartService->Add($id);

        $this->addFlash('success', "Le produit a bien été ajouté au panier");
        
        //stay on cart page after add product
        if($request->query->get('returnToCart')){
            // return $this->redirectToRoute('cart_show');
            return $this->redirect($request->headers->get('referer'));
        }

        return $this->redirectToRoute('product_detail', [
            'slug' => $productRepository->find($id)->getSlug(),
            'id' => $id
        ]);
    }

    // Remove product on session cart
    /**
     * @Route("/cart/remove/{id<\d+>}", name="cart_remove")
     */
    public function remove($id)
    {
        $product = $this->productRepository->find($id);
        if (!$product){
                throw $this->createNotFoundException("Le produit ${product} n'existe pas");
        }

        $this->cartService->remove($id);

        $this->addFlash('secondary', "Le produit a bien été supprimé du panier");

        return $this->redirectToRoute('cart_show');
    }

    //decrement item on cart
    /**
     * @Route("/cart/decrement/{id<\d+>}", name="cart_decrement")
     */
    public function decrement($id)
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("Le produit ${product} n'existe pas");
        }

        $this->cartService->decrement($id);

        $this->addFlash("secondary", "Le produit a bien été retiré du panier");

        return $this->redirectToRoute('cart_show');
    }

}
