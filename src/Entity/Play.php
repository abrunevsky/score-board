<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PlayRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass=PlayRepository::class)
 * @Table(uniqueConstraints={@UniqueConstraint(name="play_unq", columns={"championship_id", "host_id", "guest_id"})})
 */
class Play
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=Championship::class, inversedBy="plays")
     * @ORM\JoinColumn(nullable=false)
     */
    private Championship $championship;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Team $host;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Team $guest;

    /**
     * @ORM\Column(type="smallint")
     */
    private int $hostScore = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    private int $guestScore = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    private int $orderNumber;

    public function __construct(Championship $championship, Team $host, Team $guest, int $orderNumber)
    {
        $this->championship = $championship;
        $this->host = $host;
        $this->guest = $guest;
        $this->orderNumber = $orderNumber;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChampionship(): ?Championship
    {
        return $this->championship;
    }

    public function getHost(): ?Team
    {
        return $this->host;
    }

    public function getGuest(): ?Team
    {
        return $this->guest;
    }

    public function getHostScore(): ?int
    {
        return $this->hostScore;
    }

    public function setHostScore(int $hostScore): void
    {
        $this->hostScore = $hostScore;
    }

    public function getGuestScore(): ?int
    {
        return $this->guestScore;
    }

    public function setGuestScore(int $guestScore): void
    {
        $this->guestScore = $guestScore;
    }

    public function getOrderNumber(): int
    {
        return $this->orderNumber;
    }
}
