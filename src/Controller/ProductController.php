<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category", priority=-1)
     */
    public function category ($slug, CategoryRepository $categoryRepository, UrlGeneratorInterface $urlGenerator): Response
    {   

        $category = $categoryRepository->findOneBy(['slug' => $slug]);

        if (!$category)
        {
            throw $this->createNotFoundException('Cette catégory n\' existe pas');
        }
        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category,
            // utiliser la function path de twig pour generer l'url sans import
            'urlGenerator' => $urlGenerator,

        ]);
    }

    /**
     * @Route("/{slug}/{id}", name="product_detail", priority=-1)
    */
    public function detail ($id, ProductRepository $productRepository, UrlGeneratorInterface $urlGenerator)
    {
        $product = $productRepository->find($id);
        
        if (!$product)
        {
            throw $this->createNotFoundException('Ce produit n\'existe pas');
        }
        return $this->render('product/detail.html.twig', [
            'product' => $product,
            'urlGenerator' => $urlGenerator
        ]);
    }

    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create (Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        //Creation d'un nouveau produit avec le formulaire génére avec le make:form et ajout dans la bdd
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $product = $form->getData();
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('product_detail', [
                'id' => $product->getId(), 
                'slug' => $product->getSlug()
            ]);
        }
        
        return $this->render('product/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
