<?php

namespace App\Tests\Entity;

use App\Entity\Cursus;
use App\Entity\Lecon;
use App\Entity\Theme;
use PHPUnit\Framework\TestCase;

class CursusTest extends TestCase
{
    public function testProperties(): void
    {
        $cursus = new Cursus();

        // Test setting and getting the "nom" property
        $cursus->setNom('PHP Basics');
        $this->assertEquals('PHP Basics', $cursus->getNom());

        // Test setting and getting the "prix" property
        $cursus->setPrix(19.99);
        $this->assertEquals(19.99, $cursus->getPrix());

        // Test setting and getting "isValidated" property
        $cursus->setIsValidated(true);
        $this->assertTrue($cursus->isValidated());
    }

    public function testThemeRelation(): void
    {
        $cursus = new Cursus();
        $theme = $this->createMock(Theme::class);

        // Ensure the theme can be set and retrieved correctly
        $cursus->setTheme($theme);
        $this->assertSame($theme, $cursus->getTheme());
    }

    public function testLeconsRelation(): void
    {
        $cursus = new Cursus();
        $lecon = new Lecon(); // Use a real instance of Lecon

        // Test adding a lesson
        $cursus->addLecon($lecon);
        $this->assertCount(1, $cursus->getLecons());
        $this->assertTrue($cursus->getLecons()->contains($lecon));

        // Assert that the Lecon is aware of its relationship with the Cursus
        $this->assertEquals($cursus, $lecon->getCursus());

        // Test removing a lesson
        $cursus->removeLecon($lecon);
        $this->assertCount(0, $cursus->getLecons());
        $this->assertFalse($cursus->getLecons()->contains($lecon));

        // Assert that the Lecon is dissociated from the Cursus
        $this->assertNull($lecon->getCursus());
    }
}