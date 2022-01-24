<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasesListController extends AbstractController
{
    /**
     * @Route("/purchases", name="purchases_index")
     * @IsGranted("ROLE_USER", message="vous devez être connecté pour acceder à vos commandes")
    */
    public function index()
    {
        /**@var User */
        $user = $this->getUser();
        
        // //redirect if not user, the code below can be remplaced by the @ IsGranted before the function
        // if (!$user) {
        //     throw $this->createAccessDeniedException("devez être connecté pour acceder à vos commandes");
        // }
        
        return $this->render('purchases/purchases.html.twig', [
            'purchases' => $user->getPurchases(),
        ]);
    }
}
