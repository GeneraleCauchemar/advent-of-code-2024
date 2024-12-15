<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2024;

use App\ConundrumSolver\AbstractConundrumSolver;
use App\Entity\Year2024\Day04\DomainLogic;
use App\Entity\Year2024\Day04\Position;
use App\Service\CompassHelper;

/**
 * ❄️ Day 4: Ceres Search ❄️
 *
 * @see https://adventofcode.com/2024/day/7
 */
final class Day04ConundrumSolver extends AbstractConundrumSolver
{
    private DomainLogic $domainLogic;
    private array $xPositions;
    private array $aPositions;

    public function __construct()
    {
        parent::__construct('2024', '04');
    }

    public function warmup(): void
    {
        $map = $this->map();
        $this->domainLogic = new DomainLogic($map);
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): string|int
    {
        $counter = 0;

        foreach ($this->xPositions as $xPos) {
            $nodes = $this->domainLogic->getAdjacentNodes($xPos, Position::M);

            foreach ($nodes as $direction => $node) {
                if ($this->isWordComplete($node, $direction)) {
                    $counter++;
                }
            }
        }

        return $counter;
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): string|int
    {
        $counter = 0;

        foreach ($this->aPositions as $aPos) {
            $mNodes = $this->domainLogic->getAdjacentNodes($aPos, Position::M, false);
            if (2 !== \count($mNodes)) {
                continue;
            }

            $sNodes = $this->domainLogic->getAdjacentNodes($aPos, Position::S, false);
            if (2 !== \count($sNodes)) {
                continue;
            }

            foreach (array_keys($mNodes) as $direction) {
                if (!\array_key_exists(CompassHelper::getOppositeDirection($direction), $sNodes)) {
                    continue 2;
                }
            }

            $counter++;
        }

        return $counter;
    }

    ////////////////
    // METHODS
    ////////////////

    private function map(): array
    {
        $map = [];

        foreach ($this->getInput() as $y => $line) {
            $row = [];

            foreach (str_split((string) $line) as $x => $letter) {
                $position = new Position($y, $x, $letter);
                $row[$x] = $position;

                if (Position::X === $letter) {
                    $this->xPositions[] = $position;
                }

                if (Position::A === $letter) {
                    $this->aPositions[] = $position;
                }
            }

            $map[$y] = $row;
        }

        return $map;
    }

    private function isWordComplete(Position $position, string $direction): bool
    {
        if (Position::S === $position->letter) {
            return true;
        }

        $next = $this->domainLogic->getNextPositionForSameDirection($position, $direction);
        if (null === $next) {
            return false;
        }

        return $this->isWordComplete($next, $direction);
    }
}
