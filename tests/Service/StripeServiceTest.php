<?php

namespace App\Tests\Service;

use App\Service\StripeService;
use PHPUnit\Framework\TestCase;
use Mockery;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Exception\InvalidRequestException;

class StripeServiceTest extends TestCase
{
    private string $stripeApiSecret;

    protected function setUp(): void
    {
        $this->stripeApiSecret = 'test_secret_key'; // Simulated API key for tests
    }

    protected function tearDown(): void
    {
        Mockery::close(); // Cleanly close Mockery mocks.
    }

    public function testCreateCheckoutSessionSuccess(): void
    {
        $cartItems = [
            [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => 'Test Product'],
                    'unit_amount' => 1500,
                ],
                'quantity' => 2,
            ],
        ];
        $successUrl = 'https://example.com/success';
        $cancelUrl = 'https://example.com/cancel';

        // Mock StripeSession and its create method
        $mockStripeSession = Mockery::mock('alias:' . StripeSession::class);
        $mockStripeSession->shouldReceive('create')
            ->once()
            ->with([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => ['name' => 'Test Product'],
                            'unit_amount' => 1500,
                        ],
                        'quantity' => 2,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ])
            ->andReturn(new StripeSession()); // Simulate a successful session creation

        // Inject the mock into the StripeService
        $stripeService = new StripeService($this->stripeApiSecret);

        // Call the method and check the result
        $result = $stripeService->createCheckoutSession($cartItems, $successUrl, $cancelUrl);

        $this->assertInstanceOf(StripeSession::class, $result);
    }

    public function testCreateCheckoutSessionStripeApiError(): void
    {
        $this->expectException(InvalidRequestException::class);

        $cartItems = [
            [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => 'Faulty Product'],
                    'unit_amount' => 999,
                ],
                'quantity' => 1,
            ],
        ];

        // Simulate a Stripe API exception
        $mockStripeSession = Mockery::mock('alias:' . StripeSession::class);
        $mockStripeSession->shouldReceive('create')
            ->once()
            ->andThrow(new InvalidRequestException('Stripe API Error')); // Throw an error

        // Inject the mock into the StripeService
        $stripeService = new StripeService($this->stripeApiSecret);

        // Call and expect the exception
        $stripeService->createCheckoutSession($cartItems, 'https://example.com/success', 'https://example.com/cancel');
    }
}