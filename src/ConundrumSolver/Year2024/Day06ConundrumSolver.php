<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2024;

use App\ConundrumSolver\AbstractConundrumSolver;
use App\Entity\PathFinding\PositionInterface;
use App\Entity\Year2024\Day06\Guard;
use App\Entity\Year2024\Day06\Obstacle;
use App\Entity\Year2024\Day06\Position;
use App\Exception\InfiniteLoopException;
use App\Service\CompassHelper;

// /// Day 6: Guard Gallivant ///

/**
 * Axes d'amélioration :
 * - nombres complexes ?
 * - éviter d'utiliser des objets ?
 * - bouger en ligne droite jusqu'à rencontrer un obstacle
 *   et éviter de vérifier à chaque point ? si deux lignes
 *   qui vont dans la même direction se chevauchent, boucle
 */
class Day06ConundrumSolver extends AbstractConundrumSolver
{
    private array $map;
    private array $obstacles;
    private Guard $guard;
    private array $visitedInPartOne;
    private array $visitedInPartTwo;
    private array $initialGuardPositions;

    public function __construct()
    {
        parent::__construct('2024', '06');
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): string|int
    {
        $this->map();
        $this->resetGuard();

        ini_set('xdebug.max_nesting_level', 6400);
        $this->walk(CompassHelper::NORTH);
        ini_restore('xdebug.max_nesting_level');

        return \count($this->visitedInPartOne);
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): string|int
    {
        $this->map();
        $checked = [];

        foreach ($this->visitedInPartOne as $visited) {
            $this->resetGuard();
            $this->resetPartTwo();

            if (
                $this->isStartingPosition($visited->column, $visited->row)
                || \in_array($this->getKey($visited), $checked, true)
            ) {
                continue;
            }

            $obstacle = new Obstacle($visited->row, $visited->column);
            $obstacleKey = $this->getKey($obstacle);
            $this->obstacles[$obstacleKey] = $obstacle;

            try {
                ini_set('xdebug.max_nesting_level', 10000);
                $this->walk(CompassHelper::NORTH, true);
                ini_restore('xdebug.max_nesting_level');
            } catch (InfiniteLoopException) {
                $checked[] = $this->getKey($visited);
            }

            unset($this->obstacles[$obstacleKey]);
        }

        return \count($checked);
    }

    ////////////////
    // METHODS
    ////////////////

    private function map(): void
    {
        foreach ($this->getInput() as $y => $line) {
            foreach (str_split($line) as $x => $value) {
                $position = match ($value) {
                    '#' => new Obstacle($y, $x),
                    '.' => new Position($y, $x),
                    '^' => new Position($y, $x, true),
                };
                $this->map[$y][$x] = $position;

                if ('^' === $value) {
                    $this->initialGuardPositions = ['x' => $x, 'y' => $y];
                }

                if ($position instanceof Obstacle) {
                    $this->obstacles[$this->getKey($position)] = $position;
                }
            }
        }
    }

    private function walk(string $direction, bool $isPartTwo = false): bool
    {
        $nextPosition = $this->getNextPosition($direction, $this->guard);
        if (null === $nextPosition) {
            return true; // on est au bord
        }

        if ($this->isObstacle($nextPosition)) {
            $direction = CompassHelper::getDirection90Degrees($direction);

            if ($isPartTwo) {
                $wouldMoveTo = $this->getNextPosition($direction, $this->guard);
                if (null === $wouldMoveTo) {
                    return true;
                }

                /** @var Position $wouldMoveTo */
                $wouldMoveTo->direction = $direction;
                if (\array_key_exists($this->getKey($wouldMoveTo, true), $this->visitedInPartTwo)) {
                    // Have we already come this way? If so, we're entering a loop
                    throw new InfiniteLoopException();
                }
            }

            $this->guard->turnTo($direction);

            return $this->walk($direction, $isPartTwo);
        }

        /** @var Position $nextPosition */
        $nextPosition->direction = $direction;
        $nextPosition->visited = true;

        if ($isPartTwo) {
            $this->visitedInPartTwo[$this->getKey($nextPosition, true)] = $nextPosition;
        } else {
            $this->visitedInPartOne[$this->getKey($nextPosition)] = $nextPosition;
        }

        $this->guard->moveTo($nextPosition);

        return $this->walk($direction, $isPartTwo);
    }

    private function getNextPosition(string $direction, PositionInterface $position): ?PositionInterface
    {
        [$x, $y] = CompassHelper::getDiffFromDirection($direction, $position->column, $position->row);

        return $this->map[$y][$x] ?? null;
    }

    private function isObstacle(PositionInterface $position): bool
    {
        return $position instanceof Obstacle || \array_key_exists($this->getKey($position), $this->obstacles);
    }

    private function getKey(PositionInterface $position, bool $withDirection = false): string
    {
        /** @var Position $position */
        $keys = [
            $position->row,
            $position->column,
        ];

        if ($withDirection) {
            $keys[] = $position->direction;
        }

        return implode('x', $keys);
    }

    private function resetGuard(): void
    {
        $this->guard = new Guard(
            $this->initialGuardPositions['y'],
            $this->initialGuardPositions['x'],
            CompassHelper::NORTH
        );
        $this->visitedInPartOne[$this->getKey($this->guard)] = $this->guard;
    }

    private function resetPartTwo(): void
    {
        $this->visitedInPartTwo = [];
    }

    private function isStartingPosition(int $x, int $y): bool
    {
        return $this->initialGuardPositions['x'] === $x && $this->initialGuardPositions['y'] === $y;
    }
}
