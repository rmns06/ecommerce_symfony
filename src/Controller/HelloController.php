<?php

namespace App\Controller;

use App\taxe\Detector;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HelloController extends AbstractController
{
    /**
     * @Route("/hello/{prenom}", name="hello")
     */
    public function index(string $prenom = "World", Detector $detector): Response
    {
        
       return $this->render('hello.html.twig', [
            'prenom' => $prenom,
            'ages' => [
                12,18,29,15
            ],
            'formateur1' => [
                'prenom' => 'Jean',
                'nom' => 'Dupont',
                'age' => 45
            ],
            'formateur2' => [
                'prenom' => 'Maurice',
                'nom' => 'La Fontaine',
                'age' => 18
            ]
        ]);
       
       
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact() :Response
    {   
        return $this->render('base.html.twig');
        
    }
}
