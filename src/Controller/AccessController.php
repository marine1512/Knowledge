<?php

namespace App\Controller;

use App\Repository\LeconRepository;
use App\Repository\CursusRepository;
use App\Repository\UserPurchaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AccessController
 *
 * Handles user access to purchased courses (Cursus) and lessons (Leçon).
 */
class AccessController extends AbstractController
{
    private $leconRepository;
    private $cursusRepository;
    private $userPurchaseRepository;

    /**
     * Constructor for AccessController
     *
     * @param LeconRepository         $leconRepository         Repository for accessing lessons (Leçon).
     * @param UserPurchaseRepository  $userPurchaseRepository  Repository for managing user purchases.
     * @param CursusRepository        $cursusRepository        Repository for accessing courses (Cursus).
     */
    public function __construct(LeconRepository $leconRepository, UserPurchaseRepository $userPurchaseRepository, CursusRepository $cursusRepository)
    {
        $this->leconRepository = $leconRepository;
        $this->userPurchaseRepository = $userPurchaseRepository;
        $this->cursusRepository = $cursusRepository;
    }

    /**
     * Displays the purchased cursus and lessons for the logged-in user.
     *
     * @return Response
     */
    #[Route('/mes-achats', name: 'user_achat', methods: ['GET'])]
    public function accessPurchasedCursus(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour voir vos cursus.');
            return $this->redirectToRoute('app_login');
        }

        $cursus = $this->userPurchaseRepository->getPurchasedCursus($user);
        $lecon = $this->userPurchaseRepository->getPurchasedLecons($user);
        if (empty($cursus) && empty($lecon)) {
            $this->addFlash('info', 'Vous n’avez encore acheté aucun cursus.');
            return $this->redirectToRoute('home');
        }

        return $this->render('cours_payes/achat.html.twig', [
            'cursus' => $cursus,
            'lecon' => $lecon
        ]);
    }

    /**
     * Displays the details of a specific cursus.
     *
     * @param int $id The ID of the cursus.
     * @param CursusRepository $cursusRepository Repository for accessing courses (Cursus).
     * @return Response
     */
    #[Route('/mes-achats/cursus/{id}', name: 'contenu_cursus', methods: ['GET'])]
    public function detailCursus(int $id, CursusRepository $cursusRepository): Response
    {
        $cursus = $cursusRepository->find($id);

        if (!$cursus) {
            throw $this->createNotFoundException('Cursus non trouvé');
        }

        return $this->render('cours_payes/cursus_contenu.html.twig', [
            'cursus' => $cursus
        ]);
    }

    /**
     * Displays the details of a specific lesson.
     *
     * @param int $id The ID of the lesson.
     * @param LeconRepository $leconRepository Repository for accessing lessons (Leçon).
     * @return Response
     */
    #[Route('/mes-achats/lecon/{id}', name: 'contenu_lecon', methods: ['GET'])]
    public function detailLecon(int $id, LeconRepository $leconRepository): Response
    {
        $lecon = $leconRepository->find($id);

        if (!$lecon) {
            throw $this->createNotFoundException('Leçon non trouvée');
        }

        return $this->render('cours_payes/lecon_contenu.html.twig', [
            'lecon' => $lecon
        ]);
    }

}