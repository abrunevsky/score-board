<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractPlay
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';

    /**
     * @ORM\ManyToOne(targetEntity=Championship::class, inversedBy="plays")
     * @ORM\JoinColumn(nullable=false)
     */
    protected Championship $championship;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    protected \DateTimeImmutable $playAt;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('pending', 'completed') NOT NULL")
     */
    protected string $status = self::STATUS_PENDING;

    public function __construct(Championship $championship, \DateTimeImmutable $playAt)
    {
        $this->championship = $championship;
        $this->playAt = $playAt;
    }

    public function getChampionship(): Championship
    {
        return $this->championship;
    }

    public function getPlayAt(): \DateTimeImmutable
    {
        return $this->playAt;
    }

    public function isCompleted(): bool
    {
        return self::STATUS_COMPLETED === $this->status;
    }
}
