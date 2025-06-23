<?php

namespace App\Tests\Entity;

use App\Entity\Cursus;
use App\Entity\Lecon;
use App\Entity\User;
use App\Entity\UserPurchase;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    // Test basic properties such as username, email, active state, etc.
    public function testProperties(): void
    {
        $user = new User();

        // Test username property
        $user->setUsername('testuser');
        $this->assertEquals('testuser', $user->getUsername());

        // Test email property
        $user->setEmail('test@example.com');
        $this->assertEquals('test@example.com', $user->getEmail());

        // Test active state
        $user->setIsActive(true);
        $this->assertTrue($user->isActive());

        // Test email verification state
        $user->setIsVerified(true);
        $this->assertTrue($user->isVerified());

        // Test password property
        $user->setPassword('password');
        $this->assertEquals('password', $user->getPassword());
    }

    // Test roles functionality such as adding roles and ensuring "ROLE_USER" is always included
    public function testRoles(): void
    {
        $user = new User();
        $this->assertEquals(['ROLE_USER'], $user->getRoles()); // Default role is "ROLE_USER"

        $user->setRoles(['ROLE_ADMIN']); // Add another role
        $roles = $user->getRoles();

        $this->assertCount(2, $roles); // Expecting "ROLE_ADMIN" + "ROLE_USER"
        $this->assertContains('ROLE_ADMIN', $roles);
        $this->assertContains('ROLE_USER', $roles);
    }

    // Test the ManyToMany relationship between User and Lecon (Lessons)
    public function testLeconsRelation(): void
    {
        $user = new User();
        $lecon = $this->createMock(Lecon::class); // Use a mock for Lecon entity

        // Test adding a lesson
        $user->addLecon($lecon);
        $this->assertCount(1, $user->getLecons());
        $this->assertTrue($user->getLecons()->contains($lecon));

        // Test removing a lesson
        $user->removeLecon($lecon);
        $this->assertCount(0, $user->getLecons());
        $this->assertFalse($user->getLecons()->contains($lecon));
    }

    // Test the ManyToMany relationship between User and Cursus (Courses)
    public function testCursusRelation(): void
    {
        $user = new User();
        $cursus = $this->createMock(Cursus::class); // Use a mock for Cursus entity

        // Test adding a cursus
        $user->addCursus($cursus);
        $this->assertCount(1, $user->getCursus());
        $this->assertTrue($user->getCursus()->contains($cursus));

        // Test removing a cursus
        $user->removeCursus($cursus);
        $this->assertCount(0, $user->getCursus());
        $this->assertFalse($user->getCursus()->contains($cursus));
    }

    // Test the OneToMany relationship between User and UserPurchase (Purchases)
    public function testPurchasesRelation(): void
    {
        $user = new User();
        $purchase = new UserPurchase(); // Use a concrete object for UserPurchase

        // Test adding a purchase
        $user->addPurchase($purchase);

        // Assert the purchase is added to the user's Purchases collection
        $this->assertCount(1, $user->getPurchases());
        $this->assertTrue($user->getPurchases()->contains($purchase));

        // Ensure the UserPurchase object is aware of the User who owns it
        $this->assertEquals($user, $purchase->getUser());

        // Test removing a purchase
        $user->removePurchase($purchase);

        $this->assertCount(0, $user->getPurchases());
        $this->assertFalse($user->getPurchases()->contains($purchase));

        // Ensure the purchase is dissociated from the User
        $this->assertNull($purchase->getUser());
    }

    // Test the ManyToMany relationship for validated lessons
    public function testValidatedLeconsRelation(): void
    {
        $user = new User();
        $lecon = $this->createMock(Lecon::class); // Use a mock for Lecon

        // Expectation: Ensure addValidatedByUser() is called when adding a validated lesson
        $lecon->expects($this->once())->method('addValidatedByUser')->with($user);

        // Test adding a validated lesson
        $user->addValidatedLecon($lecon);
        $this->assertCount(1, $user->getValidatedLecons());
        $this->assertTrue($user->getValidatedLecons()->contains($lecon));

        // Expectation: Ensure removeValidatedByUser() is called when removing a validated lesson
        $lecon->expects($this->once())->method('removeValidatedByUser')->with($user);

        // Test removing a validated lesson
        $user->removeValidatedLecon($lecon);
        $this->assertCount(0, $user->getValidatedLecons());
        $this->assertFalse($user->getValidatedLecons()->contains($lecon));
    }

    // Test the email verification token functionality
    public function testEmailVerificationToken(): void
    {
        $user = new User();

        // Test setting a token
        $user->setEmailVerificationToken('abcd1234');
        $this->assertEquals('abcd1234', $user->getEmailVerificationToken());

        // Test clearing a token
        $user->setEmailVerificationToken(null);
        $this->assertNull($user->getEmailVerificationToken());
    }

    // Test the getUserIdentifier() function, which should return the user's email
    public function testGetUserIdentifier(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');

        $this->assertEquals('test@example.com', $user->getUserIdentifier());
    }
}