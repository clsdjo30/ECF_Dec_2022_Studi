<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email(
        message: "la {{ value }} de l'email n'est pas valide'"
    )]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private string $password;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 20,
        maxMessage: 'Votre prénom est trop long'
    )]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 20,
        maxMessage: 'Votre nom est trop long'
    )]
    private ?string $lastName = null;

    #[ORM\OneToOne(inversedBy: 'user', cascade: ['persist', 'remove'])]
    #[Assert\Type(type: Partner::class)]
    #[Assert\Valid]
    private ?Partner $franchising = null;

    #[ORM\OneToOne(inversedBy: 'user', cascade: ['persist', 'remove'])]
    #[Assert\Type(type: Subsidiary::class)]
    #[Assert\Valid]
    private ?Subsidiary $roomManager =null;

    #[ORM\OneToOne(inversedBy: 'user', cascade: ['persist', 'remove'])]
    #[Assert\Type(type: TechTeam::class)]
    #[Assert\Valid]
    private ?TechTeam $techTeam =null;

    public function __toString(): string
    {
        return $this->firstName.' '.$this->lastName;
    }


    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials():void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFranchising(): ?Partner
    {
        return $this->franchising;
    }

    public function setFranchising(Partner $franchising): self
    {
        $this->franchising = $franchising;

        return $this;
    }

    public function getRoomManager(): ?Subsidiary
    {
        return $this->roomManager;
    }

    public function setRoomManager(Subsidiary $roomManager): self
    {
        $this->roomManager = $roomManager;

        return $this;
    }

    public function getTechTeam(): ?TechTeam
    {
        return $this->techTeam;
    }

    public function setTechTeam(TechTeam $techTeam): self
    {
        $this->techTeam = $techTeam;

        return $this;
    }
}
