<?php

namespace App\Entity;

use App\Repository\TechTeamRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TechTeamRepository::class)]
class TechTeam
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(mappedBy: 'techTeam', cascade: ['persist', 'remove'])]
    #[Assert\Type(type: User::class)]
    #[Assert\Valid]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        // set the owning side of the relation if necessary
        if ($user->getTechTeam() !== $this) {
            $user->setTechTeam($this);
        }
        $this->user = $user;

        return $this;
    }
}
