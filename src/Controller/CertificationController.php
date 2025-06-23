<?php

namespace App\Controller;

use App\Entity\Theme;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class CertificationController
 *
 * Handles the display of certifications based on themes.
 */
class CertificationController extends AbstractController
{
    /**
     * Displays the certifications available for the themes.
     *
     * @param EntityManagerInterface $entityManager The entity manager to access the database.
     * @return Response The rendered view with certifications.
     */
    #[Route('/certification', name: 'certification', methods: ['GET'])]
public function showCertification(EntityManagerInterface $entityManager): Response
{
    $themes = $entityManager->getRepository(Theme::class)->findAll();

    if (!$themes) {
        throw $this->createNotFoundException('Aucun thème disponible.');
    }

    $certifications = [];
    foreach ($themes as $theme) {
        if ($theme->getCertification()) {
            $certifications[] = $theme->getCertification();
        }
    }

    return $this->render('certification/certification.html.twig', [
        'certifications' => $certifications,
    ]);
}
}