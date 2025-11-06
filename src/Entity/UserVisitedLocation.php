<?php

namespace App\Entity;

use App\Repository\UserVisitedLocationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserVisitedLocationRepository::class)]
class UserVisitedLocation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $locationId = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $visitedAt = null;

    #[ORM\ManyToOne(inversedBy: 'userVisitedLocations')]
    #[ORM\JoinColumn(nullable: false)] 
    private ?User $user = null;

    public function __construct()
    {
        $this->visitedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocationId(): ?string
    {
        return $this->locationId;
    }

    public function setLocationId(string $locationId): static
    {
        $this->locationId = $locationId;

        return $this;
    }

    public function getVisitedAt(): ?\DateTimeImmutable
    {
        return $this->visitedAt;
    }

    public function setVisitedAt(\DateTimeImmutable $visitedAt): static
    {
        $this->visitedAt = $visitedAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
