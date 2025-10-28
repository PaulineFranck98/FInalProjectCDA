<?php

namespace App\Entity;

use App\Repository\ItineraryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItineraryRepository::class)]
class Itinerary
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $itineraryName = null;

    #[ORM\Column(nullable: true)]
    private ?int $duration = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column]
    private ?bool $isPublic = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $departureDate = null;

    /**
     * @var Collection<int, ItineraryLocation>
     */
    #[ORM\OneToMany(targetEntity: ItineraryLocation::class, mappedBy: 'itinerary')]
    private Collection $itineraryLocations;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'itineraries')]
    private Collection $users;

    public function __construct()
    {
        $this->creationDate = new \DateTimeImmutable();
        $this->itineraryLocations = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItineraryName(): ?string
    {
        return $this->itineraryName;
    }

    public function setItineraryName(string $itineraryName): static
    {
        $this->itineraryName = $itineraryName;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): static
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    public function getDepartureDate(): ?\DateTimeInterface
    {
        return $this->departureDate;
    }

    public function setDepartureDate(\DateTimeInterface $departureDate): static
    {
        $this->departureDate = $departureDate;

        return $this;
    }

    /**
     * @return Collection<int, ItineraryLocation>
     */
    public function getItineraryLocations(): Collection
    {
        return $this->itineraryLocations;
    }

    public function addItineraryLocation(ItineraryLocation $itineraryLocation): static
    {
        if (!$this->itineraryLocations->contains($itineraryLocation)) {
            $this->itineraryLocations->add($itineraryLocation);
            $itineraryLocation->setItinerary($this);
        }

        return $this;
    }

    public function removeItineraryLocation(ItineraryLocation $itineraryLocation): static
    {
        if ($this->itineraryLocations->removeElement($itineraryLocation)) {
            // set the owning side to null (unless already changed)
            if ($itineraryLocation->getItinerary() === $this) {
                $itineraryLocation->setItinerary(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }
}
