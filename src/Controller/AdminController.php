<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserPurchaseRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\UserPurchase;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class AdminController
 *
 * Handles administrative tasks such as viewing the admin dashboard.
 */
class AdminController extends AbstractController
{
    /**
     * Displays the admin dashboard.
     *
     * @return Response
     */
    #[Route('/admin', name: 'admin')]
    public function dashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); 

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/achats', name: 'admin_achats')]
    public function handlePurchases(UserPurchaseRepository $userPurchaseRepository, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        // Retrieve all purchases from the repository
        $purchases = $userPurchaseRepository->findAll();

        return $this->render('admin/achats/index.html.twig', [
            'purchases' => $purchases
        ]);
    }

    /**
     * Deletes a specific purchase.
     *
     * @param UserPurchase $purchase
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/admin/achats/supprimer/{id}', name: 'admin_delete_purchase')]
public function deletePurchase(UserPurchase $purchase, ManagerRegistry $doctrine): Response
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    $em = $doctrine->getManager();
    $em->remove($purchase);
    $em->flush();

    return $this->redirectToRoute('admin_achats');
}
}
