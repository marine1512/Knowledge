<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 *
 * Handles user management tasks such as creating admin and client users,
 * listing users, and managing user accounts.
 */
class UserController extends AbstractController
{
    /**
     * Displays the admin dashboard.
     *
     * @return Response
     */
    #[Route('/create-admin', name: 'create_admin', methods: ['POST'])]
    public function createUser(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $existingAdmin = $entityManager->getRepository(User::class)
            ->findOneBy(['username' => 'admin']);
    
        if ($existingAdmin) {
            return $this->json([
                'error' => 'Un administrateur existe déjà.'
            ], 400);
        }
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@example.com'); 
        $user->setRoles(['ROLE_ADMIN']);
        
        $hashedPassword = $passwordHasher->hashPassword($user, 'admin');
        $user->setPassword($hashedPassword);
    
        $entityManager->persist($user);
        $entityManager->flush();
    
        return $this->json(['message' => 'Utilisateur admin créé avec succès']);
    }

    /**
     * Creates a client user.
     *
     * @param EntityManagerInterface $entityManager The entity manager to persist the new user.
     * @param UserPasswordHasherInterface $passwordHasher The password hasher service.
     * @return JsonResponse A JSON response indicating success or failure.
     */
    #[Route('/create-client', name: 'create_client', methods: ['POST'])]
    public function createClient(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $existingClient = $entityManager->getRepository(User::class)
            ->findOneBy(['username' => 'client']);
    
        if ($existingClient) {
            return $this->json([
                'error' => 'Un utilisateur avec ce nom existe déjà.'
            ], 400);
        }
    
        $user = new User();
        $user->setUsername('client'); 
        $user->setEmail('client@example.com'); 
        $user->setRoles(['ROLE_USER']);

        $hashedPassword = $passwordHasher->hashPassword($user, 'client');
        $user->setPassword($hashedPassword);
    
        $entityManager->persist($user);
        $entityManager->flush();
    
        return $this->json(['message' => 'Utilisateur client créé avec succès']);
    }

    /**
     * Lists all users.
     *
     * @param EntityManagerInterface $entityManager The entity manager to access the user repository.
     * @return Response The rendered user index view with a list of users.
     */
    #[Route('/user', name: 'app_user_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        return $this->render('admin/user/index.html.twig', [
            'users' => $users, 
        ]);
    }

    /**
     * Creates a new user.
     * @param Request $request The current HTTP request.
     * @param EntityManagerInterface $entityManager The entity manager to persist the new user.
     * @param UserPasswordHasherInterface $passwordHasher The password hasher service.
     * @return Response A redirect response to the user index page or the rendered form view.
     */
     #[Route('/user/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new User();
        $form = $this->createFormBuilder($user)
            ->add('username')
            ->add('email')
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_USER']);
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edits an existing user.
     * @param Request $request The current HTTP request.
     * @param User $user The user entity to edit.
     * @param EntityManagerInterface $entityManager The entity manager to persist changes.
     * @return Response The rendered form view or a redirect response after successful edit.
     */
     #[Route('/user/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createFormBuilder($user)
            ->add('username')
            ->add('email')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a user.
     * @param Request $request The current HTTP request.
     * @param User $user The user entity to delete.
     * @param EntityManagerInterface $entityManager The entity manager to remove the user.
     * @return Response A redirect response to the user index page after deletion.
     */
    #[Route('/user/{id}/delete', name: 'user_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index');
    }
}