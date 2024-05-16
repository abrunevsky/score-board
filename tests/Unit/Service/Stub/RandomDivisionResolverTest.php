<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Stub;

use App\Entity\Team;
use App\Service\Stub\RandomDivisionResolver;
use PHPUnit\Framework\TestCase;

class RandomDivisionResolverTest extends TestCase
{
    public function testCreateContextWithException(): void
    {
        $resolver = new RandomDivisionResolver(['A', 'B'], 2);

        self::expectExceptionMessage('Context creation exception: minimal number of teams is 4, but 2 teams provided');

        $resolver->createContext([
            new Team('T1'),
            new Team('T1'),
        ]);
    }

    public function createContextProvider(): iterable
    {
        yield '4 teams' => [
            ['D'],
            [
                new Team('T1'),
                new Team('T1'),
                new Team('T3'),
            ],
            ['D', 'D', 'D'],
        ];

        yield '3 teams' => [
            ['A', 'B'],
            [
                new Team('T1'),
                new Team('T1'),
                new Team('T3'),
                new Team('T3'),
                new Team('T3'),
            ],
            ['A', 'A', 'A', 'B', 'B', 'B'],
        ];
    }

    /**
     * @dataProvider createContextProvider
     */
    public function testCreateContextWithSuccess(array $divisions, array $teams, array $expectedSortedPool): void
    {
        $resolver = new RandomDivisionResolver($divisions, 2);
        $context = $resolver->createContext($teams);

        sort($context->pool); // trick to check random distribution

        self::assertEquals($expectedSortedPool, $context->pool);
    }

    public function testResolveWithException(): void
    {
        $resolver = new RandomDivisionResolver(['D']);

        self::expectExceptionMessage('Context is wrong or its pool is empty');

        $resolver->resolveDivision(new Team('T1'), (object) []);
    }

    public function testResolveWithSuccess(): void
    {
        $resolver = new RandomDivisionResolver(['X']);
        $context = (object) ['pool' => ['X', 'X']];

        $division = $resolver->resolveDivision(new Team('T1'), $context);

        self::assertSame('X', $division);
        self::assertEquals((object) ['pool' => ['X']], $context);
    }
}
