<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PlayOffRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass=PlayOffRepository::class)
 * @Table(uniqueConstraints={@UniqueConstraint(name="play_unq", columns={"championship_id", "team1_id", "team2_id"})})
 */
final class PlayOff extends AbstractPlay
{
    public const STAGE_QUARTERFINAL = '1/4final';
    public const STAGE_SEMIFINAL = '1/2final';
    public const STAGE_FINAL = 'final';
    public const STAGE_3RD_PLACE = '3place';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

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
     * @ORM\Column(type="string", columnDefinition="ENUM('1/4final', '1/2final', 'final', '3place')")
     */
    private string $stage;

    public function __construct(
        Championship $championship,
        Team $team1,
        Team $team2,
        string $stage,
        \DateTimeImmutable $playAt
    ) {
        parent::__construct($championship, $playAt);
        $this->team1 = $team1;
        $this->team2 = $team2;
        $this->stage = $stage;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTeam2Score(): int
    {
        return $this->team2Score;
    }

    public function getStage(): string
    {
        return $this->stage;
    }

    public function setCompleted(int $team1Score, int $team2Score): void
    {
        $this->team1Score = $team1Score;
        $this->team2Score = $team2Score;
        $this->status = self::STATUS_COMPLETED;
    }
}
