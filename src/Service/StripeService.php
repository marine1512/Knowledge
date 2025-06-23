<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

/**
 * Class StripeService
 *
 * Handles interactions with the Stripe API for payment processing.
 */
class StripeService
{
    private string $stripeApiSecret;
    
    public function __construct(string $stripeApiSecret)
    {
        $this->stripeApiSecret = $stripeApiSecret;
        Stripe::setApiKey($this->stripeApiSecret);
    }

    /**
     * Creates a Stripe Checkout session for the provided cart items.
     *
     * @param array $cartItems An array of cart items, each containing price data and quantity.
     * @param string $successUrl The URL to redirect to upon successful payment.
     * @param string $cancelUrl The URL to redirect to if the payment is cancelled.
     * @return StripeSession The created Stripe Checkout session.
     * @throws \InvalidArgumentException If the cart item data is invalid.
     */
    public function createCheckoutSession(array $cartItems, string $successUrl, string $cancelUrl): StripeSession
    {
        $lineItems = [];

        foreach ($cartItems as $cartItem) {
            if (!isset($cartItem['price_data'], $cartItem['quantity'])) {
                throw new \InvalidArgumentException('Les informations du produit sont invalides');
            }

            $priceData = $cartItem['price_data'];
            $lineItems[] = [
                'price_data' => [
                    'currency' => $priceData['currency'], 
                    'product_data' => [
                        'name' => $priceData['product_data']['name'],
                    ],
                    'unit_amount' => (int) $priceData['unit_amount'], 
                ],
                'quantity' => (int) $cartItem['quantity'], 
            ];
        }

        
        return StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);
    }
}