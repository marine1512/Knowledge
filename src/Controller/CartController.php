<?php

namespace App\Controller;

use App\Entity\Cursus;
use App\Entity\Lecon;
use App\Entity\UserPurchase;
use App\Service\CartService;
use App\Service\StripeService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class CartController
 *
 * Handles operations related to the shopping cart, including adding items,
 * removing items, and checking out.
 */
class CartController extends AbstractController
{
    /**
     * Displays the contents of the shopping cart.
     * * @param Request $request The current HTTP request.
     * @return Response The rendered cart view.
     */
    #[Route('/cart', name: 'cart', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $cart = $request->getSession()->get('cart', []);

        $totalPrice = array_reduce($cart, function ($total, $item) {
            return $total + $item['prix'] * $item['quantité'];
        }, 0);

        return $this->render('panier/index.html.twig', [
            'cart' => $cart,
            'totalPrice' => $totalPrice,
        ]);
    }

    /**
     * Adds a lesson (Lecon) to the shopping cart.
     *
     * @param int $id The ID of the lesson to add.
     * @param Request $request The current HTTP request.
     * @param ManagerRegistry $doctrine The Doctrine manager registry.
     * @return Response A redirect response to the cart page.
     */
    #[Route('/cart/add/lecon/{id}', name: 'cart_add_lecon', methods: ['POST'])]
    public function addLeconToCart($id, Request $request, ManagerRegistry $doctrine): Response
    {
        $lecon = $doctrine->getRepository(Lecon::class)->find($id);

        if (!$lecon) {
            $this->addFlash('error', 'Leçon non trouvée.');
            return $this->redirectToRoute('lecon_list');
        }

        $session = $request->getSession();
        $cart = $session->get('cart', []);

        $itemKey = 'lecon-' . $id;

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantité'] += 1;
        } else {
            $cart[$itemKey] = [
                'id' => $lecon->getId(),
                'nom' => $lecon->getNom(),
                'prix' => $lecon->getPrix(),
                'quantité' => 1,
            ];
        }

        $session->set('cart', $cart);

        $this->addFlash('success', 'Leçon ajoutée au panier avec succès.');
        return $this->redirectToRoute('cart');
    }

    /**
     * Adds a cursus (course) to the shopping cart.
     *
     * @param int $id The ID of the cursus to add.
     * @param Request $request The current HTTP request.
     * @param ManagerRegistry $doctrine The Doctrine manager registry.
     * @return Response A redirect response to the cart page.
     */
    #[Route('/cart/add/cursus/{id}', name: 'cart_add_cursus', methods: ['POST'])]
    public function addCursusToCart($id, Request $request, ManagerRegistry $doctrine): Response
    {
        $cursus = $doctrine->getRepository(Cursus::class)->find($id);

        if (!$cursus) {
            $this->addFlash('error', 'Cursus non trouvé.');
            return $this->redirectToRoute('cursus_list');
        }

        $session = $request->getSession();
        $cart = $session->get('cart', []);

        $itemKey = 'cursus-' . $id;

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantité'] += 1;
        } else {
            $cart[$itemKey] = [
                'id' => $cursus->getId(),
                'nom' => $cursus->getNom(),
                'prix' => $cursus->getPrix(),
                'quantité' => 1,
            ];
        }

        $session->set('cart', $cart);

        $this->addFlash('success', 'Cursus ajouté au panier avec succès.');
        return $this->redirectToRoute('cart');
    }

    /**
     * Removes an item from the shopping cart.
     *
     * @param int $id The ID of the item to remove.
     * @param Request $request The current HTTP request.
     * @return Response A redirect response to the cart page.
     */ 
    #[Route('/cart/remove/{id}', name: 'cart_remove', methods: ['POST'])]
    public function removeFromCart($id, Request $request): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);
        $this->addFlash('success', 'Produit retiré du panier avec succès.');

        return $this->redirectToRoute('cart');
    }

    /**
     * Initiates the checkout process by creating a Stripe checkout session.
     * @param CartService $cartService The service for managing the shopping cart.
     * @param StripeService $stripeService The service for handling Stripe payments.
     * @return Response A redirect response to the Stripe checkout page.
     */
     #[Route('/checkout', name: 'app_checkout')]
    public function checkout(CartService $cartService, StripeService $stripeService): Response
    {
        $cart = $cartService->getFullCart();

        if (empty($cart)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('cart');
        }

        $cartItems = [];
        foreach ($cart as $item) {
            if (!isset($item['item']) || !method_exists($item['item'], 'getNom') || !method_exists($item['item'], 'getPrix')) {
                throw new \LogicException("Le panier contient un élément invalide.");
            }

            $cartItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['item']->getNom(),
                    ],
                    'unit_amount' => (int) ($item['item']->getPrix() * 100),
                ],
                'quantity' => $item['quantity'],
            ];
        }

        $session = $stripeService->createCheckoutSession(
            $cartItems,
            $this->generateUrl('payment_success', [], false),
            $this->generateUrl('payment_cancel', [], false)
        );

        return $this->redirect($session->url, 303); 
    }

    /**
     * Handles the success callback from Stripe after a successful payment.
     *
     * @param CartService $cartService The service for managing the shopping cart.
     * @param EntityManagerInterface $entityManager The Doctrine entity manager.
     * @return Response A response indicating the success of the payment.
     */
   #[Route('/success', name: 'payment_success')]
public function success(CartService $cartService, EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();
    if (!$user) {
        $this->addFlash('error', 'Vous devez être connecté pour finaliser votre achat.');
        return $this->redirectToRoute('cart');
    }

    $cart = $cartService->getFullCart();

    if (empty($cart)) {
        $this->addFlash('error', 'Votre panier est vide. Aucun achat n’a été réalisé.');
        return $this->redirectToRoute('cart');
    }

    foreach ($cart as $cartItem) {
        $item = $cartItem['item'];

        if ($item instanceof Lecon) {
            $userPurchase = new UserPurchase();
            $userPurchase->setUser($user);
            $userPurchase->setLecon($item);
            $entityManager->persist($userPurchase);
        }

        if ($item instanceof Cursus) {
            $userPurchase = new UserPurchase();
            $userPurchase->setUser($user);
            $userPurchase->setCursus($item);
            $entityManager->persist($userPurchase);

            foreach ($item->getLecons() as $lecon) {
                $leconPurchase = new UserPurchase();
                $leconPurchase->setUser($user);
                $leconPurchase->setLecon($lecon);
                $entityManager->persist($leconPurchase);
            }
        }
    }

    $entityManager->flush(); 
    $cartService->clear();

    $this->addFlash('success', 'Votre paiement a été effectué avec succès. Vos accès ont été ajoutés à votre compte.');

    return $this->render('panier/success.html.twig', [
        'message' => 'Merci pour votre achat ! Vos contenus sont désormais accessibles.',
    ]);
}

    /**
     * Handles the cancellation of a payment.
     *
     * @return Response A response indicating the cancellation of the payment.
     */
    #[Route('/cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
        $this->addFlash('error', 'Votre paiement a été annulé.');
        return $this->redirectToRoute('cart');
    }
}