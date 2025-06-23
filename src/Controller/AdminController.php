<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}