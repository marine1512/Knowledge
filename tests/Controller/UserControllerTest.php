<?php

namespace App\Tests\Controller;

use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test creating an admin user (createUser).
     */
    public function testCreateAdminUser(): void
    {
        // Mock EntityManager and EntityRepository
        $mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $mockRepository = $this->createMock(EntityRepository::class);
        $mockPasswordHasher = $this->createMock(UserPasswordHasherInterface::class);

        // Simulate no existing admin user by returning null from findOneBy
        $mockRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        // Configure EntityManager to return the mocked repository
        $mockEntityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($mockRepository);

        // Simulate password hashing
        $mockPasswordHasher->expects($this->once())
            ->method('hashPassword')
            ->willReturn('hashed_password');

        // Expect persist and flush
        $mockEntityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(User::class));
        $mockEntityManager->expects($this->once())
            ->method('flush');

        // Perform the controller action
        $controller = new \App\Controller\UserController();
        $response = $controller->createUser($mockEntityManager, $mockPasswordHasher);

        // Assert response
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Utilisateur admin créé avec succès', $response->getContent());
    }

    /**
     * Test creating a client user (createClient).
     */
    public function testCreateClientUser(): void
    {
        // Mock EntityManager and EntityRepository
        $mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $mockRepository = $this->createMock(EntityRepository::class);
        $mockPasswordHasher = $this->createMock(UserPasswordHasherInterface::class);

        // Simulate no existing client user by returning null from findOneBy
        $mockRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        // Configure EntityManager to return the mocked repository
        $mockEntityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($mockRepository);

        // Simulate password hashing
        $mockPasswordHasher->expects($this->once())
            ->method('hashPassword')
            ->willReturn('hashed_password');

        // Expect persist and flush
        $mockEntityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(User::class));
        $mockEntityManager->expects($this->once())
            ->method('flush');

        // Perform the controller action
        $controller = new \App\Controller\UserController();
        $response = $controller->createClient($mockEntityManager, $mockPasswordHasher);

        // Assert response
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Utilisateur client créé avec succès', $response->getContent());
    }

    /**
     * Test listing users (index).
     */
    public function testIndex(): void
    {
        // Mock EntityManager and EntityRepository
        $mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $mockRepository = $this->createMock(EntityRepository::class);

        // Simulate a list of users returned by findAll
        $mockUser = (new User())
            ->setUsername('admin')
            ->setEmail('admin@example.com');
        $mockRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$mockUser]);

        // Configure EntityManager to return the mocked repository
        $mockEntityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($mockRepository);

        // Perform the controller action
        $controller = new \App\Controller\UserController();
        $response = $controller->index($mockEntityManager);

        // Assert response
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('admin', $response->getContent());
    }
}