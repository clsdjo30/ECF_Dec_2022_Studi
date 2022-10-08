<?php

namespace App\Entity;

use App\Repository\PartnerRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PartnerRepository::class)]
class Partner
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        max: 20,
        maxMessage: 'Votre nom est trop long'
    )]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 15)]
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 10,
        maxMessage: 'Votre numero est trop long'
    )]
    private ?string $phoneNumber = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $updatedAt = null;

    #[ORM\OneToOne(mappedBy: 'franchising', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'partner', targetEntity: Subsidiary::class, orphanRemoval: true)]
    private Collection $subsidiaries;

    #[ORM\OneToMany(mappedBy: 'partner', targetEntity: PartnerPermission::class, orphanRemoval: true)]
    private Collection $globalPermissions;

    public function __construct()
    {
        $this->subsidiaries = new ArrayCollection();
        $this->globalPermissions = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
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

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        // set the owning side of the relation if necessary
        if ($user->getFranchising() !== $this) {
            $user->setFranchising($this);
        }

        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Subsidiary>
     */
    public function getSubsidiaries(): Collection
    {
        return $this->subsidiaries;
    }

    public function addSubsidiary(Subsidiary $subsidiary): self
    {
        if (!$this->subsidiaries->contains($subsidiary)) {
            $this->subsidiaries->add($subsidiary);
            $subsidiary->setPartner($this);
        }

        return $this;
    }

    public function removeSubsidiary(Subsidiary $subsidiary): self
    {
        if ($this->subsidiaries->removeElement($subsidiary)) {
            // set the owning side to null (unless already changed)
            if ($subsidiary->getPartner() === $this) {
                $subsidiary->setPartner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PartnerPermission>
     */
    public function getGlobalPermissions(): Collection
    {
        return $this->globalPermissions;
    }

    public function addGlobalPermission(PartnerPermission $globalPermission): self
    {
        if (!$this->globalPermissions->contains($globalPermission)) {
            $this->globalPermissions->add($globalPermission);
            $globalPermission->setPartner($this);
        }

        return $this;
    }

    public function removeGlobalPermission(PartnerPermission $globalPermission): self
    {
        if ($this->globalPermissions->removeElement($globalPermission)) {
            // set the owning side to null (unless already changed)
            if ($globalPermission->getPartner() === $this) {
                $globalPermission->setPartner(null);
            }
        }

        return $this;
    }
}
