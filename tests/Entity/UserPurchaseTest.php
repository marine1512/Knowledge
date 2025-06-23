<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Lecon;
use App\Entity\Cursus;
use App\Entity\UserPurchase;
use PHPUnit\Framework\TestCase;

class UserPurchaseTest extends TestCase
{
    public function testProperties(): void
    {
        $purchase = new UserPurchase();

        // Test default value for isValidated
        $this->assertFalse($purchase->isValidated());

        // Test setting and getting isValidated
        $purchase->setIsValidated(true);
        $this->assertTrue($purchase->isValidated());
    }

    public function testUserRelationship(): void
    {
        $user = $this->createMock(User::class); // Mock the User entity
        $purchase = new UserPurchase();

        // Test setting the user
        $purchase->setUser($user);
        $this->assertSame($user, $purchase->getUser());

        // Test unsetting the user
        $purchase->setUser(null);
        $this->assertNull($purchase->getUser());
    }

    public function testLeconRelationship(): void
    {
        $lecon = $this->createMock(Lecon::class); // Mock the Lecon entity
        $purchase = new UserPurchase();

        // Test setting the lesson
        $purchase->setLecon($lecon);
        $this->assertSame($lecon, $purchase->getLecon());

        // Test unsetting the lesson
        $purchase->setLecon(null);
        $this->assertNull($purchase->getLecon());
    }

    public function testCursusRelationship(): void
    {
        $cursus = $this->createMock(Cursus::class); // Mock the Cursus entity
        $purchase = new UserPurchase();

        // Test setting the cursus
        $purchase->setCursus($cursus);
        $this->assertSame($cursus, $purchase->getCursus());

        // Test unsetting the cursus
        $purchase->setCursus(null);
        $this->assertNull($purchase->getCursus());
    }
}