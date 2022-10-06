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

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?bool $isActive = null;

    #[ORM\ManyToMany(targetEntity: Partner::class, mappedBy: 'globalPermissions')]
    private Collection $partners;

    #[ORM\ManyToMany(targetEntity: Subsidiary::class, mappedBy: 'roomPermissions')]
    private Collection $subsidiaries;

    public function __construct()
    {
        $this->partners = new ArrayCollection();
        $this->subsidiaries = new ArrayCollection();
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
     * @return Collection<int, Partner>
     */
    public function getPartners(): Collection
    {
        return $this->partners;
    }

    public function addPartner(Partner $partner): self
    {
        if (!$this->partners->contains($partner)) {
            $this->partners->add($partner);
            $partner->addGlobalPermission($this);
        }

        return $this;
    }

    public function removePartner(Partner $partner): self
    {
        if ($this->partners->removeElement($partner)) {
            $partner->removeGlobalPermission($this);
        }

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
}
