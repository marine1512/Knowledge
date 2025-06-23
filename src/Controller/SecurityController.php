<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 *
 * Handles user authentication, including login and logout functionalities.
 */
class SecurityController extends AbstractController
{
    /**
     * Displays the login form and handles user authentication.
     *
     * @param AuthenticationUtils $authenticationUtils The authentication utils service.
     * @return Response The rendered login view or redirect to home if already authenticated.
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home'); 
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * This method is used by Symfony to handle the login check.
     * It should not contain any logic and will be intercepted by the security system.
     *
     * @throws \LogicException
     */
    #[Route(path: '/login_check', name: 'app_login_check')]
    public function loginCheck(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by your security system.');
    }
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
    }
}