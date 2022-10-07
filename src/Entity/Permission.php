<?php

namespace App\Entity;

use App\Repository\PermissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
class Permission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 50,
        maxMessage: 'Le nom de la permission est trop long'
    )]
    private ?string $name = null;

    public function __toString(): string
    {
        return $this->name;
    }

    #[ORM\ManyToMany(targetEntity: Subsidiary::class, mappedBy: 'roomPermissions')]
    private Collection $subsidiaries;

    #[ORM\OneToMany(mappedBy: 'permission', targetEntity: PartnerPermission::class, orphanRemoval: true)]
    private Collection $partnerPermissions;

    #[ORM\OneToMany(mappedBy: 'permission', targetEntity: SubsidiaryPermission::class, orphanRemoval: true)]
    private Collection $subsidiaryPermissions;

    public function __construct()
    {
        $this->subsidiaries = new ArrayCollection();
        $this->partnerPermissions = new ArrayCollection();
        $this->subsidiaryPermissions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $subsidiary->addRoomPermission($this);
        }

        return $this;
    }

    public function removeSubsidiary(Subsidiary $subsidiary): self
    {
        if ($this->subsidiaries->removeElement($subsidiary)) {
            $subsidiary->removeRoomPermission($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, PartnerPermission>
     */
    public function getPartnerPermissions(): Collection
    {
        return $this->partnerPermissions;
    }

    public function addPartnerPermission(PartnerPermission $partnerPermission): self
    {
        if (!$this->partnerPermissions->contains($partnerPermission)) {
            $this->partnerPermissions->add($partnerPermission);
            $partnerPermission->setPermission($this);
        }

        return $this;
    }

    public function removePartnerPermission(PartnerPermission $partnerPermission): self
    {
        if ($this->partnerPermissions->removeElement($partnerPermission)) {
            // set the owning side to null (unless already changed)
            if ($partnerPermission->getPermission() === $this) {
                $partnerPermission->setPermission(null);
            }
        }

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
            $subsidiaryPermission->setPermission($this);
        }

        return $this;
    }

    public function removeSubsidiaryPermission(SubsidiaryPermission $subsidiaryPermission): self
    {
        if ($this->subsidiaryPermissions->removeElement($subsidiaryPermission)) {
            // set the owning side to null (unless already changed)
            if ($subsidiaryPermission->getPermission() === $this) {
                $subsidiaryPermission->setPermission(null);
            }
        }

        return $this;
    }
}
