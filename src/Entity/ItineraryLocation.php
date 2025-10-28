<?php

namespace App\Entity;

use App\Repository\ItineraryLocationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItineraryLocationRepository::class)]
class ItineraryLocation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $locationId = null;

    #[ORM\Column]
    private ?int $orderIndex = null;

    #[ORM\ManyToOne(inversedBy: 'itineraryLocations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Itinerary $itinerary = null;

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

    public function getOrderIndex(): ?int
    {
        return $this->orderIndex;
    }

    public function setOrderIndex(int $orderIndex): static
    {
        $this->orderIndex = $orderIndex;

        return $this;
    }

    public function getItinerary(): ?Itinerary
    {
        return $this->itinerary;
    }

    public function setItinerary(?Itinerary $itinerary): static
    {
        $this->itinerary = $itinerary;

        return $this;
    }
}
