<?php

namespace App\Tests\Service;

use App\Entity\Cursus;
use App\Entity\Lecon;
use App\Repository\CursusRepository;
use App\Repository\LeconRepository;
use App\Service\CartService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartServiceTest extends TestCase
{
    private $cartService;
    private $session;
    private $cursusRepository;
    private $leconRepository;

    protected function setUp(): void
    {
        $this->session = $this->createMock(SessionInterface::class);
        $requestStack = $this->createMock(RequestStack::class);

        // Mocking session retrieval
        $requestStack->method('getSession')->willReturn($this->session);

        $this->cursusRepository = $this->createMock(CursusRepository::class);
        $this->leconRepository = $this->createMock(LeconRepository::class);

        $this->cartService = new CartService(
            $requestStack,
            $this->cursusRepository,
            $this->leconRepository
        );
    }

    public function testAddItemToCart(): void
    {
        // Mock session data
        $this->session->method('get')->with('cart', [])->willReturn([]);

        // Expect session to save the updated cart
        $this->session->expects($this->once())
            ->method('set')
            ->with('cart', ['cursus-1' => 1]);

        // Add an item to the cart
        $this->cartService->add(1, 'cursus');
    }

    public function testGetFullCart(): void
    {
        $cursus = new Cursus(); // Create fake Cursus entity
        $lecon = new Lecon(); // Create fake Lecon entity

        // Set expectations for the repositories
        $this->cursusRepository->method('find')->with(1)->willReturn($cursus);
        $this->leconRepository->method('find')->with(2)->willReturn($lecon);

        // Mock session data with cart items
        $this->session->method('get')->with('cart', [])->willReturn([
            'cursus-1' => 2,
            'lecon-2' => 1,
        ]);

        // Get the full cart
        $fullCart = $this->cartService->getFullCart();

        $this->assertCount(2, $fullCart); // Two items in the cart

        $this->assertEquals($cursus, $fullCart[0]['item']);
        $this->assertEquals(2, $fullCart[0]['quantity']);

        $this->assertEquals($lecon, $fullCart[1]['item']);
        $this->assertEquals(1, $fullCart[1]['quantity']);
    }

public function testGetFullCartHandlesInvalidItems(): void
{
    // Mock the repositories to return null (invalid items)
    $this->cursusRepository->method('find')->with(1)->willReturn(null);
    $this->leconRepository->method('find')->with(2)->willReturn(null);

    // Mock session data with cart items
    $this->session->method('get')->with('cart', [])->willReturn([
        'cursus-1' => 2,
        'lecon-2' => 1,
    ]);

    // Expect the session to update and remove invalid items
    $this->session->expects($this->once())
        ->method('set')
        ->with('cart', []); // Expect an empty cart after cleaning up invalid items.

    // Get the full cart and verify it’s now empty
    $fullCart = $this->cartService->getFullCart();
    $this->assertEmpty($fullCart); // There should be no valid items left in the cart.
}

    public function testRemoveItemFromCart(): void
    {
        // Mock session with an initial cart
        $this->session->method('get')->with('cart', [])->willReturn([
            'cursus-1' => 1,
            'lecon-2' => 1,
        ]);

        // Expect session to save the updated cart after removing an item
        $this->session->expects($this->once())
            ->method('set')
            ->with('cart', [
                'lecon-2' => 1, // cursus-1 is removed, only lecon-2 remains
            ]);

        // Remove an item from the cart
        $this->cartService->remove('cursus-1');
    }

    public function testClearCart(): void
    {
        // Expect the session to remove the entire cart
        $this->session->expects($this->once())
            ->method('remove')
            ->with('cart');

        // Clear the cart
        $this->cartService->clear();
    }
}