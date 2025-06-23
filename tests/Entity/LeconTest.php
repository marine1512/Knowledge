<?php

namespace App\Tests\Entity;

use App\Entity\Lecon;
use App\Entity\User;
use App\Entity\Cursus;
use PHPUnit\Framework\TestCase;

class LeconTest extends TestCase
{
    public function testProperties(): void
    {
        $lecon = new Lecon();

        // Test "nom" property
        $lecon->setNom('Lesson Test');
        $this->assertEquals('Lesson Test', $lecon->getNom());

        // Test "prix" property
        $lecon->setPrix(99.99);
        $this->assertEquals(99.99, $lecon->getPrix());
    }

    public function testCursusRelation(): void
    {
        $lecon = new Lecon();
        $cursus = $this->createMock(Cursus::class); // Mock a Cursus entity

        // Test setting a Cursus
        $lecon->setCursus($cursus);
        $this->assertSame($cursus, $lecon->getCursus()); // Ensure the Cursus is correctly set

        // Test that a null Cursus can be assigned
        $lecon->setCursus(null);
        $this->assertNull($lecon->getCursus());
    }

    public function testUsersRelation(): void
    {
        $lecon = new Lecon();
        $user = $this->createMock(User::class); // Mock a User entity

        $this->assertCount(0, $lecon->getUsers()); // Initially should be empty

        // Test adding a User
        $lecon->getUsers()->add($user);
        $this->assertTrue($lecon->getUsers()->contains($user));
        $this->assertCount(1, $lecon->getUsers());

        // Test removing a User
        $lecon->getUsers()->removeElement($user);
        $this->assertFalse($lecon->getUsers()->contains($user));
        $this->assertCount(0, $lecon->getUsers());
    }

    public function testValidatedByUsersRelation(): void
    {
        $lecon = new Lecon();
        $user = $this->createMock(User::class);

        // Configure the User mock to expect `addValidatedLecon` when added
        $user->expects($this->once())
            ->method('addValidatedLecon')
            ->with($lecon);

        // Adding a validated user
        $lecon->addValidatedByUser($user);
        $this->assertTrue($lecon->getValidatedByUsers()->contains($user));
        $this->assertCount(1, $lecon->getValidatedByUsers());

        // Configure the User mock to expect `removeValidatedLecon` when removed
        $user->expects($this->once())
            ->method('removeValidatedLecon')
            ->with($lecon);

        // Removing a validated user
        $lecon->removeValidatedByUser($user);
        $this->assertFalse($lecon->getValidatedByUsers()->contains($user));
        $this->assertCount(0, $lecon->getValidatedByUsers());
    }
}