<?php

namespace App\Tests\Service;

use App\Entity\Certification;
use App\Entity\Theme;
use App\Service\ThemeService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ThemeServiceTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject&EntityManagerInterface */
    private EntityManagerInterface $entityManagerMock;
    private ThemeService $themeService;

    protected function setUp(): void
    {
        // Mock the EntityManagerInterface
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        // Instantiate the ThemeService with the mocked EntityManager
        $this->themeService = new ThemeService($this->entityManagerMock);
    }

    /**
     * Test that `validerTheme` correctly validates a theme and creates a certification if it doesn't exist.
     */
    public function testValiderThemeCreatesCertification(): void
    {
        // Create a mock theme marked as invalid (not validated)
        $theme = $this->createMock(Theme::class);
        
        // Simulate that the theme is not validated
        $theme->method('isValide')->willReturn(false);

        // Expect the theme's `setValide` to be called with `true`
        $theme->expects($this->once())
            ->method('setValide')
            ->with(true);

        // Simulate that no certification is currently associated with this theme
        $theme->method('getCertification')->willReturn(null);

        // Expect the EntityManager's `persist()` method to be called with a Certification instance
        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Certification::class));

        // Expect the EntityManager's `flush()` method to be called
        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        // Call the method being tested
        $result = $this->themeService->validerTheme($theme);

        // Assert that the method returns true (indicating a certification was created)
        $this->assertTrue($result);
    }

    /**
     * Test that `validerTheme` does nothing when the theme is already validated.
     */
    public function testValiderThemeAlreadyValidated(): void
    {
        // Create a mock theme marked as already validated
        $theme = $this->createMock(Theme::class);

        // Simulate that the theme is already validated
        $theme->method('isValide')->willReturn(true);

        // Expect the EntityManager's `persist` and `flush` methods NOT to be called
        $this->entityManagerMock->expects($this->never())->method('persist');
        $this->entityManagerMock->expects($this->never())->method('flush');

        // Call the method being tested
        $result = $this->themeService->validerTheme($theme);

        // Assert that the method returns false (indicating no certification was created)
        $this->assertFalse($result);
    }

    /**
     * Test that `validerTheme` throws an exception when the theme is null.
     */
public function testValiderThemeThrowsExceptionForNullTheme(): void
{
    // Expect an InvalidArgumentException to be thrown
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage('Le thème fourni est nul ou invalide.');

}

    /**
     * Test that `validerTheme` throws a RuntimeException if flushing fails.
     */
    public function testValiderThemeThrowsExceptionOnFlushError(): void
    {
        // Create a mock theme marked as invalid (not validated)
        $theme = $this->createMock(Theme::class);
        
        // Simulate that the theme is not validated
        $theme->method('isValide')->willReturn(false);

        // Simulate that no certification is currently associated with the theme
        $theme->method('getCertification')->willReturn(null);

        // Expect the theme's `setValide` to be called
        $theme->expects($this->once())->method('setValide')->with(true);

        // Expect `persist` to be called once
        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Certification::class));

        // Simulate an exception being thrown when `flush` is called
        $this->entityManagerMock->expects($this->once())
            ->method('flush')
            ->will($this->throwException(new \Exception('Database error')));

        // Expect a RuntimeException to be thrown
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Échec lors de la validation du thème : Database error');

        // Call the method being tested
        $this->themeService->validerTheme($theme);
    }
}