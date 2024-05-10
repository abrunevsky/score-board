<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PlayingTeamRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass=PlayingTeamRepository::class)
 * @Table(uniqueConstraints={@UniqueConstraint(name="division_unq", columns={"tournament_id", "team_id", "division"})})
 */
class PlayingTeam
{
    public const DIVISION_A = 'A';
    public const DIVISION_B = 'B';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=Tournament::class, inversedBy="divisions")
     * @ORM\JoinColumn(nullable=false)
     */
    private Tournament $tournament;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Team $team;

    /**
     * @ORM\Column(type="string", length=1, columnDefinition="CHAR(1)")
     */
    private string $division;

    /**
     * @ORM\Column(type="smallint")
     */
    private int $score = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    private int $position;

    /**
     * @ORM\Column(type="smallint")
     */
    private int $totalPosition = 0;

    public function __construct(Tournament $tournament, Team $team, string $division, int $position)
    {
        $this->tournament = $tournament;
        $this->team = $team;
        $this->division = $division;
        $this->position = $position;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function getTournament(): ?Tournament
    {
        return $this->tournament;
    }

    public function getDivision(): ?string
    {
        return $this->division;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): void
    {
        $this->score = $score;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getTotalPosition(): int
    {
        return $this->totalPosition;
    }

    public function setTotalPosition(int $totalPosition): void
    {
        $this->totalPosition = $totalPosition;
    }
}
