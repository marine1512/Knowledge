<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class AccessControllerTest extends WebTestCase
{
    /**
     * Test access to purchased cursus for unauthenticated users.
     */
    public function testAccessPurchasedCursusRedirectsForUnauthenticated(): void
    {
        $client = static::createClient();

        // Access the /mes-achats route as a guest
        $client->request('GET', '/mes-achats');

        // Verify that the user is redirected to the login page
        $this->assertResponseRedirects('/login');
    }

    /**
     * Test access to purchased cursus for users without any purchases.
     */
    public function testAccessPurchasedCursusWithNoPurchases(): void
    {
        $client = static::createClient();

        $user = $this->createMockUser([]);
        $client->loginUser($user);

        // Mock the result of the UserPurchaseRepository
        $mockUserPurchaseRepository = $this->getMockBuilder('App\Repository\UserPurchaseRepository')
            ->disableOriginalConstructor()
            ->onlyMethods(['getPurchasedCursus', 'getPurchasedLecons'])
            ->getMock();

        $mockUserPurchaseRepository->expects($this->once())->method('getPurchasedCursus')->willReturn([]);
        $mockUserPurchaseRepository->expects($this->once())->method('getPurchasedLecons')->willReturn([]);

        // Access the /mes-achats route
        $client->request('GET', '/mes-achats');

        // Verify the user is redirected to the home page
        $this->assertResponseRedirects('/home');
    }

    /**
     * Test access to purchased cursus for users with existing purchases.
     */
    public function testAccessPurchasedCursusWithPurchases(): void
    {
        $client = static::createClient();

        $user = $this->createMockUser(['ROLE_USER']);
        $client->loginUser($user);

        // Mock the result of the UserPurchaseRepository
        $mockUserPurchaseRepository = $this->getMockBuilder('App\Repository\UserPurchaseRepository')
            ->disableOriginalConstructor()
            ->onlyMethods(['getPurchasedCursus', 'getPurchasedLecons'])
            ->getMock();

        $cursus = [
            ['id' => 1, 'name' => 'Cursus Test', 'prix' => '10'],
        ];
        $lecons = [
            ['id' => 1, 'name' => 'Leçon Test', 'prix' => '50'],
        ];

        $mockUserPurchaseRepository->expects($this->once())->method('getPurchasedCursus')->willReturn($cursus);
        $mockUserPurchaseRepository->expects($this->once())->method('getPurchasedLecons')->willReturn($lecons);

        // Access the /mes-achats page
        $client->request('GET', '/mes-achats');

        // Verify the response is successful
        $this->assertResponseIsSuccessful();

        // Verify the purchased cursus and lessons appear on the page
        $this->assertSelectorTextContains('.cursus-name', 'Cursus Test');
        $this->assertSelectorTextContains('.lecon-name', 'Leçon Test');
    }

    /**
     * Test details of a specific valid Cursus.
     */
    public function testDetailCursusWithValidId(): void
    {
        $client = static::createClient();

        // Use a valid cursus ID
        $client->request('GET', '/mes-achats/cursus/1');

        // Verify the response is successful
        $this->assertResponseIsSuccessful();

        // Verify the cursus information is displayed
        $this->assertSelectorExists('.cursus-detail');
    }

    /**
     * Test details of an invalid Cursus ID.
     */
    public function testDetailCursusWithInvalidId(): void
    {
        $client = static::createClient();

        // Use an invalid cursus ID
        $client->request('GET', '/mes-achats/cursus/999');

        // Assert a 404 error is thrown
        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * Test details of a valid lesson.
     */
    public function testDetailLeconWithValidId(): void
    {
        $client = static::createClient();

        // Use a valid leçon ID
        $client->request('GET', '/mes-achats/lecon/1');

        // Verify the response is successful
        $this->assertResponseIsSuccessful();

        // Verify the leçon information is displayed
        $this->assertSelectorExists('.lecon-detail');
    }

    /**
     * Test details of an invalid lesson ID.
     */
    public function testDetailLeconWithInvalidId(): void
    {
        $client = static::createClient();

        // Use an invalid leçon ID
        $client->request('GET', '/mes-achats/lecon/999');

        // Assert a 404 error is thrown
        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * Create a mock user with specified roles.
     *
     * @param array $roles
     * @return UserInterface
     */
    private function createMockUser(array $roles): UserInterface
    {
        return new class($roles) implements UserInterface {
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


            public function eraseCredentials(): void
            {
            }

            public function getUserIdentifier(): string
            {
                return 'mock_user';
            }

            public function getNom(): string
            {
                // Deprecated but still required for older Symfony versions
                return 'mock_user';
            }
        };
    }
}