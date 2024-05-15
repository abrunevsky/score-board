<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Championship;
use App\Entity\Play;
use App\Entity\PlayingTeam;
use App\Entity\Team;
use App\Repository\TeamRepository;
use App\Service\ChampionshipFactory;
use App\Service\PlayingTimeResolver;
use App\Service\TeamDivisionResolver;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class ChampionshipFactoryTest extends TestCase
{
    private ChampionshipFactory $championshipFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $teamRepositoryMock = $this->createMock(TeamRepository::class);
        $teamRepositoryMock->expects(self::once())
            ->method('findAllSorted')
            ->willReturn([
                self::createTestTeam(1, 'A-Team 1'),
                self::createTestTeam(2, 'A-Team 2'),
                self::createTestTeam(3, 'B-Team 3'),
                self::createTestTeam(4, 'B-Team 4'),
                self::createTestTeam(5, 'A-Team 5'),
            ])
        ;

        $divisionResolverMock = $this->createMock(TeamDivisionResolver::class);
        $divisionResolverMock->expects(self::exactly(5))
            ->method('resolveDivision')
            ->willReturnCallback(static function (Team $team) {
                return substr($team->getName(), 0, 1);
            })
        ;

        $entityManager = $this->createMock(EntityManager::class);
        $entityManager->expects(self::once())
            ->method('wrapInTransaction')
            ->willReturnCallback(function (callable $transaction) {
                return $transaction();
            })
        ;

        $timeResolverMock = $this->createMock(PlayingTimeResolver::class);
        $timeResolverMock->expects(self::atLeast(4))
            ->method('resolvePlayingTime')
            ->willReturn(new \DateTimeImmutable('2000-01-01T10:00:00+00:00'))
        ;

        $this->championshipFactory = new ChampionshipFactory(
            $teamRepositoryMock,
            $entityManager,
            $divisionResolverMock,
            $timeResolverMock,
        );

    }

    public function testCreateChampionshipWillReturnSuccess(): void
    {
        $championship = $this->championshipFactory->create(false);

        self::assertInstanceOf(Championship::class, $championship);
        self::assertSame(Championship::STATUS_PLAY, $championship->getStatus());
    }

    public function testCreateChampionshipWithPlayingTeamsWillReturnSuccess(): void
    {
        $championship = $this->championshipFactory->create(false);
        $playingTeams = $championship->getPlayingTeams();

        self::assertCount(5, $playingTeams);

        $playingTeam3 = $playingTeams->get(2);
        self::assertInstanceOf(PlayingTeam::class, $playingTeam3);
        self::assertSame($championship, $playingTeam3->getChampionship());
        self::assertSame('B', $playingTeam3->getDivision());
        self::assertSame('B-Team 3', $playingTeam3->getTeam()->getName());
    }

    public function testCreateUnidirectionalChampionshipWillReturnSuccess(): void
    {
        $championship = $this->championshipFactory->create(false);
        $plays = $championship->getPlays();

        self::assertCount(4, $plays);

        $play3 = $plays->get(2);
        self::assertInstanceOf(Play::class, $play3);

        self::assertSame($championship, $play3->getChampionship());
        self::assertSame(false, $play3->isCompleted());
        self::assertSame(0, $play3->getHostScore());
        self::assertSame(0, $play3->getGuestScore());
        self::assertSame('2000-01-01T10:00:00+00:00', $play3->getPlayAt()->format(DATE_ATOM));

        self::assertSame('A-Team 2', $play3->getHost()->getName());
        self::assertSame('A-Team 5', $play3->getGuest()->getName());
    }

    public function testCreateBidirectionalChampionshipWillReturnSuccess(): void
    {
        $championship = $this->championshipFactory->create(true);
        $plays = $championship->getPlays();
        self::assertCount(8, $plays);

        $play7 = $plays->get(6);
        self::assertInstanceOf(Play::class, $play7);
        self::assertSame($championship, $play7->getChampionship());
        self::assertSame(false, $play7->isCompleted());
        self::assertSame(0, $play7->getHostScore());
        self::assertSame(0, $play7->getGuestScore());

        self::assertSame('B-Team 3', $play7->getHost()->getName());
        self::assertSame('B-Team 4', $play7->getGuest()->getName());
    }

    private static function createTestTeam(int $id, string $name): Team
    {
        $team = new Team($name);
        $idReflection = new \ReflectionProperty($team, 'id');
        $idReflection->setAccessible(true);
        $idReflection->setValue($team, $id);
        $idReflection->setAccessible(false);

        return $team;
    }
}
