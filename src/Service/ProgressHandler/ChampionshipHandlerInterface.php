<?php

declare(strict_types=1);

namespace App\Service\ProgressHandler;

use App\Entity\Championship;

interface ChampionshipHandlerInterface
{
    public function canProcess(Championship $championship): bool;

    public function process(Championship $championship): void;

    public static function getIndex(): int;
}
