<?php

namespace App\Controller;

use App\Entity\Theme;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ThemeRepository;
use App\Repository\CursusRepository;

/**
 * Class ThemeController
 *
 * Handles operations related to themes, including displaying theme details,
 * listing themes, creating, editing, and deleting themes.
 */
class ThemeController extends AbstractController
{
    /**
     * Displays the details of a specific theme product.
     *
     * @param int $id The ID of the theme product.
     * @param ThemeRepository $themeRepository Repository to fetch theme products.
     * @return Response Rendered theme detail view.
     */
    #[Route('/product/{id}', name: 'product_detail', methods: ['GET'])]
    public function detail(int $id, ThemeRepository $themeRepository): Response
    {
        $theme = $themeRepository->find($id);

        if (!$theme) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        return $this->render('formations/theme-detail.html.twig', [
            'theme' => $theme,
            'cursus' => $theme->getCursus(),
        ]);
    }

    /**
     * Displays the lessons associated with a specific cursus.
     *
     * @param int $id The ID of the cursus.
     * @param CursusRepository $cursusRepository Repository to fetch cursus entries.
     * @return Response Rendered cursus detail view with lessons.
     */
    #[Route('/cursus/{id}/lecon', name: 'cursus_lecon', methods: ['GET'])]
    public function lessons(int $id, CursusRepository $cursusRepository): Response
    {
        $cursus = $cursusRepository->find($id);

        if (!$cursus) {
            throw $this->createNotFoundException('Cursus non trouvé');
        }

        $lecons = $cursus->getLecons();

        return $this->render('formations/cursus-detail.html.twig', [
            'cursus' => $cursus,
            'lecons' => $lecons,  
        ]);
    }

    /**
     * Displays a list of all themes.
     *
     * @param EntityManagerInterface $entityManager The entity manager to access the database.
     * @return Response Rendered view with themes.
     */
    #[Route('/theme', name: 'theme', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $themes = $entityManager->getRepository(Theme::class)->findAll();

        return $this->render('admin/theme/index.html.twig', [
            'themes' => $themes,
        ]);
    }

    /**
     * Creates a new theme.
     *
     * @param Request $request The current HTTP request.
     * @param EntityManagerInterface $entityManager The entity manager to persist the new theme.
     * @return Response A redirect response to the theme index page or the rendered form view.
     */
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $theme = new Theme();

        if ($request->isMethod('POST')) {
            $theme->setNom($request->request->get('nom'));
            $theme->setImage($request->files->get('image'));
        

            $entityManager->persist($theme);
            $entityManager->flush();

            return $this->redirectToRoute('theme');
        }

        return $this->render('admin/theme/new.html.twig');
    }

    /**
     * Edits an existing theme.
     *
     * @param Theme $theme The theme entity to edit.
     * @param Request $request The current HTTP request.
     * @param EntityManagerInterface $entityManager The entity manager to persist changes.
     * @return Response A redirect response to the theme index page or the rendered form view.
     */
    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Theme $theme, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder($theme)
            ->add('nom')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('theme');
        }

        return $this->render('admin/theme/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a theme.
     *
     * @param Theme $theme The theme entity to delete.
     * @param EntityManagerInterface $entityManager The entity manager to remove the theme.
     * @return Response A redirect response to the theme index page.
     */
    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Theme $theme, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($theme);
        $entityManager->flush();

        return $this->redirectToRoute('theme');
    }
}