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
    public const STATUS_PLAYOFF_QUARTER = 'playoff_quarter';
    public const STATUS_PLAYOFF_SEMIFINAL = 'playoff_semifinal';
    public const STATUS_PLAYOFF_FINAL = 'playoff_final';
    public const STATUS_PLAYOFF_3RD_PLACE = 'playoff_3d_place';
    public const STATUS_FINISHED = 'finished';
    public const STATUS_ERROR = 'error';

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
     * @ORM\Column(
     *     type="string",
     *     columnDefinition="ENUM('draw', 'play', 'qualifying', 'playoff_quarter', 'playoff_semifinal', 'playoff_final', 'playoff_3d_place', 'finished', 'error') NOT NULL"
     * )
     */
    private string $status = self::STATUS_DRAW;

    /**
     * @ORM\OneToMany(targetEntity=PlayingTeam::class, mappedBy="championship", orphanRemoval=true, cascade={"all"})
     *
     * @var Collection<int, PlayingTeam>
     */
    private Collection $playingTeams;

    /**
     * @ORM\OneToMany(targetEntity=Play::class, mappedBy="championship", orphanRemoval=true, cascade={"all"})
     *
     * @var Collection<int, Play>
     */
    private Collection $plays;

    /**
     * @ORM\OneToMany(targetEntity=PlayOff::class, mappedBy="championship", orphanRemoval=true, cascade={"all"})
     *
     * @var Collection<int, PlayOff>
     */
    private Collection $playOffs;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $bidirectional;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $errorMessage = null;

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
     * @return array<int, PlayingTeam>
     */
    public function getPlayingTeams(): array
    {
        return $this->playingTeams->toArray();
    }

    /**
     * @param array<int, PlayingTeam> $playingTeams
     */
    public function setPlayingTeams(array $playingTeams): void
    {
        $this->playingTeams = new ArrayCollection($playingTeams);
    }

    /**
     * @return array<int, Play>
     */
    public function getPlays(): array
    {
        return $this->plays->toArray();
    }

    /**
     * @param array<int, Play> $plays
     */
    public function setPlays(array $plays): void
    {
        $this->plays = new ArrayCollection($plays);
    }

    /**
     * @return array<int, PlayOff>
     */
    public function getPlayOffs(): array
    {
        return $this->playOffs->toArray();
    }

    /**
     * @param array<int, PlayOff> $playOffs
     */
    public function appendPlayOffs(array $playOffs): void
    {
        foreach ($playOffs as $playOff) {
            $this->playOffs->add($playOff);
        }
    }

    public function isBidirectional(): bool
    {
        return $this->bidirectional;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
        $this->status = self::STATUS_ERROR;
    }

    public function findPlayingTeamByTeam(Team $team): ?PlayingTeam
    {
        if (null === $team->getId()) {
            return null;
        }

        $players = $this->playingTeams->filter(static function (PlayingTeam $playingTeam) use ($team) {
            return $playingTeam->getTeam()->getId() === $team->getId();
        });

        return $players->getValues()[0] ?? null;
    }

    /**
     * @return PlayingTeam[]
     */
    public function getSortedPlayingTeams(): array
    {
        $playingTeams = $this->playingTeams->toArray();
        uasort($playingTeams, static function (PlayingTeam $player1, PlayingTeam $player2) {
            return -1 * ($player1->getScore() <=> $player2->getScore());
        });

        return $playingTeams;
    }
}
