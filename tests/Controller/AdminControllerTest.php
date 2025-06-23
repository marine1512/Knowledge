<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminControllerTest extends WebTestCase
{
    /**
     * Test access to the admin dashboard for non-admin users.
     */
    public function testDashboardAccessDeniedForNonAdmin(): void
    {
        $client = static::createClient();

        // Simulate a logged-in user without ROLE_ADMIN
        $nonAdminUser = $this->createSerializableMockUser(['ROLE_USER']);
        $client->loginUser($nonAdminUser);

        // Request the dashboard route
        $client->request('GET', '/admin');

        // Assert access is denied (403 Forbidden)
        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * Test access to the admin dashboard for admin users.
     */
    public function testDashboardAccessGrantedForAdmin(): void
    {
        $client = static::createClient();

        // Simulate a logged-in admin user
        $adminUser = $this->createSerializableMockUser(['ROLE_ADMIN']);
        $client->loginUser($adminUser);

        // Request the dashboard route
        $client->request('GET', '/admin');

        // Assert access is successful (200 OK)
        $this->assertResponseIsSuccessful();

        // Assert the content of the response
        $this->assertSelectorTextContains('h1', 'AdminController');
    }

    /**
     * Create a serializable mock user with custom roles.
     *
     * @param array $roles Custom user roles.
     * @return UserInterface A mock user object implementing UserInterface.
     */
    private function createSerializableMockUser(array $roles): UserInterface
    {
        return new class($roles) implements UserInterface, \Serializable {
            private array $roles;

            public function __construct(array $roles)
            {
                $this->roles = $roles;
            }

            public function getRoles(): array
            {
                return $this->roles;
            }

            public function getPassword(): ?string
            {
                return null;
            }

            public function getSalt(): ?string
            {
                return null;
            }

            public function getUserIdentifier(): string
            {
                return 'mock_user';
            }

            public function eraseCredentials(): void
            {
                // Do nothing
            }

            public function serialize(): string
            {
                return serialize([
                    'roles' => $this->roles,
                ]);
            }

            public function unserialize($data): void
            {
                $unserialized = unserialize($data);
                $this->roles = $unserialized['roles'] ?? [];
            }
        };
    }
}