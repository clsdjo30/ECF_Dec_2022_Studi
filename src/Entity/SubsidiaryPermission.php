<?php

namespace App\Entity;

use App\Repository\SubsidiaryPermissionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubsidiaryPermissionRepository::class)]
class SubsidiaryPermission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'subsidiaryPermissions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?subsidiary $subsidiary = null;

    #[ORM\ManyToOne(inversedBy: 'subsidiaryPermissions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Permission $permission = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    public function __toString(): string
    {
        return $this->permission->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubsidiary(): ?subsidiary
    {
        return $this->subsidiary;
    }

    public function setSubsidiary(?subsidiary $subsidiary): self
    {
        $this->subsidiary = $subsidiary;

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
