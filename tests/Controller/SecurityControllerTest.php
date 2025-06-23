<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    /**
     * Test accessing the login page.
     */
    public function testLoginPage(): void
    {
        $client = static::createClient();

        // Perform a GET request to /login
        $crawler = $client->request('GET', '/login');

        // Assert the response status code is 200 (OK)
        $this->assertResponseStatusCodeSame(200);

        // Check if the login form is displayed
        $this->assertSelectorExists('form[action="/login_check"]');
    }

    /**
     * Test login with valid credentials.
     */
    public function testLoginWithValidCredentials(): void
    {
        $client = static::createClient();

        // Perform a GET request to /login to get the form
        $crawler = $client->request('GET', '/login');

        // Select the form and fill it with valid credentials
        $form = $crawler->selectButton('Log in')->form([
            '_username' => 'valid_user', // Replace with a valid test username
            '_password' => 'valid_password', // Replace with a valid test password
        ]);

        // Submit the form
        $client->submit($form);

        // Follow the redirection after a successful login
        $client->followRedirect();

        // Assert that we are redirected to the expected page (e.g., home page)
        $this->assertRouteSame('home');

        // Optionally, assert that the user is authenticated
        $this->assertStringContainsString('Welcome', $client->getResponse()->getContent());
    }

    /**
     * Test login with invalid credentials.
     */
    public function testLoginWithInvalidCredentials(): void
    {
        $client = static::createClient();

        // Perform a GET request to /login to get the form
        $crawler = $client->request('GET', '/login');

        // Select the form and fill it with invalid credentials
        $form = $crawler->selectButton('Log in')->form([
            '_username' => 'invalid_user',
            '_password' => 'invalid_password',
        ]);

        // Submit the form
        $client->submit($form);

        // Assert that the response status code is 200 (login page is shown again)
        $this->assertResponseStatusCodeSame(200);

        // Assert that an error message is displayed
        $this->assertSelectorExists('.alert-danger'); // Adjust the selector if necessary
    }

    /**
     * Test login redirection when already authenticated.
     */
    public function testRedirectWhenAuthenticated(): void
    {
        $client = static::createClient();
        $client->loginUser($this->createAuthenticatedUser());

        // Perform a GET request to /login while authenticated
        $client->request('GET', '/login');

        // Assert redirection to the home route
        $this->assertResponseRedirects('/'); // Adjust the path if necessary
    }

    /**
     * Create a mock user for authentication tests (if necessary).
     */
    private function createAuthenticatedUser()
    {
        // Create or retrieve an authenticated User object
        // Replace "App\Entity\User" with your actual User class
        $user = new \App\Entity\User();
        $user->setUsername('auth_user');
        $user->setPassword('password'); // Use proper password encoding
        $user->setRoles(['ROLE_USER']);
        return $user;
    }
}