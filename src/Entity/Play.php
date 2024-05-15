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
final class Play extends AbstractPlay
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected ?int $id = null;

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

    public function __construct(Championship $championship, Team $host, Team $guest, \DateTimeImmutable $playAt)
    {
        parent::__construct($championship, $playAt);
        $this->host = $host;
        $this->guest = $guest;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getGuestScore(): ?int
    {
        return $this->guestScore;
    }

    public function setCompleted(int $hostScore, int $guestScore): void
    {
        $this->hostScore = $hostScore;
        $this->guestScore = $guestScore;
        $this->status = self::STATUS_COMPLETED;
    }
}
