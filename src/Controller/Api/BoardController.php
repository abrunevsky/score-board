<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\ChampionshipRepository;
use App\Repository\TeamRepository;
use App\Service\Format\BoardChampionshipFormatter;
use App\Service\Format\BoardTeamFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BoardController extends AbstractController
{
    private ChampionshipRepository $championshipRepository;
    private TeamRepository $teamRepository;
    private BoardChampionshipFormatter $championshipFormatter;
    private BoardTeamFormatter $teamFormatter;

    public function __construct(
        ChampionshipRepository $championshipRepository,
        TeamRepository $teamRepository,
        BoardChampionshipFormatter $championshipFormatter,
        BoardTeamFormatter $teamFormatter
    ) {
        $this->teamRepository = $teamRepository;
        $this->championshipRepository = $championshipRepository;
        $this->championshipFormatter = $championshipFormatter;
        $this->teamFormatter = $teamFormatter;
    }

    /**
     * @Route("/api/board/current", name="board_championship", methods={"GET"})
     */
    public function getCurrentChampionship(): JsonResponse
    {
        $teams = $this->teamRepository->findAllSorted();
        $championship = $this->championshipRepository->findCurrent();

        return $this->json([
            'teams' => $this->teamFormatter->formatArray($teams),
            'championship' => $championship
                ? $this->championshipFormatter->format($championship)
                : null,
        ]);
    }
}
