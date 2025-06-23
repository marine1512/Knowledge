<?php

namespace App\Service;

use App\Entity\Theme;
use App\Entity\Certification;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ThemeService
 *
 * Provides methods to validate a theme and manage its associated certification.
 */
class ThemeService
{
    /**
     * @var EntityManagerInterface The entity manager to handle database operations.
     * This service is used to persist changes to the database, such as updating the theme's
     * validity status and creating a new certification if necessary.
     */
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * Validates a theme and creates a certification if it does not already exist.
     *
     * @param Theme $theme The theme to validate.
     * @return bool Returns true if a new certification was created, false otherwise.
     * @throws \InvalidArgumentException If the provided theme is null or invalid.
     * @throws \RuntimeException If there is an error during the validation process.
     */
    public function validerTheme(Theme $theme): bool
    {
        if (!$theme) {
            throw new \InvalidArgumentException('Le thème fourni est nul ou invalide.');
        }


        if ($theme->isValide()) {
            return false;
        }

        $theme->setValide(true);

        $certificationCree = false;
        if (!$theme->getCertification()) {
            $certification = new Certification();
            $certification->setTheme($theme);
            $certification->setCreatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($certification);
            $certificationCree = true;
        }

        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException('Échec lors de la validation du thème : ' . $e->getMessage());
        }

        return $certificationCree;
    }
}