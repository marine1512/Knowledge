<?php

namespace App\Tests\Controller;

use App\Entity\Cursus;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test class for CursusController.
 */
class CursusControllerTest extends WebTestCase
{
    /**
     * Test the index page of the cursus.
     *
     * Checks if the list of cursus entries is displayed correctly.
     */
    public function testCursusIndex(): void
    {
        $client = static::createClient();
        $entityManager = self::getContainer()->get('doctrine')->getManager();

        // Create dummy cursus data
        $cursus1 = new Cursus();
        $cursus1->setNom('Cursus 1');
        $entityManager->persist($cursus1);

        $cursus2 = new Cursus();
        $cursus2->setNom('Cursus 2');
        $entityManager->persist($cursus2);

        $entityManager->flush();

        // Send a GET request to the index route
        $client->request('GET', '/cursus/');

        // Assert that the response is successful
        $this->assertResponseIsSuccessful();

        // Check if cursus names are displayed in the view
        $this->assertSelectorTextContains('body', 'Cursus 1');
        $this->assertSelectorTextContains('body', 'Cursus 2');
    }

    /**
     * Test creating a new cursus.
     *
     * Ensures that a new cursus can be created via the POST method.
     */
    public function testNewCursus(): void
    {
        $client = static::createClient();

        // Submit the form to create a new Cursus
        $client->request('GET', '/cursus/new');
        $client->submitForm('Save', [
            'nom' => 'New Cursus',
        ]);

        // Assert redirect to the cursus index page
        $this->assertResponseRedirects('/cursus/');

        // Follow the redirect
        $client->followRedirect();

        // Ensure the new cursus is displayed
        $this->assertSelectorTextContains('body', 'New Cursus');
    }

    /**
     * Test editing an existing cursus.
     *
     * Ensures that an existing cursus can be updated correctly.
     */
    public function testEditCursus(): void
    {
        $client = static::createClient();
        $entityManager = self::getContainer()->get('doctrine')->getManager();

        // Create a dummy cursus to edit
        $cursus = new Cursus();
        $cursus->setNom('Old Cursus');
        $entityManager->persist($cursus);
        $entityManager->flush();

        // Access the edit route
        $crawler = $client->request('GET', '/cursus/' . $cursus->getId() . '/edit');

        // Ensure the form is preloaded with the existing cursus name
        $this->assertSelectorExists('input[value="Old Cursus"]');

        // Submit the form with updated data
        $form = $crawler->selectButton('Save')->form([
            'nom' => 'Updated Cursus',
        ]);
        $client->submit($form);

        // Assert redirect to the cursus index page
        $this->assertResponseRedirects('/cursus/');

        // Follow the redirect
        $client->followRedirect();

        // Check if the updated name is displayed
        $this->assertSelectorTextContains('body', 'Updated Cursus');
    }

    /**
     * Test deleting a cursus.
     *
     * Ensures that a cursus can be deleted.
     */
    public function testDeleteCursus(): void
    {
        $client = static::createClient();
        $entityManager = self::getContainer()->get('doctrine')->getManager();

        // Create a dummy cursus to delete
        $cursus = new Cursus();
        $cursus->setNom('Cursus to Delete');
        $entityManager->persist($cursus);
        $entityManager->flush();

        // Delete the cursus using its ID
        $client->request('POST', '/cursus/' . $cursus->getId() . '/delete');

        // Assert redirect to the cursus index page
        $this->assertResponseRedirects('/cursus/');

        // Follow the redirect
        $client->followRedirect();

        // Ensure the cursus is no longer displayed
        $this->assertSelectorTextNotContains('body', 'Cursus to Delete');
    }
}