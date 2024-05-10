<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TournamentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TournamentRepository::class)
 */
final class Tournament
{
    public const STATUS_DRAW = 0;
    public const STATUS_DIVISION = 25;
    public const STATUS_QUALIFYING = 50;
    public const STATUS_PLAYOFF = 75;
    public const STATUS_OVER = 100;

    /**
     * @ORM\Id
     *
     * @ORM\GeneratedValue
     *
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $created_at;

    /**
     * @ORM\Column(type="smallint")
     */
    private int $status = self::STATUS_DRAW;

    /**
     * @ORM\OneToMany(targetEntity=PlayingTeam::class, mappedBy="tournament", orphanRemoval=true, fetch="EAGER")
     *
     * @var Collection<int, PlayingTeam>
     */
    private Collection $playingTeams;

    /**
     * @ORM\OneToMany(targetEntity=Play::class, mappedBy="tournament", orphanRemoval=true, fetch="EAGER")
     *
     * @var Collection<int, Play>
     */
    private Collection $plays;

    /**
     * @ORM\OneToMany(targetEntity=PlayOff::class, mappedBy="tournament", orphanRemoval=true, fetch="EAGER")
     *
     * @var Collection<int, PlayOff>
     */
    private Collection $playOffs;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $bidirectional;

    public function __construct(bool $bidirectional)
    {
        $this->created_at = new \DateTimeImmutable();
        $this->playingTeams = new ArrayCollection();
        $this->plays = new ArrayCollection();
        $this->playOffs = new ArrayCollection();
        $this->bidirectional = $bidirectional;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return Collection<int, PlayingTeam>
     */
    public function getPlayingTeams(): Collection
    {
        return $this->playingTeams;
    }

    /**
     * @return Collection<int, Play>
     */
    public function getPlays(): Collection
    {
        return $this->plays;
    }

    /**
     * @return Collection<int, PlayOff>
     */
    public function getPlayOffs(): Collection
    {
        return $this->playOffs;
    }

    public function isBidirectional(): bool
    {
        return $this->bidirectional;
    }
}
