<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Builder\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(ProductRepository $productRepository, EntityManagerInterface $em): Response
    {   
        //Import Repository by entityManager or upside by serviceContainer
        // $productRepository = $em->getRepository(Product::class);
        //ProductRepository methods to search products
        //count([])
        //find(id)
        //findBy([], [])
        //findOneBy([], [])
        //findAll()
        // $products = $productRepository->findAll();
        // dump($products);

        //Entity Manager method to add product
        // $product = new Product();
        // $product->setName('Chaise en plastique')
        //         ->setPrice(2600)
        //         ->setSlug('chaise-en-plastique');
        // $em->persist($product);
        // $em->flush();
        
        //Entity Manager method to delete product
        // $product = $productRepository->find(2);
        // $em->remove($product);
        // $em->flush();

        //Entity Manager Method to update product
        // $product = $productRepository->find(1);
        // $product->setPrice(1500);
        // $em->flush();
        // dump($product);

        $products = $productRepository->findBy([], [], 3);
        return $this->render('home/index.html.twig', [
            'products' => $products,
        ]);
    }
}
