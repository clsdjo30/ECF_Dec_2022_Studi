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

    #[ORM\Column(nullable: true)]
    private ?bool $isActive = null;

    #[ORM\ManyToOne(inversedBy: 'subsidiaryPermissions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subsidiary $subsidiary = null;

    #[ORM\OneToOne(inversedBy: 'subsidiaryPermission', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?PartnerPermission $partnerPermission = null;

    public function __toString(): string
    {
        return $this->getPartnerPermission()->getPermission()->getName();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getSubsidiary(): ?Subsidiary
    {
        return $this->subsidiary;
    }

    public function setSubsidiary(?Subsidiary $subsidiary): self
    {
        $this->subsidiary = $subsidiary;

        return $this;
    }

    public function getPartnerPermission(): ?PartnerPermission
    {
        return $this->partnerPermission;
    }

    public function setPartnerPermission(PartnerPermission $partnerPermission): self
    {
        $this->partnerPermission = $partnerPermission;

        return $this;
    }

    public function setPermissionNotInclude():bool
    {
        $permissions = $this->partnerPermission;

        foreach($permissions as $value) {
            if (!$value->isActive()) {
               return false;
            }
        }
        return true;
    }
}
