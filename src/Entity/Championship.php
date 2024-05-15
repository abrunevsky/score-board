<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ChampionshipRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChampionshipRepository::class)
 */
final class Championship
{
    public const STATUS_DRAW = 'draw';
    public const STATUS_PLAY = 'play';
    public const STATUS_QUALIFYING = 'qualifying';
    public const STATUS_PLAYOFF = 'playoff';
    public const STATUS_FINISHED = 'finished';

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
    private \DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('draw', 'play', 'qualifying', 'playoff', 'finished')")
     */
    private string $status = self::STATUS_DRAW;

    /**
     * @ORM\OneToMany(targetEntity=PlayingTeam::class, mappedBy="championship", orphanRemoval=true)
     *
     * @var Collection<int, PlayingTeam>
     */
    private Collection $playingTeams;

    /**
     * @ORM\OneToMany(targetEntity=Play::class, mappedBy="championship", orphanRemoval=true)
     *
     * @var Collection<int, Play>
     */
    private Collection $plays;

    /**
     * @ORM\OneToMany(targetEntity=PlayOff::class, mappedBy="championship", orphanRemoval=true)
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
        $this->createdAt = new \DateTimeImmutable();
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
        return $this->createdAt;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
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
