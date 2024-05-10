<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Tournament;
use App\Repository\TournamentRepository;
use App\Service\TournamentFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Annotation\Route;

class TournamentController extends AbstractController
{
    private TournamentRepository $tournamentRepository;
    private TournamentFactory $tournamentFactory;

    public function __construct(
        TournamentRepository $tournamentRepository,
        TournamentFactory $tournamentFactory
    ) {
        $this->tournamentRepository = $tournamentRepository;
        $this->tournamentFactory = $tournamentFactory;
    }

    /**
     * @Route("/tournament/current", name="tournament_create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $tournament = $this->tournamentRepository->findCurrent();
        $bidirectional = (bool) $request->request->get('bidirectional', false);

        if (
            $tournament instanceof Tournament
            && Tournament::STATUS_OVER !== $tournament->getStatus()
        ) {
            throw new ConflictHttpException('Current tournament already exists and it has not been over yet.');
        }

        $tournament = $this->tournamentFactory->create($bidirectional);

        return new JsonResponse([
            'message' => 'Create Tournament request',
            'tournamentId' => $tournament->getId(),
        ]);
    }

    /**
     * @Route("/tournament/current", name="tournament_increment", methods={"PUT"})
     */
    public function increment(Request $request): JsonResponse
    {
        $tournament = $this->tournamentRepository->findCurrent();
        $finalize = (bool) $request->request->get('finalize', false);

        if (
            null === $tournament
            || Tournament::STATUS_OVER === $tournament->getStatus()
        ) {
            throw new ConflictHttpException('Current tournament does not exist or it is over.');
        }

        return new JsonResponse([
            'message' => 'Increment current tournament!',
            'path' => 'src/Controller/TournamentController.php',
            'method' => __METHOD__,
        ]);
    }
}
