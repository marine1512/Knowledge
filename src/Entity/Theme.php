<?php

namespace App\Entity;

use App\Repository\ThemeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Certification;

/**
 * Class Theme
 *
 * Represents a theme entity which includes details about the theme,
 * its associated cursus, and certification.
 */
#[ORM\Entity(repositoryClass: ThemeRepository::class)]
class Theme
{
    /**
     * @var int|null The unique identifier for the theme.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string|null The name of the theme.
     * This represents the title or name of the theme.
     */
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var string|null The image associated with the theme.
     * This represents the image URL or path for the theme.
     */
    #[ORM\Column(length: 255, nullable: true)] // Permet de rendre ce champ optionnel
    private ?string $image = null;

    /**
     * @var Collection<Cursus> A collection of cursus associated with this theme.
     * This is a one-to-many relationship, meaning one theme can have multiple cursus.
     */
    #[ORM\OneToMany(mappedBy: 'theme', targetEntity: Cursus::class)]
    private Collection $cursus;

    /**
     * @var bool Indicates whether the theme is valid or not.
     * This is used to track the validity status of the theme.
     */
    #[ORM\Column(type: "boolean")]
       private bool $valide = false;

    /**
     * @var Certification|null The certification associated with this theme.
     * This is a one-to-one relationship, meaning each theme can have one certification.
     */
    #[ORM\OneToOne(mappedBy: 'theme', targetEntity: Certification::class, cascade: ['persist', 'remove'])]
    private ?Certification $certification = null;

    /**
     * Theme constructor.
     * Initializes the cursus collection and sets default values for properties.
     */
    public function __construct()
    {
        $this->cursus = new ArrayCollection();
        $this->valide = false; 
        $this->certification = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getCursus(): Collection
    {
        return $this->cursus;
    }

    public function addCursus(Cursus $cursus): self
    {
        if (!$this->cursus->contains($cursus)) {
            $this->cursus->add($cursus);
            $cursus->setTheme($this);
        }

        return $this;
    }

    public function removeCursus(Cursus $cursus): self
    {
        if ($this->cursus->removeElement($cursus)) {
            // Set the owning side to null (unless already changed)
            if ($cursus->getTheme() === $this) {
                $cursus->setTheme(null);
            }
        }

        return $this;
    }

    public function isValide(): bool
{
    return $this->valide;
}

public function setValide(bool $valide): self
{
    $this->valide = $valide;

    return $this;
}

public function getCertification(): ?Certification
{
    return $this->certification;
}

public function setCertification(?Certification $certification): self
{
    // Gérer la relation bidirectionnelle
    if ($certification && $certification->getTheme() !== $this) {
        $certification->setTheme($this);
    }

    $this->certification = $certification;

    return $this;
}
}
