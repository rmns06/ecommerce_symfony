<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(EntityManagerInterface $em, Request $request, SluggerInterface $slugger): Response
    {   
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {   
            $category = $form->getData();
            $category->setSlug(strtolower($slugger->slug($category->getName())));
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('category/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/category/{id}/edit", name="category_edit")
     */
    public function edit($id, CategoryRepository $repository, EntityManagerInterface $em, Request $request, SluggerInterface $slugger): Response
    {
        $category = $repository->find($id);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        
        // si la catégorie n'existe pas
        if (!$category)
        {
            throw $this->createNotFoundException('Cette catégorie n\'existe pas');
        }

        
        // Seul le créateur de la catégorie peut la modifier
        // $user = $this->getUser();
        // if ($user !== $category->getOwner()) {
        //     throw new AccessDeniedHttpException("Vous n'êtes pas le créateur de cette catégorie");
        // }
        
        //gestion du droit de modification avec un voter

        $this->denyAccessUnlessGranted('CAN_EDIT', $category, "Vous n'êtes pas le créateur de cette cact");
        if ($form->isSubmitted() && $form->isValid())
        {
            $category->setSlug(strtolower($slugger->slug($category->getName())));
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('category/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
