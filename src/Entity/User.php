<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(nullable: true)]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilePicture = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $google_id = null;

    /**
     * @var Collection<int, Rating>
     */
    #[ORM\OneToMany(targetEntity: Rating::class, mappedBy: 'user')]
    private Collection $ratings;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;

    /**
     * @var Collection<int, Itinerary>
     */
    #[ORM\ManyToMany(targetEntity: Itinerary::class, mappedBy: 'users')]
    private Collection $itineraries;

    /**
     * @var Collection<int, Itinerary>
     */
    #[ORM\OneToMany(targetEntity: Itinerary::class, mappedBy: 'createdBy')]
    private Collection $createdItineraries;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $registrationDate = null;

    /**
     * @var Collection<int, Itinerary>
     */
    #[ORM\ManyToMany(targetEntity: Itinerary::class, inversedBy: 'favoritedBy')]
    private Collection $favoriteItineraries;

    public function __construct()
    {
        $this->ratings = new ArrayCollection();
        $this->itineraries = new ArrayCollection();
        $this->createdItineraries = new ArrayCollection();
        $this->registrationDate = new \DateTimeImmutable();
        $this->favoriteItineraries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): static
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getGoogleId(): ?string
    {
        return $this->google_id;
    }

    public function setGoogleId(?string $google_id): static
    {
        $this->google_id = $google_id;

        return $this;
    }

    /**
     * @return Collection<int, Rating>
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): static
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
            $rating->setUser($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): static
    {
        if ($this->ratings->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getUser() === $this) {
                $rating->setUser(null);
            }
        }

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection<int, Itinerary>
     */
    public function getItineraries(): Collection
    {
        return $this->itineraries;
    }

    public function addItinerary(Itinerary $itinerary): static
    {
        if (!$this->itineraries->contains($itinerary)) {
            $this->itineraries->add($itinerary);
            $itinerary->addUser($this);
        }

        return $this;
    }

    public function removeItinerary(Itinerary $itinerary): static
    {
        if ($this->itineraries->removeElement($itinerary)) {
            $itinerary->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Itinerary>
     */
    public function getCreatedItineraries(): Collection
    {
        return $this->createdItineraries;
    }

    public function addCreatedItinerary(Itinerary $createdItinerary): static
    {
        if (!$this->createdItineraries->contains($createdItinerary)) {
            $this->createdItineraries->add($createdItinerary);
            $createdItinerary->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCreatedItinerary(Itinerary $createdItinerary): static
    {
        if ($this->createdItineraries->removeElement($createdItinerary)) {
            // set the owning side to null (unless already changed)
            if ($createdItinerary->getCreatedBy() === $this) {
                $createdItinerary->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(?\DateTimeInterface $registrationDate): static
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    /**
     * @return Collection<int, Itinerary>
     */
    public function getFavoriteItineraries(): Collection
    {
        return $this->favoriteItineraries;
    }

    public function addFavoriteItinerary(Itinerary $favoriteItinerary): static
    {
        if (!$this->favoriteItineraries->contains($favoriteItinerary)) {
            $this->favoriteItineraries->add($favoriteItinerary);
        }

        return $this;
    }

    public function removeFavoriteItinerary(Itinerary $favoriteItinerary): static
    {
        $this->favoriteItineraries->removeElement($favoriteItinerary);

        return $this;
    }
}
