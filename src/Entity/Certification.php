<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Certification
 *
 * Represents a certification entity linked to a specific theme.
 * Contains properties for the certification ID, associated theme, and creation date.
 */
#[ORM\Entity]
class Certification
{
    /**
     * @var int|null The unique identifier for the certification.
     */
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    /**
     * @var Theme|null The theme associated with this certification.
     * This is a one-to-one relationship, meaning each certification is linked to exactly one theme.
     */
    #[ORM\OneToOne(targetEntity: "App\Entity\Theme", inversedBy: "certification")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Theme $theme = null;

    /**
     * @var \DateTimeInterface|null The date when the certification was created.
     * This is automatically set to the current date when a new certification is created.
     */
    #[ORM\Column(type: "date")]
    private ?\DateTimeInterface $createdAt = null;

    /**
     * Certification constructor.
     * Initializes the createdAt property to the current date and time.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable(); // Définition automatique de la date de création
    }

    /**
     * Getters and setters for the Certification entity properties.
     */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(Theme $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}