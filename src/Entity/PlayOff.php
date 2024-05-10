<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PlayOffRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass=PlayOffRepository::class)
 * @Table(uniqueConstraints={@UniqueConstraint(name="play_unq", columns={"tournament_id", "team1_id", "team2_id"})})
 */
class PlayOff
{
    public const STAGE_FINAL = 10;
    public const STAGE_SEMIFINAL = 20;
    public const STAGE_3RD_PLACE = 20;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=Tournament::class, inversedBy="playOffs")
     * @ORM\JoinColumn(nullable=false)
     */
    private Tournament $tournament;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Team $team1;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Team $team2;

    /**
     * @ORM\Column(type="smallint")
     */
    private int $team1Score = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    private int $team2Score = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    private int $stage;

    public function __construct(Tournament $tournament, Team $team1, Team $team2, int $stage)
    {
        $this->tournament = $tournament;
        $this->team1 = $team1;
        $this->team2 = $team2;
        $this->stage = $stage;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTournament(): ?Tournament
    {
        return $this->tournament;
    }

    public function getTeam1(): ?Team
    {
        return $this->team1;
    }

    public function getTeam2(): ?Team
    {
        return $this->team2;
    }

    public function getTeam1Score(): int
    {
        return $this->team1Score;
    }

    public function setTeam1Score(int $team1Score): void
    {
        $this->team1Score = $team1Score;
    }

    public function getTeam2Score(): int
    {
        return $this->team2Score;
    }

    public function setTeam2Score(int $team2Score): void
    {
        $this->team2Score = $team2Score;
    }

    public function getStage(): ?int
    {
        return $this->stage;
    }
}
