<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2023;

use App\ConundrumSolver\AbstractConundrumSolver;
use App\Service\MatrixHelper;

// /// Day 13: Point of Incidence ///
class Day13ConundrumSolver extends AbstractConundrumSolver
{
    private int $rowsAbove;
    private int $columnsToTheLeft;
    private array $partOneSymetryAxis = [];

    public function __construct(string $year, string $day)
    {
        parent::__construct($year, $day, PHP_EOL . PHP_EOL);
    }

    #[\Override]
    public function warmup(): void
    {
        $this->reset();
    }

    ////////////////
    // PART 1
    ////////////////

    #[\Override]
    public function partOne(): string|int
    {
        $this->readInput();

        return $this->getResult();
    }

    ////////////////
    // PART 2
    ////////////////

    #[\Override]
    public function partTwo(): string|int
    {
        $this->reset();
        $this->readInput(self::PART_TWO);

        return $this->getResult();
    }

    ////////////////
    // METHODS
    ////////////////

    private function reset(): void
    {
        $this->rowsAbove = 0;
        $this->columnsToTheLeft = 0;
    }

    private function readInput(int $part = self::PART_ONE): void
    {
        foreach ($this->getInput() as $id => $pattern) {
            $pattern = array_map(
                static fn($line) => str_split($line),
                array_filter(explode(PHP_EOL, $pattern))
            );

            // Read pattern horizontally
            $this->readPattern($id, $pattern, \count($pattern), part: $part);

            // Read pattern vertically
            MatrixHelper::rotateMatrix($pattern);
            $this->readPattern($id, $pattern, \count($pattern), false, $part);
        }
    }

    private function readPattern(
        int $patternId,
        array $lines,
        int $width,
        bool $horizontally = true,
        int $part = self::PART_ONE
    ): void {
        foreach ($lines as $i => $iValue) {
            $j = $i + 1;

            if ($j < $width) {
                $differences = $this->countDifferences($iValue, $lines[$j]);
                $areEqual = $this->linesAreEqual($differences, $part);
                $smudgeAlreadyFixed = self::PART_ONE === $part || 1 === $differences; // Only one allowed on part two
                $lineAlreadyIdentified = self::PART_TWO === $part
                    && $this->partOneSymetryAxis[$patternId] === $this->getAxisIdentifier($horizontally, $i, $j);

                if ($areEqual && !$lineAlreadyIdentified) {
                    // How close to the border are we = how many lines must be mirrored?
                    $mustMirror = min(\count(range(0, $i)), \count(range($j, $width - 1)));
                    $isFullReflection = true;

                    for ($iMirror = 1; $iMirror < $mustMirror; $iMirror++) {
                        $differences = $this->countDifferences($lines[$i - $iMirror], $lines[$j + $iMirror]);

                        if (1 === $differences && !$smudgeAlreadyFixed) {
                            $smudgeAlreadyFixed = true;

                            continue;
                        }

                        if (0 < $differences) {
                            $isFullReflection = false;

                            break;
                        }
                    }

                    if ($isFullReflection) {
                        if ($horizontally) {
                            $this->rowsAbove += ($i + 1);
                        } else {
                            $this->columnsToTheLeft += ($i + 1);
                        }

                        /**
                         * Keeping track of axis to ignore
                         * in part two
                         */
                        if (self::PART_ONE === $part) {
                            $this->partOneSymetryAxis[$patternId] = $this->getAxisIdentifier($horizontally, $i, $j);
                        }
                    }
                }
            }
        }
    }

    private function countDifferences(array $line1, array $line2): int
    {
        return \count(array_diff_assoc($line1, $line2));
    }

    /**
     * Lines are equal if there are no differences between them ;
     * one difference on the same key is considered a smudge in
     * part two so lines will be deemed equal
     */
    private function linesAreEqual(int $differences, int $part): bool
    {
        return 0 === $differences || (1 === $differences && self::PART_TWO === $part);
    }

    private function getAxisIdentifier(bool $horizontally, int $i, int $j): string
    {
        return \sprintf('%s%s%s', $horizontally ? 'H' : 'V', $i, $j);
    }

    private function getResult(): int
    {
        return (100 * $this->rowsAbove) + $this->columnsToTheLeft;
    }
}
