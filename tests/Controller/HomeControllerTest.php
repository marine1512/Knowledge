<?php

namespace App\Tests\Controller;

use App\Entity\Theme;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomePage(): void
    {
        $client = static::createClient();
        $container = self::getContainer();
        $entityManager = $container->get('doctrine')->getManager();

        // Create test Theme objects
        $theme1 = new Theme();
        $theme1->setNom('Test Theme 1');

        $theme2 = new Theme();
        $theme2->setNom('Test Theme 2');

        $entityManager->persist($theme1);
        $entityManager->persist($theme2);
        $entityManager->flush();

        // Simulate a GET request to the home page
        $crawler = $client->request('GET', '/');

        // Assert successful response
        $this->assertResponseIsSuccessful();

        // Verify that themes are rendered in the view
        $this->assertSelectorTextContains('h1', 'Test Theme 1'); // Adjust selector based on your view
        $this->assertSelectorTextContains('h1', 'Test Theme 2');
    }

    public function testHomePageWithNoThemes(): void
    {
        $client = static::createClient();
        $entityManager = self::getContainer()->get('doctrine')->getManager();

        // Clear out the database
        $entityManager->createQuery('DELETE FROM App\Entity\Theme')->execute();

        // Simulate a GET request
        $crawler = $client->request('GET', '/');

        // Assert successful response
        $this->assertResponseIsSuccessful();

        // Verify message for no themes
        $this->assertSelectorTextContains('p', 'No themes found.'); // Update text if needed
    }

    public function testHomePageWithLoggedInUser(): void
    {
        $client = static::createClient();
        $entityManager = self::getContainer()->get('doctrine')->getManager();

        // Mock a user and persist it
        $user = (new User())
            ->setUsername('testuser') // Ensure username is set
            ->setEmail('testuser@example.com')
            ->setPassword('password'); // Hash the password if necessary

        $entityManager->persist($user);
        $entityManager->flush();

        // Simulate login
        $client->loginUser($user);

        // Simulate a GET request to the home page
        $crawler = $client->request('GET', '/');

        // Assert successful response
        $this->assertResponseIsSuccessful();

        // Check the page reflects the user's login status
        $responseContent = $client->getResponse()->getContent();
        $this->assertStringContainsString('Welcome back', $responseContent); // Adjust template text as necessary
    }
}