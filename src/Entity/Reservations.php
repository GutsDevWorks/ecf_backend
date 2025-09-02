<?php

namespace App\Entity;

use App\Repository\ReservationsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationsRepository::class)]
#[ORM\Table(name: 'reservations')]
#[ORM\HasLifecycleCallbacks]
class Reservations
{
    // Libellés métier (utilisables en UI)
    public const STATUS_VALIDEE    = 'validée';
    public const STATUS_EN_ATTENTE = 'en attente';
    public const STATUS_REFUSEE    = 'refusée';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    // Types Doctrine explicités pour portabilité (MySQL/MariaDB)
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $startAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $endAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $reminderSentAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $validatedAt = null;

    /**
     * Colonne DB existante : TINYINT(1) NULL (MariaDB/MySQL)
     * Mapping Doctrine portable : smallint nullable
     *
     * Convention :
     *   1     => VALIDEE (true)
     *   0     => REFUSEE (false)
     *   NULL  => EN_ATTENTE
     */
    #[ORM\Column(type: 'smallint', nullable: true, options: ['unsigned' => true])]
    private ?int $reservationStatus = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'user_id_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $userId = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'room_id_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Room $roomId = null;

    // -------------------
    // Getters / Setters
    // -------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;
        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getReminderSentAt(): ?\DateTimeImmutable
    {
        return $this->reminderSentAt;
    }

    public function setReminderSentAt(?\DateTimeImmutable $reminderSentAt): self
    {
        $this->reminderSentAt = $reminderSentAt;
        return $this;
    }

    public function getValidatedAt(): ?\DateTimeImmutable
    {
        return $this->validatedAt;
    }

    public function setValidatedAt(?\DateTimeImmutable $validatedAt): self
    {
        $this->validatedAt = $validatedAt;
        return $this;
    }

    // -------------------
    // Statut (logique sans enum)
    // -------------------

    /** Accès brut si nécessaire (int|null) */
    public function getReservationStatusCode(): ?int
    {
        return $this->reservationStatus;
    }

    public function setReservationStatusCode(?int $code): self
    {
        $this->reservationStatus = $code;
        return $this;
    }

    /** Getter attendu par Twig : {{ reservation.reservationStatus }} (bool|null) */
    public function getReservationStatus(): ?bool
    {
        return match ($this->reservationStatus) {
            1       => true,
            0       => false,
            default => null, // en attente
        };
    }

    /** Alias Twig : {{ reservation.isReservationStatus }} */
    public function isReservationStatus(): ?bool
    {
        return $this->getReservationStatus();
    }

    /** Setter « legacy » depuis bool|null */
    public function setReservationStatus(?bool $status): self
    {
        $this->reservationStatus = match ($status) {
            true    => 1,
            false   => 0,
            default => null,
        };
        return $this;
    }

    /** Libellé prêt à afficher */
    public function getStatusLabel(): string
    {
        return match ($this->reservationStatus) {
            1       => self::STATUS_VALIDEE,
            0       => self::STATUS_REFUSEE,
            default => self::STATUS_EN_ATTENTE,
        };
    }

    /** Couleurs helper (hex) */
    public function getStatusColorHex(): string
    {
        return match ($this->reservationStatus) {
            1       => '#16a34a', // vert
            0       => '#dc2626', // rouge
            default => '#f59e0b', // orange (en attente)
        };
    }

    /** Classe badge (si tu veux passer par des classes CSS) */
    public function getStatusBadgeClass(): string
    {
        return match ($this->reservationStatus) {
            1       => 'bg-green-100 text-green-800',
            0       => 'bg-red-100 text-red-800',
            default => 'bg-amber-100 text-amber-800',
        };
    }

    // -------------------
    // Relations
    // -------------------

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getRoomId(): ?Room
    {
        return $this->roomId;
    }

    public function setRoomId(?Room $roomId): self
    {
        $this->roomId = $roomId;
        return $this;
    }

    // -------------------
    // Callbacks
    // -------------------

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if (null === $this->createdAt) {
            $this->createdAt = new \DateTimeImmutable('now');
        }
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable('now');
    }
}
