<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Championship;
use App\Service\ProgressHandler\ChampionshipHandlerInterface;

class ChampionshipProcessor
{
    /**
     * @var non-empty-array<ChampionshipHandlerInterface>
     */
    private array $handlers;

    public function __construct(\IteratorAggregate $locator)
    {
        $this->handlers = iterator_to_array($locator->getIterator());

        if (0 === count($this->handlers)) {
            throw new \LogicException('Something went wrong! No one "ChampionshipHandlerInterface" found.');
        }
    }

    public function processStep(Championship $championship): void
    {
        foreach ($this->handlers as $handler) {
            if ($handler->canProcess($championship)) {
                $handler->process($championship);

                return;
            }
        }
    }

    public function processAllSteps(Championship $championship): void
    {
        foreach ($this->handlers as $handler) {
            while ($handler->canProcess($championship)) {
                $handler->process($championship);
            }
        }
    }
}
