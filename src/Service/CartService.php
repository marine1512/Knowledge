<?php

namespace App\Service;

use App\Repository\CursusRepository;
use App\Repository\LeconRepository;
use App\Entity\Cursus;
use App\Entity\Lecon;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class CartService
 *
 * Manages the shopping cart functionality, allowing users to add, view, and remove items.
 */
class CartService 
{
    /**
     * @param RequestStack $requestStack The request stack to manage session.
     * @param CursusRepository $cursusRepository Repository to fetch cursus items.
     * @param LeconRepository $leconRepository Repository to fetch lesson items.
     */
    public function __construct(
        private RequestStack $requestStack,
        private CursusRepository $cursusRepository,
        private LeconRepository $leconRepository,
    ) {}

    /**
     * Retrieves the current session.
     *
     * @return SessionInterface The current session.
     */
    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }

    /**
     * Adds an item to the cart.
     *
     * @param int $id The ID of the item to add.
     * @param string $type The type of item ('cursus' or 'lecon').
     */
    public function add(int $id, string $type): void
    {
        $cart = $this->getSession()->get('cart', []);
        $key = $type . '-' . $id;
        $cart[$key] = ($cart[$key] ?? 0) + 1;
        $this->getSession()->set('cart', $cart);
    }

    /**
     * Checks if the cart is empty.
     *
     * @return bool True if the cart is empty, false otherwise.
     */
    public function getFullCart(): array
    {
        $cart = $this->getSession()->get('cart', []);
        $fullCart = [];

        foreach ($cart as $key => $quantity) {
            [$type, $id] = explode('-', $key);

            if ($type === 'cursus') {
                $item = $this->cursusRepository->find($id);
            } elseif ($type === 'lecon') {
                $item = $this->leconRepository->find($id);
            } else {
                continue;
            }
            // Validate item type
            if ($item === null || !($item instanceof Cursus || $item instanceof Lecon)) {
                unset($cart[$key]);
                $this->getSession()->set('cart', $cart);
                continue;
            }

            $fullCart[] = [
                'item' => $item,
                'quantity' => $quantity,
            ];
        }

        return $fullCart;
    }

    /**
     * Retrieves the total price of the cart.
     *
     * @return float The total price of all items in the cart.
     */
    public function clear(): void
    {
        $this->getSession()->remove('cart');
    }

    /**
     * Removes an item from the cart.
     *
     * @param string $key The key of the item to remove (e.g., 'cursus-1' or 'lecon-2').
     */
    public function remove(string $key): void
    {
        $cart = $this->getSession()->get('cart', []);

        if (isset($cart[$key])) {
            unset($cart[$key]);
            $this->getSession()->set('cart', $cart);
        }
    }
}