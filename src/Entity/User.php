<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Entity\Lecon; 
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


/**
 * Class User
 * * Represents a user entity which includes details about the user,
 * their roles, password, and associated lessons (lecons),
 * cursus, and purchases.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
#[UniqueEntity(fields: ['username'], message: 'Il existe déjà un compte avec cet identifiant.')]
 
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var int|null The unique identifier for the user.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    /**
     * @var string|null The username of the user.
     * This is a unique identifier for the user within the system.
     */
    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    /**
     * @var string|null The email address of the user.
     * This is used for communication and account verification.
     */
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * @var array The roles assigned to the user.
     * This is used for access control and permissions within the application.
     */
    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    /**
     * @var string|null The password of the user.
     * This is used for authentication purposes.
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var bool Indicates whether the user's email is verified.
     * This is used to ensure that the user has a valid email address.
     */
    #[ORM\Column]
    private bool $isVerified = false;

    /**
     * @var bool Indicates whether the user account is active.
     * This is used to manage user access to the application.
     */
    #[ORM\Column(type: 'boolean')]
    private bool $isActive = false;

    /**
     * @var Collection<Lecon> A collection of lessons (lecons) associated with the user.
     * This is a many-to-many relationship, meaning multiple users can access multiple lessons.
     */
    #[ORM\ManyToMany(targetEntity: Lecon::class, inversedBy: 'users')]
    private Collection $lecons;

    /**
     * @var Collection<Cursus> A collection of cursus associated with the user.
     * This is a many-to-many relationship, meaning multiple users can be enrolled in multiple cursus.
     */
    #[ORM\ManyToMany(targetEntity: Cursus::class, inversedBy: 'users')]
    private Collection $cursus;

    /**
     * @var Collection<Lecon> A collection of lessons (lecons) that the user has validated.
     * This is a many-to-many relationship, meaning multiple users can validate multiple lessons.
     */
    #[ORM\ManyToMany(targetEntity: Lecon::class, inversedBy: 'validatedByUsers')]
    #[ORM\JoinTable(name: 'user_lecon_validations')]
    private Collection $validatedLecons;

    /**
     * @var string|null The token used for email verification.
     * This is used to verify the user's email address during registration or password reset.
     */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $emailVerificationToken = null;

    /**
     * @var Collection<UserPurchase> A collection of purchases made by the user.
     * This is a one-to-many relationship, meaning one user can have multiple purchases.
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserPurchase::class, cascade: ['persist', 'remove'])]
    private Collection $purchases;

    /**
     * User constructor.
     * Initializes the collections for lessons, cursus, validated lessons, and purchases.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->email; 
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getEmailVerificationToken(): ?string
    {
        return $this->emailVerificationToken;
    }

    public function setEmailVerificationToken(?string $emailVerificationToken): self
    {
        $this->emailVerificationToken = $emailVerificationToken;

        return $this;
    }

        public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function __construct()
    {
        $this->lecons = new ArrayCollection();
        $this->cursus = new ArrayCollection();
        $this->purchases = new ArrayCollection();
        $this->validatedLecons = new ArrayCollection();
    }

    public function getLecons(): Collection
    {
        return $this->lecons;
    }

    public function addLecon(Lecon $lecon): self
    {
        if (!$this->lecons->contains($lecon)) {
            $this->lecons->add($lecon); 
        }

        return $this;
    }

    public function removeLecon(Lecon $lecon): self
    {
        $this->lecons->removeElement($lecon);

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
        }

        return $this;
    }

    public function removeCursus(Cursus $cursus): self
    {
        $this->cursus->removeElement($cursus);

        return $this;
    }

    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(UserPurchase $purchase): self
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases->add($purchase);
            $purchase->setUser($this);
        }

        return $this;
    }

    public function removePurchase(UserPurchase $purchase): self
    {
        if ($this->purchases->removeElement($purchase)) {
            // Set the owning side to null (unless already changed)
            if ($purchase->getUser() === $this) {
                $purchase->setUser(null);
            }
        }

        return $this;
    }

    public function getValidatedLecons(): Collection
    {
        return $this->validatedLecons;
    }

    public function addValidatedLecon(Lecon $lecon): self
    {
        if (!$this->validatedLecons->contains($lecon)) {
            $this->validatedLecons[] = $lecon;
            $lecon->addValidatedByUser($this);
        }

        return $this;
    }

    public function removeValidatedLecon(Lecon $lecon): self
    {
        if ($this->validatedLecons->contains($lecon)) {
            $this->validatedLecons->removeElement($lecon);
            $lecon->removeValidatedByUser($this);
        }

        return $this;
    }
}