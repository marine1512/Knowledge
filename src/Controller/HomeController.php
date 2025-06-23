<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ThemeRepository;
use App\Entity\Theme;

/**
 * Class HomeController
 *
 * Handles the display of the home page, including theme products and user login status.
 */
class HomeController extends AbstractController
{
    /**
     * Displays the home page with theme products and user login status.
     *
     * @param ThemeRepository $themeRepository Repository to fetch theme products.
     * @return Response Rendered home page view.
     */
    #[Route('/', name: 'home')]
    public function index(ThemeRepository $themeRepository): Response
    {
        $themeProducts = $themeRepository->findAll();

        $isUserLoggedIn = $this->isGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('home/index.html.twig', [
            'themeProducts' => $themeProducts,
            'isUserLoggedIn' => $isUserLoggedIn,
        ]);
    }
}