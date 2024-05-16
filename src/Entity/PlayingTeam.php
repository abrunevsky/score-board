<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PlayingTeamRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass=PlayingTeamRepository::class)
 * @Table(uniqueConstraints={@UniqueConstraint(name="division_unq", columns={"championship_id", "team_id", "division"})})
 */
final class PlayingTeam
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=Championship::class, inversedBy="divisions")
     * @ORM\JoinColumn(nullable=false)
     */
    private Championship $championship;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Team $team;

    /**
     * @ORM\Column(type="string", length=1, columnDefinition="CHAR(1) NOT NULL")
     */
    private string $division;

    /**
     * @ORM\Column(type="smallint")
     */
    private int $score = 0;

    public function __construct(Championship $championship, Team $team, string $division)
    {
        $this->championship = $championship;
        $this->team = $team;
        $this->division = $division;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChampionship(): Championship
    {
        return $this->championship;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function getDivision(): ?string
    {
        return $this->division;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function incrementScore(int $score): void
    {
        $this->score += $score;
    }
}
