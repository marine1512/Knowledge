<?php

namespace App\Controller;

use App\Entity\Cursus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CursusController
 *
 * Handles operations related to the cursus, including listing, creating,
 * editing, and deleting cursus entries.
 */
#[Route('/cursus')]
class CursusController extends AbstractController
{
    /**
     * Displays a list of all cursus entries.
     *
     * @param EntityManagerInterface $entityManager The entity manager to access the database.
     * @return Response The rendered view with cursus entries.
     */
    #[Route('/', name: 'cursus_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $cursus = $entityManager->getRepository(Cursus::class)->findAll();

        return $this->render('admin/cursus/index.html.twig', [
            'cursus' => $cursus,
        ]);
    }

    /**
     * Creates a new cursus entry.
     *
     * @param Request $request The current HTTP request.
     * @param EntityManagerInterface $entityManager The entity manager to persist the new cursus.
     * @return Response A redirect response to the cursus index page or the rendered form view.
     */
    #[Route('/new', name: 'new_cursus', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cursus = new Cursus();

        if ($request->isMethod('POST')) {
            $cursus->setNom($request->request->get('nom'));
        

            $entityManager->persist($cursus);
            $entityManager->flush();

            return $this->redirectToRoute('cursus');
        }

        return $this->render('admin/cursus/new.html.twig');
    }

    /**
     * Edits an existing cursus entry.
     *
     * @param Cursus $cursus The cursus entity to edit.
     * @param Request $request The current HTTP request.
     * @param EntityManagerInterface $entityManager The entity manager to persist changes.
     * @return Response A redirect response to the cursus index page or the rendered form view.
     */
    #[Route('/{id}/edit', name: 'edit_cursus', methods: ['GET', 'POST'])]
    public function edit(Cursus $cursus, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder($cursus)
            ->add('nom')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('cursus');
        }

        return $this->render('admin/cursus/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a cursus entry.
     *
     * @param Cursus $cursus The cursus entity to delete.
     * @param EntityManagerInterface $entityManager The entity manager to remove the cursus.
     * @return Response A redirect response to the cursus index page.
     */
    #[Route('/{id}/delete', name: 'delete_cursus', methods: ['POST'])]
    public function delete(Cursus $cursus, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($cursus);
        $entityManager->flush();

        return $this->redirectToRoute('cursus');
    }
}