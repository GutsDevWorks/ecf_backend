<?php

namespace App\Entity;

use App\Repository\OptionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OptionsRepository::class)]
class Options
{
    // Identifiant unique (clé primaire auto-incrémentée)
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Nom de l’option (ex: Vidéoprojecteur, Climatisation, etc.)
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    // Description détaillée (texte long, optionnel)
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    // Type de l’option (ex: "Technique", "Confort", etc.)
    #[ORM\Column(length: 255)]
    private ?string $type = null;

    // Relation ManyToMany avec l’entité Room (une option peut appartenir à plusieurs salles)
    #[ORM\ManyToMany(targetEntity: Room::class, mappedBy: 'options')]
    private Collection $rooms;

    // Constructeur : initialise la collection de salles liées
    public function __construct()
    {
        $this->rooms = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Room>
     * Retourne toutes les salles qui possèdent cette option
     */
    public function getRooms(): Collection
    {
        return $this->rooms;
    }

    // Ajoute une salle à la liste des salles liées à cette option
    public function addRoom(Room $room): static
    {
        if (!$this->rooms->contains($room)) {
            $this->rooms->add($room);
            // Synchronise la relation côté Room
            $room->addOption($this);
        }

        return $this;
    }

    // Supprime une salle de la liste des salles liées à cette option
    public function removeRoom(Room $room): static
    {
        if ($this->rooms->removeElement($room)) {
            // Synchronise la relation côté Room
            $room->removeOption($this);
        }

        return $this;
    }
}
