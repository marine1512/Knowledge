<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/** 
 * Class UserPurchase
 *  
 * Represents a user's purchase of a lesson or cursus.
 * Contains properties for the purchase ID, associated user, lesson, cursus, and validation status.
 */
#[ORM\Entity]
class UserPurchase
{
    /**
     * @var int|null The unique identifier for the purchase.
     * This is automatically generated when a new purchase is created.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var User|null The user who made the purchase.
     * This is a many-to-one relationship, meaning multiple purchases can be associated with one user.
     */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'purchases')]
    private ?User $user = null;

    /**
     * @var Lecon|null The lesson associated with this purchase.
     * This is a many-to-one relationship, meaning multiple purchases can be associated with one lesson.
     */
    #[ORM\ManyToOne(targetEntity: Lecon::class)]
    private ?Lecon $lecon = null; 
    #[ORM\ManyToOne(targetEntity: Cursus::class)]
    private ?Cursus $cursus = null;

    /**
     * @var bool Indicates whether the purchase has been validated.
     * This is used to track the validation status of the purchase.
     */
    #[ORM\Column(type: 'boolean')]
    private bool $isValidated = false; 

    /**
     * UserPurchase constructor.
     * Initializes the isValidated property to false by default.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getLecon(): ?Lecon
    {
        return $this->lecon;
    }

    public function setLecon(?Lecon $lecon): self
    {
        $this->lecon = $lecon;

        return $this;
    }

    public function getCursus(): ?Cursus
{
    return $this->cursus;
}

public function setCursus(?Cursus $cursus): self
{
    $this->cursus = $cursus;
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