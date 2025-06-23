<?php

// src/DataFixtures/ThemeFixture.php
namespace App\DataFixtures;

use App\Entity\Theme;
use App\Entity\Cursus;
use App\Entity\Lecon;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ThemeFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création des thèmes
        $themesData = [
            ['id' => 1, 'nom' => 'Musique', 'image' => 'images/musique.png', 'valide' => false],
            ['id' => 2, 'nom' => 'Informatique', 'image' => 'images/informatique.png', 'valide' => false],
            ['id' => 3, 'nom' => 'Jardinage', 'image' => 'images/jardinage.png', 'valide' => false],
            ['id' => 4, 'nom' => 'Cuisine', 'image' => 'images/cuisine.png', 'valide' => false],
        ];

        $themes = [];
        foreach ($themesData as $data) {
            $theme = new Theme();
            $theme->setNom($data['nom']);
            $theme->setImage($data['image']);
            $theme->setValide($data['valide']);
            $manager->persist($theme);
            $themes[$data['id']] = $theme;
        }

        // Création des cursus
        $cursusData = [
            ['id' => 1, 'theme_id' => 1, 'nom' => 'Cursus d’initiation à la guitare', 'prix' => 50, 'is_validated' => false],
            ['id' => 2, 'theme_id' => 1, 'nom' => 'Cursus d’initiation au piano', 'prix' => 50, 'is_validated' => false],
            ['id' => 3, 'theme_id' => 2, 'nom' => 'Cursus d’initiation au développement web', 'prix' => 60, 'is_validated' => false],
            ['id' => 4, 'theme_id' => 3, 'nom' => 'Cursus d’initiation au jardinage', 'prix' => 30, 'is_validated' => false],
            ['id' => 5, 'theme_id' => 4, 'nom' => 'Cursus d’initiation à la cuisine', 'prix' => 44, 'is_validated' => false],
            ['id' => 6, 'theme_id' => 4, 'nom' => 'Cursus d’initiation à l’art du dressage culinaire', 'prix' => 48, 'is_validated' => false],
        ];

        $cursus = [];
        foreach ($cursusData as $data) {
            $cur = new Cursus();
            $cur->setNom($data['nom']);
            $cur->setPrix($data['prix']);
            $cur->setIsValidated($data['is_validated']);
            $cur->setTheme($themes[$data['theme_id']]);
            $manager->persist($cur);
            $cursus[$data['id']] = $cur;
        }

        // Création des leçons
        $lessonsData = [
            ['id' => 1, 'cursus_id' => 1, 'nom' => 'Leçon n°1 : Découverte de l’instrument', 'prix' => 26],
            ['id' => 2, 'cursus_id' => 1, 'nom' => 'Leçon n°2 : Les accords et les gammes', 'prix' => 26],
            ['id' => 3, 'cursus_id' => 2, 'nom' => 'Leçon n°1 : Découverte de l’instrument', 'prix' => 26],
            ['id' => 4, 'cursus_id' => 2, 'nom' => 'Leçon n°2 : Les accords et les gammes', 'prix' => 26],
            ['id' => 5, 'cursus_id' => 3, 'nom' => 'Leçon n°1 : Les langages Html et CSS', 'prix' => 32],
            ['id' => 6, 'cursus_id' => 3, 'nom' => 'Leçon n°2 : Dynamiser votre site avec Javascript', 'prix' => 32],
            ['id' => 7, 'cursus_id' => 4, 'nom' => 'Leçon n°1 : Les outils du jardinier', 'prix' => 16],
            ['id' => 8, 'cursus_id' => 4, 'nom' => 'Leçon n°2 : Jardiner avec la lune', 'prix' => 16],
            ['id' => 9, 'cursus_id' => 5, 'nom' => 'Leçon n°1 : Les modes de cuisson', 'prix' => 23],
            ['id' => 10, 'cursus_id' => 5, 'nom' => 'Leçon n°2 : Les saveurs', 'prix' => 23],
            ['id' => 11, 'cursus_id' => 6, 'nom' => 'Leçon n°1 : Mettre en œuvre le style dans l’assiette', 'prix' => 26],
            ['id' => 12, 'cursus_id' => 6, 'nom' => 'Leçon n°2 : Harmoniser un repas à quatre plats', 'prix' => 26],
        ];

        foreach ($lessonsData as $data) {
            $lecon = new Lecon();
            $lecon->setNom($data['nom']);
            $lecon->setPrix($data['prix']);
            $lecon->setCursus($cursus[$data['cursus_id']]);
            $manager->persist($lecon);
        }

        // Validation des données en base
        $manager->flush();
    }
}
