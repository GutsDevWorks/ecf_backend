<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    // Identifiant unique de la salle (clé primaire auto-incrémentée)
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Nom de la salle (ex: Salle A, Auditorium...)
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    // Localisation de la salle (ex: Bâtiment B, Étage 2...)
    #[ORM\Column(length: 255)]
    private ?string $location = null;

    // Capacité d’accueil (nombre de personnes)
    #[ORM\Column]
    private ?int $capacity = null;

    // Description détaillée de la salle
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    // Relation OneToMany : une salle peut avoir plusieurs réservations
    #[ORM\OneToMany(targetEntity: Reservations::class, mappedBy: 'roomId')]
    private Collection $reservations;

    // Relation ManyToMany avec Options : une salle peut avoir plusieurs options
    #[ORM\ManyToMany(targetEntity: Options::class, inversedBy: 'rooms')]
    #[ORM\JoinTable(name: 'room_options')] // Table de jointure personnalisée
    private Collection $options;

    // Nom de fichier ou URL de la photo associée à la salle (nullable)
    #[ORM\Column(length: 255, nullable: true)] 
    private ?string $photo = null;

    // Constructeur : initialise les collections (reservations et options)
    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->options = new ArrayCollection();
    }

    // --- Getters & Setters ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;
        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Collection<int, Reservations>
     * Retourne toutes les réservations liées à cette salle
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    // Ajoute une réservation à la salle et synchronise la relation
    public function addReservation(Reservations $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setRoomId($this);
        }
        return $this;
    }

    // Supprime une réservation et casse la relation si nécessaire
    public function removeReservation(Reservations $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getRoomId() === $this) {
                $reservation->setRoomId(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Options>
     * Retourne toutes les options associées à la salle
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    // Ajoute une option à la salle
    public function addOption(Options $option): static
    {
        if (!$this->options->contains($option)) {
            $this->options->add($option);
        }
        return $this;
    }

    // Supprime une option de la salle
    public function removeOption(Options $option): static
    {
        $this->options->removeElement($option);
        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;
        return $this;
    }
}
