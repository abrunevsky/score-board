<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Championship;
use App\Repository\ChampionshipRepository;
use App\Service\ChampionshipFactory;
use App\Service\ChampionshipProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ChampionshipController extends AbstractController
{
    private ChampionshipRepository $championshipRepository;
    private ChampionshipFactory $championshipFactory;
    private ChampionshipProcessor $championshipProcessor;

    public function __construct(
        ChampionshipRepository $championshipRepository,
        ChampionshipFactory $championshipFactory,
        ChampionshipProcessor $championshipProcessor
    ) {
        $this->championshipRepository = $championshipRepository;
        $this->championshipFactory = $championshipFactory;
        $this->championshipProcessor = $championshipProcessor;
    }

    /**
     * @Route("/championship/current", name="championship_get", methods={"GET"})
     */
    public function getCurrent(): JsonResponse
    {
        $championship = $this->championshipRepository->findCurrent();

        return new JsonResponse(
            $championship ? [
                'id' => $championship->getId(),
                'status' => $championship->getStatus(),
            ] : null
        );
    }

    /**
     * @Route("/championship/current", name="championship_create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $championship = $this->championshipRepository->findCurrent();
        $bidirectional = (bool) $request->request->get('bidirectional', false);

        if (
            $championship instanceof Championship
            && Championship::STATUS_OVER !== $championship->getStatus()
        ) {
            throw new ConflictHttpException('Current championship already exists and it has not been over yet.');
        }

        $championship = $this->championshipFactory->create($bidirectional);

        return new JsonResponse([
            'id' => $championship->getId(),
            'status' => $championship->getStatus(),
        ]);
    }

    /**
     * @Route("/championship/current", name="championship_increment", methods={"PUT"})
     */
    public function increment(Request $request): JsonResponse
    {
        $championship = $this->championshipRepository->findCurrent();
        $finalize = (bool) $request->request->get('finalize', false);

        if (
            null === $championship
            || Championship::STATUS_OVER === $championship->getStatus()
        ) {
            throw new ConflictHttpException('Current championship does not exist or it is over.');
        }

        if ($finalize) {
            $this->championshipProcessor->processAllSTeps($championship);
        } else {
            $this->championshipProcessor->processStep($championship);
        }

        return new JsonResponse([
            'id' => $championship->getId(),
            'status' => $championship->getStatus(),
        ]);
    }
}
