<?php

namespace App\Entity;

use App\Repository\PartnerPermissionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartnerPermissionRepository::class)]
class PartnerPermission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'globalPermissions')]
    private ?Partner $partner;

    #[ORM\ManyToOne(inversedBy: 'partnerPermissions')]
    private ?Permission $permission;

    #[ORM\Column]
    private ?bool $isActive;

    public function __toString(): string
    {
        return $this->permission->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPermission(): ?Permission
    {
        return $this->permission;
    }

    public function setPermission(?Permission $permission): self
    {
        $this->permission = $permission;

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
}
