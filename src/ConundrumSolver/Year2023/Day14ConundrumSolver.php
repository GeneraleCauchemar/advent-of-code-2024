<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2023;

use App\ConundrumSolver\AbstractConundrumSolver;
use App\Entity\CycleDetection\LinkedList;
use App\Service\CycleDetection;
use App\Service\MatrixHelper;

// /// Day 14: Parabolic Reflector Dish ///
// PART ONE: 107053, PART TWO: 88371
class Day14ConundrumSolver extends AbstractConundrumSolver
{
    private const int PART_TWO_CYCLES = 1000000000;

    private array $inputAsArray;
    private int $gridHeight;
    private int $gridWidth;

    public function __construct(private readonly CycleDetection $cycleDetection)
    {
        parent::__construct('2023', '14');
    }

    #[\Override]
    public function warmup(): void
    {
        $this->resetInputAsArray();

        $this->gridHeight = \count($this->inputAsArray);
        $this->gridWidth = \count($this->inputAsArray[0]);
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): string|int
    {
        $keys = $this->getKeys();
        $endGamePositionsOfRoundedRocks = array_fill_keys($keys, 0);

        $this->tiltNorth($keys, $endGamePositionsOfRoundedRocks);

        return $this->computeLoad($endGamePositionsOfRoundedRocks);
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): string|int
    {
        $this->resetInputAsArray();
        $linkedList = new LinkedList();

        for ($i = 0; $i < 201; $i++) {
            $endCyclePositionsOfRoundedRocks = $this->cycle();
            $load = $this->computeLoad($endCyclePositionsOfRoundedRocks);

            $linkedList->pushToBack($load);
        }

        [$length, $offset] = $this->cycleDetection->applyBrentsAlgorithm($linkedList->head);
        $n = $offset + ((self::PART_TWO_CYCLES - $offset) % $length);

        $this->resetInputAsArray();
        $endGamePositionsOfRoundedRocks = [];

        for ($i = 1; $i <= $n; $i++) {
            $endGamePositionsOfRoundedRocks = $this->cycle();
        }

        return $this->computeLoad($endGamePositionsOfRoundedRocks);
    }

    ////////////////
    // METHODS
    ////////////////

    private function resetInputAsArray(): void
    {
        $this->inputAsArray = array_map(
            static fn($line) => str_split($line),
            array_filter($this->getInput())
        );
    }

    private function cycle(): array
    {
        for ($i = 1; $i <= 4; $i++) {
            $keys = $this->getKeys($i);
            $endCyclePositionsOfRoundedRocks = array_fill_keys($keys, 0);

            match ($i) {
                1 => $this->tiltNorth($keys, $endCyclePositionsOfRoundedRocks),
                2 => $this->tiltWest($keys, $endCyclePositionsOfRoundedRocks),
                3 => $this->tiltSouth($keys, $endCyclePositionsOfRoundedRocks),
                4 => $this->tiltEast($keys, $endCyclePositionsOfRoundedRocks),
            };
        }

        return $endCyclePositionsOfRoundedRocks;
    }

    private function tiltNorth(array $keys, array &$endCyclePositionsOfRoundedRocks): void
    {
        $input = $this->inputAsArray;
        MatrixHelper::rotateMatrix($input);

        $this->tiltOnce($input, $keys, $endCyclePositionsOfRoundedRocks, rotated: true);

        // Rotate back
        MatrixHelper::rotateMatrix($input);

        $this->inputAsArray = $input;
    }

    private function tiltWest(array $keys, array &$endCyclePositionsOfRoundedRocks): void
    {
        $input = $this->inputAsArray;

        $this->tiltOnce($input, $keys, $endCyclePositionsOfRoundedRocks);

        $this->inputAsArray = $input;
    }

    private function tiltSouth(array $keys, array &$endCyclePositionsOfRoundedRocks): void
    {
        $input = $this->inputAsArray;
        MatrixHelper::rotateMatrix($input);

        $this->tiltOnce($input, $keys, $endCyclePositionsOfRoundedRocks, true, rotated: true);

        // Rotate back
        MatrixHelper::rotateMatrix($input);

        $this->inputAsArray = $input;
    }

    private function tiltEast(array $keys, array &$endCyclePositionsOfRoundedRocks): void
    {
        $input = $this->inputAsArray;

        $this->tiltOnce($input, $keys, $endCyclePositionsOfRoundedRocks, true);

        $this->inputAsArray = $input;
    }

    private function tiltOnce(
        array &$input,
        array $keys,
        array &$endCyclePositionsOfRoundedRocks,
        bool $readInReverse = false,
        bool $rotated = false
    ): void {
        foreach ($input as $xkey => $column) {
            $column = array_combine($keys, $column);

            if ($readInReverse) {
                $column = array_reverse($column, true);
            }

            $canRollXTimes = 0;
            $columnAfterMove = [];

            // Compute moves
            foreach ($column as $yKey => $symbol) {
                if (('O' === $symbol) && 0 < $canRollXTimes) {
                    $columnAfterMove[$readInReverse
                        ? ($yKey - $canRollXTimes)
                        : ($yKey + $canRollXTimes)] = 'O';
                    $columnAfterMove[$yKey] = '.';

                    continue;
                }

                $columnAfterMove[$yKey] = $symbol;

                if ('.' === $symbol) {
                    $canRollXTimes++;

                    continue;
                }

                $canRollXTimes = 0;
            }

            // Take note of rocks lines for load computing
            foreach (array_keys($columnAfterMove, 'O', true) as $position) {
                if (!$rotated) {
                    $position = $keys[$xkey];
                }

                $endCyclePositionsOfRoundedRocks[$position]++;
            }

            // Update positions in input for future moves
            $input[$xkey] = $readInReverse
                ? array_reverse($columnAfterMove, true)
                : $columnAfterMove;
        }
    }

    private function getKeys(int $i = 1): array
    {
        return (0 === $i % 2)
            ? range($this->gridWidth, 1)
            : range($this->gridHeight, 1);
    }

    private function computeLoad(array $endGamePositionsOfRoundedRocks): int
    {
        array_walk($endGamePositionsOfRoundedRocks, static function (&$value, $key) {
            $value *= $key;
        });

        return array_sum($endGamePositionsOfRoundedRocks);
    }
}
