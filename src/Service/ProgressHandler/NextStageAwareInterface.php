<?php

declare(strict_types=1);

namespace App\Service\ProgressHandler;

use App\Entity\Championship;

interface NextStageAwareInterface
{
    public function prepareNextStage(Championship $championship): void;
}
