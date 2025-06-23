<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationForm;
use App\Security\EmailVerifier;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

/**
 * Class RegistrationController
 *
 * Handles user registration, email verification, and confirmation.
 */
class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * Displays the registration form and handles user registration.
     *
     * @param Request $request The current HTTP request.
     * @param UserPasswordHasherInterface $userPasswordHasher The password hasher service.
     * @param EntityManagerInterface $entityManager The entity manager to persist the new user.
     * @return Response The rendered registration form or a redirect response after successful registration.
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $user->setRoles(['ROLE_USER']);
    
            $emailVerificationToken = Uuid::v4()->toRfc4122(); 
            $user->setEmailVerificationToken($emailVerificationToken);
        
            $entityManager->persist($user);
            $entityManager->flush();

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('mignomarine@gmail.com', 'Mail Bot'))
                    ->to((string) $user->getEmail())
                    ->subject('Merci de confirmer votre mail')
                    ->html(
                        '<h1>Bienvenue sur notre application !</h1>' .
                        '<p>Merci de vous être inscrit. Veuillez confirmer votre adresse email en cliquant sur le lien suivant :</p>' .
                        '<a href="http://127.0.0.1:8000/verify/email?token=' . $emailVerificationToken . '">Confirmer mon adresse</a>'
                    )
            );
        
            $this->addFlash('success', 'Un email de confirmation a été envoyé. Veuillez vérifier votre boîte de réception.');
        
            return $this->redirectToRoute('app_register_confirmation');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
    
    /**
     * Verifies the user's email address using the provided token.
     *
     * @param Request $request The current HTTP request.
     * @param EntityManagerInterface $entityManager The entity manager to access the user repository.
     * @param UserAuthenticatorInterface $userAuthenticator The user authenticator service.
     * @param FormLoginAuthenticator $authenticator The form login authenticator service.
     * @return Response A redirect response after successful verification or an error message.
     */
    #[Route('/verify/email', name: 'app_verify_email')]
   public function verifyUserEmail(Request $request, EntityManagerInterface $entityManager, UserAuthenticatorInterface $userAuthenticator, FormLoginAuthenticator $authenticator): Response
{
    $token = $request->query->get('token');

    if (!$token) {
        throw $this->createNotFoundException('Token non fourni pour la vérification.');
    }

    $user = $entityManager->getRepository(User::class)->findOneBy(['emailVerificationToken' => $token]);

    if (!$user) {
        throw $this->createNotFoundException('Aucun utilisateur trouvé pour ce token.');
    }
    if (!$user->isVerified()) {
        $user->setIsVerified(true);
        $user->setIsActive(true);
        $user->setEmailVerificationToken(null); 
        $entityManager->flush(); 
    }
    try {
        return $userAuthenticator->authenticateUser(
            $user,
            $authenticator,
            $request
        );
    } catch (AuthenticationException $e) {
        $this->addFlash('error', 'Une erreur est survenue lors de l\'authentification automatique.');
        return $this->redirectToRoute('app_login'); 
    }

    $this->addFlash('success', 'Votre email a été confirmé avec succès et vous êtes maintenant connecté.');
    return $this->redirectToRoute('home'); 
}

    #[Route('/register/confirmation', name: 'app_register_confirmation')]

    public function confirmEmailNotification(): Response
    {
        return $this->render('registration/confirmation_email.html.twig');
    }
}