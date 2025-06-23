<?php

namespace App\Entity;

use App\Repository\LeconRepository;
use App\Entity\User; 
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class Lecon
 *
 * Represents a lesson entity which includes details about the lesson,
 * its associated cursus, and users who have access to it.
 */
#[ORM\Entity(repositoryClass: LeconRepository::class)]
class Lecon
{
    /**
     * @var int|null The unique identifier for the lesson.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string|null The name of the lesson.
     * This represents the title or name of the lesson.
     */
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var float|null The price of the lesson.
     * This represents the cost associated with accessing the lesson.
     */
    #[ORM\Column]
    private ?float $prix = null;

    /**
     * @var Cursus|null The cursus associated with this lesson.
     * This is a many-to-one relationship, meaning multiple lessons can be linked to one cursus.
     */
    #[ORM\ManyToOne(targetEntity: Cursus::class, inversedBy: 'lecons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cursus $cursus = null;

    /**
     * @var Collection<User> A collection of users who have access to this lesson.
     * This is a many-to-many relationship, meaning multiple users can access multiple lessons.
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'lecons')]
    private Collection $users;

    /**
     * @var Collection<User> A collection of users who have validated this lesson.
     * This is a many-to-many relationship, meaning multiple users can validate multiple lessons.
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'validatedLecons')]
    private Collection $validatedByUsers;

    /**
     * Lecon constructor.
     * Initializes the collections for users and validatedByUsers.
     */
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

    public function getCursus(): ?Cursus
    {
        return $this->cursus;
    }

    public function setCursus(?Cursus $cursus): static
    {
        $this->cursus = $cursus;

        return $this;
    }

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->validatedByUsers = new ArrayCollection();
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function getValidatedByUsers(): Collection
    {
        return $this->validatedByUsers;
    }

    public function addValidatedByUser(User $user): self
    {
        if (!$this->validatedByUsers->contains($user)) {
            $this->validatedByUsers[] = $user;
            $user->addValidatedLecon($this);
        }

        return $this;
    }

    public function removeValidatedByUser(User $user): self
    {
        if ($this->validatedByUsers->contains($user)) {
            $this->validatedByUsers->removeElement($user);
            $user->removeValidatedLecon($this);
        }

        return $this;
    }


}
