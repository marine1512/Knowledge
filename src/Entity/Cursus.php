<?php

namespace App\Entity;

use App\Repository\CursusRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Cursus
 *
 * Represents a cursus entity which includes details about the course,
 * its associated theme, lessons, and users enrolled in the cursus.
 */
#[ORM\Entity(repositoryClass: CursusRepository::class)]
class Cursus
{
    /**
     * @var int|null The unique identifier for the cursus.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string|null The name of the cursus.
     */
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var float|null The price of the cursus.
     * This represents the cost associated with enrolling in the cursus.
     */
    #[ORM\Column]
    private ?float $prix = null;

    /**
     * @var Theme|null The theme associated with this cursus.
     * This is a many-to-one relationship, meaning multiple cursus can be linked to one theme.
     */
    #[ORM\ManyToOne(targetEntity: Theme::class, inversedBy: 'cursus')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Theme $theme = null;

    /**
     * @var Collection<Lecon> A collection of lessons associated with this cursus.
     * This is a one-to-many relationship, meaning one cursus can have multiple lessons.
     */
    #[ORM\OneToMany(mappedBy: 'cursus', targetEntity: Lecon::class, cascade: ['persist', 'remove'])]
    private Collection $lecons;

    /**
     * @var Collection<User> A collection of users enrolled in this cursus.
     * This is a many-to-many relationship, meaning multiple users can be enrolled in multiple cursus.
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'cursus')]
    private Collection $users;

    /**
     * @var bool Indicates whether the cursus has been validated.
     * This property is used to track the validation status of the cursus.
     */
    #[ORM\Column(type: 'boolean')]
    private bool $isValidated = false;

    /**
     * Cursus constructor.
     * Initializes the collections for lessons and users.
     */
    public function __construct()
    {
        $this->lecons = new ArrayCollection();
        $this->users = new ArrayCollection();
    }
    /**
     * Adds a user to the cursus.
     *
     * @param User $user The user to add.
     * @return static
     */

    public function getUsers(): Collection
    {
        return $this->users;
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

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(?Theme $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function getLecons(): Collection
    {
        return $this->lecons;
    }

    public function addLecon(Lecon $lecon): static
    {
        if (!$this->lecons->contains($lecon)) {
            $this->lecons->add($lecon);
            $lecon->setCursus($this);
        }

        return $this;
    }

    public function removeLecon(Lecon $lecon): static
    {
        if ($this->lecons->removeElement($lecon)) {
            if ($lecon->getCursus() === $this) {
                $lecon->setCursus(null);
            }
        }

        return $this;
    }

    public function isValidated(): bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): self
    {
        $this->isValidated = $isValidated;

        return $this;
    }
}
