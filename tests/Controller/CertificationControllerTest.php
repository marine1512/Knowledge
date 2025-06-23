<?php

namespace App\Tests\Controller;

use App\Entity\Theme;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test class for CertificationController.
 */
class CertificationControllerTest extends WebTestCase
{
    /**
     * Test showing certifications when themes are present.
     *
     * Ensures that themes with certifications are displayed correctly.
     */
    public function testShowCertificationWithThemes(): void
    {
        $client = static::createClient();
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        // Create themes with certifications
        $theme1 = new Theme();
        $theme1->setNom('Theme 1');
        $entityManager->flush();

        // Request the certification route
        $client->request('GET', '/certification');

        // Assert that the response is successful
        $this->assertResponseIsSuccessful();

        // Ensure the certifications are displayed correctly in the response
        $this->assertSelectorTextContains('body', 'Certification 1');
        $this->assertSelectorTextContains('body', 'Certification 2');

        // Ensure that themes without certifications are not included
        $this->assertSelectorTextNotContains('body', 'Theme 3');
    }

    /**
     * Test showing certifications when there are no themes in the database.
     *
     * Ensures that an exception is thrown and the appropriate error message is displayed.
     */
    public function testShowCertificationWithNoThemes(): void
    {
        $client = static::createClient();
        $entityManager = self::getContainer()->get('doctrine')->getManager();

        // Clear themes from the database
        $entityManager->createQuery('DELETE FROM App\Entity\Theme')->execute();

        // Request the certification route
        $client->request('GET', '/certification');

        // Assert that the response throws a 404 exception
        $this->assertResponseStatusCodeSame(404);

        // Ensure the error message is displayed (if customized in the exception template)
        $this->assertSelectorTextContains('body', 'Aucun thème disponible.');
    }

    /**
     * Test showing certifications when themes exist, but none have certifications.
     *
     * Ensures that no certifications are displayed when all themes lack certifications.
     */
    public function testShowCertificationWithNoCertifications(): void
    {
        $client = static::createClient();
        $entityManager = self::getContainer()->get('doctrine')->getManager();

        // Create themes without certifications
        $theme1 = new Theme();
        $theme1->setNom('Theme 1')->setCertification(null);

        $theme2 = new Theme();
        $theme2->setNom('Theme 2')->setCertification(null);

        $entityManager->persist($theme1);
        $entityManager->persist($theme2);
        $entityManager->flush();

        // Request the certification route
        $client->request('GET', '/certification');

        // Assert that the response is successful
        $this->assertResponseIsSuccessful();

        // Ensure that no certifications are displayed in the response
        $responseContent = $client->getResponse()->getContent();
        $this->assertStringNotContainsString('Certification', $responseContent);
    }
}