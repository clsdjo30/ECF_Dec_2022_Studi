<?php

namespace App\Entity;

use App\Repository\SubsidiaryRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SubsidiaryRepository::class)]
class Subsidiary
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        max:40,
        maxMessage: "Le nom de la salle est trop long"
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $logoUrl;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $url;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(
        min:50,
        minMessage: "La description est trop courte"
    )]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[ASsert\NotBlank]
    #[Assert\Length(
        max:10,
        maxMessage: "Le numéro est trop long"
    )]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min:20,
        minMessage: "L'adresse est trop courte"
    )]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        max:40,
        maxMessage: "Le nom de la salle est trop long"
    )]
    private ?string $city = null;

    #[ORM\Column]
    #[Assert\Length(
        min:2,
        max:5,
        minMessage: "Le Code postal est trop court",
        maxMessage: "le code postal est trop long"
    )]
    private ?int $postalCode = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\OneToOne(inversedBy: 'roomManager', cascade: ['persist', 'remove'])]
    #[Assert\Type(type: User::class)]
    #[Assert\Valid]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'subsidiaries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Partner $partner = null;

    #[ORM\Column(type: 'datetime')]
    #[Timestampable(on: 'create')]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    #[Timestampable(on: 'update')]
    private ?DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'subsidiary', targetEntity: SubsidiaryPermission::class, orphanRemoval: true)]
    private Collection $subsidiaryPermissions;

    public function __toString(): string
    {
        return $this->name;
    }


    public function __construct()
    {
        $this->subsidiaryPermissions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    public function setLogoUrl(string $logoUrl): self
    {
        $this->logoUrl = $logoUrl;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?int
    {
        return $this->postalCode;
    }

    public function setPostalCode(int $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        // set the owning side of the relation if necessary
        if ($user->getRoomManager() !== $this) {
            $user->setRoomManager($this);
        }

        $this->user = $user;

        return $this;
    }

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function setPartner(?Partner $partner): self
    {
        $this->partner = $partner;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, SubsidiaryPermission>
     */
    public function getSubsidiaryPermissions(): Collection
    {
        return $this->subsidiaryPermissions;
    }

    public function addSubsidiaryPermission(SubsidiaryPermission $subsidiaryPermission): self
    {
        if (!$this->subsidiaryPermissions->contains($subsidiaryPermission)) {
            $this->subsidiaryPermissions->add($subsidiaryPermission);
            $subsidiaryPermission->setSubsidiary($this);
        }

        return $this;
    }

    public function removeSubsidiaryPermission(SubsidiaryPermission $subsidiaryPermission): self
    {
        if ($this->subsidiaryPermissions->removeElement($subsidiaryPermission)) {
            // set the owning side to null (unless already changed)
            if ($subsidiaryPermission->getSubsidiary() === $this) {
                $subsidiaryPermission->setSubsidiary(null);
            }
        }

        return $this;
    }
}
