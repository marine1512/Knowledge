<?php

namespace App\Tests\Entity;

use App\Entity\Certification;
use App\Entity\Theme;
use PHPUnit\Framework\TestCase;

class CertificationTest extends TestCase
{
    public function testProperties(): void
    {
        $certification = new Certification();

        // Test that the ID is null initially (it will be set by the database)
        $this->assertNull($certification->getId());

        // Test that the createdAt is automatically set on object creation
        $this->assertInstanceOf(\DateTimeInterface::class, $certification->getCreatedAt());
        $this->assertEqualsWithDelta(
            (new \DateTimeImmutable())->getTimestamp(),
            $certification->getCreatedAt()->getTimestamp(),
            2, // Allow a 2-second delta since tests may not execute immediately
        );
    }

    public function testThemeRelation(): void
    {
        $certification = new Certification();
        $theme = $this->createMock(Theme::class); // Mock the Theme entity

        // Test setting and getting the theme
        $certification->setTheme($theme);
        $this->assertSame($theme, $certification->getTheme());
    }

    public function testSetCreatedAt(): void
    {
        $certification = new Certification();
        $date = new \DateTimeImmutable('2023-01-01');

        // Test setting a custom creation date
        $certification->setCreatedAt($date);
        $this->assertEquals($date, $certification->getCreatedAt());
    }
}