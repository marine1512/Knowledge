<?php

namespace App\Tests\Controller;

use App\Entity\Cursus;
use App\Entity\Lecon;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Stripe\Checkout\Session;

class CartControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();

        // Set up a mock session with cart items
        $session = $client->getContainer()->get('session.factory')->createSession();
        $session->set('cart', [
            'lecon-1' => ['id' => 1, 'nom' => 'Test Leçon', 'prix' => 10.0, 'quantité' => 2],
            'cursus-1' => ['id' => 1, 'nom' => 'Test Cursus', 'prix' => 20.0, 'quantité' => 1],
        ]);
        $session->save();

        $cookie = $client->getContainer()->get('framework.test.client.cookiejar');
        $cookie->set($session->getName(), $session->getId());

        // Request the cart route
        $crawler = $client->request('GET', '/cart');

        // Assert that the response is successful
        $this->assertResponseIsSuccessful();

        // Verify cart items and total price calculation
        $this->assertSelectorTextContains('.cart-item-name', 'Test Leçon');
        $this->assertSelectorTextContains('.cart-item-name', 'Test Cursus');
        $this->assertSelectorTextContains('.total-price', '40.00'); // 2 * 10.0 + 1 * 20.0 = 40.00
    }

    /**
     * Creates a mock User entity for authentication in tests.
     */
    private static function createMockUser()
    {
        $user = new \App\Entity\User();
        $user->setEmail('testuser@example.com');
        $user->setPassword('password'); // You may want to hash this if your User entity requires it
        $user->setRoles(['ROLE_USER']);
        return $user;
    }

    public function testAddLeconToCart(): void
    {
        $client = static::createClient();
        $entityManager = self::getContainer()->get('doctrine')->getManager();

        // Create and persist a mock Lecon
        $lecon = new Lecon();
        $lecon->setNom('Test Leçon')->setPrix(15.0);
        $entityManager->persist($lecon);
        $entityManager->flush();

        // Add Lecon to the cart
        $client->request('POST', '/cart/add/lecon/' . $lecon->getId());

        // Assert redirection to the cart page
        $this->assertResponseRedirects('/cart');

        // Follow the redirection
        $client->followRedirect();

        // Assert that the item is in the cart
        $this->assertSelectorTextContains('.cart-item-name', 'Test Leçon');
        $this->assertSelectorTextContains('.cart-item-quantity', '1');
    }

    public function testAddCursusToCart(): void
    {
        $client = static::createClient();
        $entityManager = self::getContainer()->get('doctrine')->getManager();

        // Create and persist a mock Cursus
        $cursus = new Cursus();
        $cursus->setNom('Test Cursus')->setPrix(50.0);
        $entityManager->persist($cursus);
        $entityManager->flush();

        // Add Cursus to the cart
        $client->request('POST', '/cart/add/cursus/' . $cursus->getId());

        // Assert redirection to the cart page
        $this->assertResponseRedirects('/cart');

        // Follow the redirection
        $client->followRedirect();

        // Assert that the item is in the cart
        $this->assertSelectorTextContains('.cart-item-name', 'Test Cursus');
        $this->assertSelectorTextContains('.cart-item-quantity', '1');
    }

    public function testRemoveFromCart(): void
    {
        $client = static::createClient();

        // Add item to session
        $session = $client->getContainer()->get('session.factory')->createSession();
        $session->set('cart', [
            'lecon-1' => ['id' => 1, 'nom' => 'Test Leçon', 'prix' => 10.0, 'quantité' => 1],
        ]);
        $session->save();

        $cookie = $client->getContainer()->get('framework.test.client.cookiejar');
        $cookie->set($session->getName(), $session->getId());

        // Remove item from cart
        $client->request('POST', '/cart/remove/lecon-1');

        // Assert redirection to the cart
        $this->assertResponseRedirects('/cart');

        // Follow the redirection
        $client->followRedirect();

        // Assert that the cart is empty
        $this->assertSelectorTextNotContains('.cart-item-name', 'Test Leçon');
    }

    public function testCheckoutWithEmptyCart(): void
    {
        $client = static::createClient();

        // Ensure the session is empty
        $client->getContainer()->get('session.factory')->createSession()->clear();

        // Attempt checkout with empty cart
        $client->request('GET', '/checkout');

        // Assert redirection with error flash message
        $this->assertResponseRedirects('/cart');

        // Follow the redirection and verify error message
        $client->followRedirect();
        $this->assertSelectorTextContains('.flash-error', 'Votre panier est vide.');
    }

    public function testCheckoutWithItems(): void
    {
        $client = static::createClient();

        // Mock a CartService to include items
        $cartService = $client->getContainer()->get('App\Service\CartService');
        $cartService->setCart([
            ['item' => new Lecon(), 'quantity' => 2],
        ]);

        // Mock StripeService to intercept redirect
        $stripeService = $client->getContainer()->get('App\Service\StripeService');
        $stripeService->createCheckoutSession = function () {
            return (object)['url' => 'https://stripe-fake-checkout/session'];
        };

        // Attempt checkout
        $client->request('GET', '/checkout');

        // Assert redirection to the Stripe page
        $this->assertResponseRedirects('https://stripe-fake-checkout/session');
    }

    public function testPaymentSuccess(): void
    {
        $client = static::createClient();

        // Mock a logged-in user
        $client->loginUser(self::createMockUser());

        // Ensure session includes cart items
        $client->getContainer()->get('App\Service\CartService')->setCart([
            ['item' => new Lecon(), 'quantity' => 1],
        ]);

        // Assert payment processing and database updates
        $client->request('GET', '/success');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.success', 'Votre paiement a été effectué avec succès.');
    }

    public function testPaymentCancel(): void
    {
        $client = static::createClient();

        // Attempt payment cancellation
        $client->request('GET', '/cancel');

        // Assert redirection and flash error message
        $this->assertResponseRedirects('/cart');
        $client->followRedirect();
        $this->assertSelectorTextContains('.flash-error', 'Votre paiement a été annulé.');
    }
}