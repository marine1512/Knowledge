<?php

namespace App\Tests\Entity;

use App\Entity\Cursus;
use App\Entity\Certification;
use App\Entity\Theme;
use PHPUnit\Framework\TestCase;

class ThemeTest extends TestCase
{
    // Test basic properties like "nom", "image", and "valide"
    public function testProperties(): void
    {
        $theme = new Theme();

        // Test "nom" property
        $theme->setNom('Technology');
        $this->assertEquals('Technology', $theme->getNom());

        // Test "image" property
        $theme->setImage('image_path.jpg');
        $this->assertEquals('image_path.jpg', $theme->getImage());

        $theme->setImage(null);
        $this->assertNull($theme->getImage());

        // Test "valide" property
        $theme->setValide(true);
        $this->assertTrue($theme->isValide());

        $theme->setValide(false);
        $this->assertFalse($theme->isValide());
    }

    // Test the OneToMany relationship with Cursus
    public function testCursusRelation(): void
{
    $theme = new Theme();
    $cursus = new Cursus(); // Use a real Cursus entity object

    // Test adding a cursus
    $theme->addCursus($cursus);
    $this->assertCount(1, $theme->getCursus());
    $this->assertTrue($theme->getCursus()->contains($cursus));

    // Check that the cursus has the correct theme set
    $this->assertSame($theme, $cursus->getTheme());

    // Test removing a cursus
    $theme->removeCursus($cursus);
    $this->assertCount(0, $theme->getCursus());
    $this->assertFalse($theme->getCursus()->contains($cursus));

    // Check that the cursus theme is null
    $this->assertNull($cursus->getTheme());
}

    // Test the OneToOne relationship with Certification
    public function testCertificationRelation(): void
    {
        $theme = new Theme();
        $certification = $this->createMock(Certification::class); // Create a mock object for Certification

        // Mock the "setTheme" method to handle the bidirectional relation
        $certification->expects($this->once())->method('setTheme')->with($theme);

        // Test setting the certification
        $theme->setCertification($certification);
        $this->assertEquals($certification, $theme->getCertification());

        // Test clearing the certification
        $theme->setCertification(null);
        $this->assertNull($theme->getCertification());
    }

    // Test the default values when the Theme entity is created
    public function testDefaults(): void
    {
        $theme = new Theme();

        // Default "valide" should be false
        $this->assertFalse($theme->isValide());

        // Default "certification" should be null
        $this->assertNull($theme->getCertification());

        // Default "cursus" collection should be empty
        $this->assertCount(0, $theme->getCursus());
    }
}