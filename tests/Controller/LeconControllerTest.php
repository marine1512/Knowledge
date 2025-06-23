<?php

namespace App\Tests\Controller;

use App\Entity\Lecon;
use App\Entity\User;
use App\Entity\UserPurchase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LeconControllerTest extends WebTestCase
{
    /**
     * Test the lessons index page.
     */
    public function testIndex(): void
    {
        $client = static::createClient();

        // Simulate a GET request to the lessons index URL
        $crawler = $client->request('GET', '/lecon/');

        // Assert that the response is successful (HTTP 200 status code)
        $this->assertResponseIsSuccessful();

        // Assert that the page contains the table or grid for lessons
        $this->assertSelectorExists('table'); // Adjust the selector based on your view
    }

    /**
     * Test creating a new lesson.
     */
    public function testCreateNewLesson(): void
    {
        $client = static::createClient();

        // Simulate accessing the "new" lesson page
        $crawler = $client->request('GET', '/lecon/new');

        // Assert that the response is successful and the form is shown
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form'); // Check if a form is displayed

        // Submit form data
        $form = $crawler->selectButton('Save')->form([
            'nom' => 'New Lesson Name', // Adjust to match your form field names
        ]);

        // Submit the form
        $client->submit($form);

        // Assert redirection after saving
        $this->assertResponseRedirects('/lecon'); // Adjust route if necessary

        // Assert the new lesson is saved in the database
        $lecon = self::getContainer()->get('doctrine')->getRepository(Lecon::class)->findOneBy(['nom' => 'New Lesson Name']);
        $this->assertNotNull($lecon);
    }

    /**
     * Test editing an existing lesson.
     */
    public function testEditLesson(): void
    {
        $client = static::createClient();

        // Simulate a pre-existing lesson in the database
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $lecon = new Lecon();
        $lecon->setNom('Old Lesson Name');
        $entityManager->persist($lecon);
        $entityManager->flush();

        // Access the edit page
        $crawler = $client->request('GET', '/lecon/' . $lecon->getId() . '/edit');

        // Assert the page is loaded successfully
        $this->assertResponseIsSuccessful();

        // Update the form with new data
        $form = $crawler->selectButton('Update')->form([
            'nom' => 'Updated Lesson Name',
        ]);
        $client->submit($form);

        // Assert redirection to the lesson index
        $this->assertResponseRedirects('/lecon');

        // Assert the lesson name has been updated in the database
        $updatedLecon = $entityManager->getRepository(Lecon::class)->find($lecon->getId());
        $this->assertEquals('Updated Lesson Name', $updatedLecon->getNom());
    }

    /**
     * Test deleting a lesson.
     */
    public function testDeleteLesson(): void
    {
        $client = static::createClient();

        // Simulate a pre-existing lesson in the database
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $lecon = new Lecon();
        $lecon->setNom('Lesson to Delete');
        $entityManager->persist($lecon);
        $entityManager->flush();

        // Send a POST request to delete the lesson
        $client->request('POST', '/lecon/' . $lecon->getId() . '/delete');

        // Assert redirection back to the lessons index
        $this->assertResponseRedirects('/lecon');

        // Assert the lesson is deleted from the database
        $deletedLecon = $entityManager->getRepository(Lecon::class)->find($lecon->getId());
        $this->assertNull($deletedLecon);
    }

    /**
     * Test validation of a lesson.
     */
    public function testValidateLesson(): void
    {
        $client = static::createClient();

        // Simulate a logged-in user
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $user = new User();
        $user->setEmail('user@test.com');
        $user->setPassword('password'); // Use a properly hashed password
        $user->setRoles(['ROLE_USER']);
        $entityManager->persist($user);

        // Simulate a lesson and an associated purchase
        $lecon = new Lecon();
        $lecon->setNom('Purchased Lesson');
        $entityManager->persist($lecon);

        $purchase = new UserPurchase();
        $purchase->setUser($user);
        $purchase->setLecon($lecon);
        $purchase->setIsValidated(false);
        $entityManager->persist($purchase);

        $entityManager->flush();

        // Log the user in
        $client->loginUser($user);

        // Send a POST request to validate the lesson
        $client->request('POST', '/lecon/' . $lecon->getId() . '/validate');

        // Assert redirection after validation
        $this->assertResponseRedirects('/user_achat');

        // Assert the lesson is marked as validated
        $validatedPurchase = $entityManager->getRepository(UserPurchase::class)->find($purchase->getId());
        $this->assertTrue($validatedPurchase->isValidated());
    }
}