<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Uid\Uuid;

class RegistrationControllerTest extends WebTestCase
{
    /**
     * Test that the registration form is rendered correctly.
     */
    public function testRegistrationFormIsDisplayed(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        // Assert that the request is successful
        $this->assertResponseIsSuccessful();

        // Assert that the registration form is displayed
        $this->assertSelectorExists('form[action="/register"]');
    }

    /**
     * Test registration with valid data.
     */
    public function testSuccessfulRegistration(): void
    {
        $client = static::createClient();

        // Load registration page
        $crawler = $client->request('GET', '/register');

        // Fill out the registration form with valid data
        $form = $crawler->selectButton('Register')->form([
            'registration_form[email]' => 'test@test.com',
            'registration_form[username]' => 'testuser',
            'registration_form[plainPassword][first]' => 'Password1234',
            'registration_form[plainPassword][second]' => 'Password1234',
        ]);

        // Submit the form
        $client->submit($form);

        // Follow the redirection
        $client->followRedirect();

        // Assert a flash message
        $this->assertSelectorTextContains('.alert-success', 'Un email de confirmation a été envoyé.');

        // Optionally, assert user data has been saved in the database
        $user = self::getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['email' => 'test@test.com']);
        $this->assertNotNull($user);
        $this->assertFalse($user->isVerified()); // User should not yet be verified
    }

    /**
     * Test registration with invalid data (e.g., unmatched passwords).
     */
    public function testRegistrationWithInvalidData(): void
    {
        $client = static::createClient();

        // Load registration page
        $crawler = $client->request('GET', '/register');

        // Fill out the registration form with invalid data
        $form = $crawler->selectButton('Register')->form([
            'registration_form[email]' => 'invalid-email',
            'registration_form[username]' => 'testuser',
            'registration_form[plainPassword][first]' => 'Password1',
            'registration_form[plainPassword][second]' => 'Password2', // Mismatched passwords
        ]);

        // Submit the form
        $client->submit($form);

        // Assert that the response is still the registration form
        $this->assertResponseIsSuccessful();

        // Assert validation errors (adjust selectors to match your form)
        $this->assertSelectorTextContains('.form-error-message', 'The email address is not valid.');
        $this->assertSelectorTextContains('.form-error-message', 'Passwords must match.');
    }

    /**
     * Test email verification with a valid token.
     */
    public function testSuccessfulEmailVerification(): void
    {
        $client = static::createClient();

        // Create a test user with a valid verification token
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $user = new User();
        $user->setEmail('valid@test.com');
        $user->setUsername('validuser');
        $user->setPassword('hashedpassword');
        $user->setEmailVerificationToken(Uuid::v4()->toRfc4122());
        $user->setIsVerified(false); // Initially not verified
        $entityManager->persist($user);
        $entityManager->flush();

        // Make a request to verify the user's email
        $client->request('GET', '/verify/email', ['token' => $user->getEmailVerificationToken()]);

        // Follow redirection after successful verification
        $client->followRedirect();

        // Assert that the user is now verified
        $updatedUser = $entityManager->getRepository(User::class)->findOneBy(['email' => 'valid@test.com']);
        $this->assertTrue($updatedUser->isVerified());

        // Assert success flash message
        $this->assertSelectorTextContains('.alert-success', 'Votre email a été confirmé.');
    }

    /**
     * Test email verification with an invalid token.
     */
    public function testEmailVerificationWithInvalidToken(): void
    {
        $client = static::createClient();

        // Request verification with an invalid token
        $client->request('GET', '/verify/email', ['token' => 'invalid-token']);

        // Assert 404 is returned
        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * Test email verification without a token.
     */
    public function testEmailVerificationWithoutToken(): void
    {
        $client = static::createClient();

        // Request verification with no token
        $client->request('GET', '/verify/email');

        // Assert 404 is returned
        $this->assertResponseStatusCodeSame(404);
    }
}