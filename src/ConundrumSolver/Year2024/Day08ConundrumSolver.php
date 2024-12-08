<?php

namespace App\ConundrumSolver\Year2024;

use App\ConundrumSolver\AbstractConundrumSolver;

// ❄️ Day 8: Resonant Collinearity ❄️
// @see https://adventofcode.com/2024/day/8
final class Day08ConundrumSolver extends AbstractConundrumSolver
{
    private int $minX = 0;
    private int $maxX = 0;
    private int $minY = 0;
    private int $maxY = 0;
    private array $antennas = [];
    private array $antinodes;
    private array $newPairs;

    public function __construct()
    {
        parent::__construct('2024', '08');
    }

    #[\Override]
    public function warmup(): void
    {
        $this->maxX = \strlen($this->getInput()[0]) - 1;
        $this->maxY = \count($this->getInput()) - 1;

        foreach ($this->getInput() as $y => $row) {
            $antennas = array_filter(str_split($row), static function ($element) {
                return '.' !== $element;
            });

            foreach ($antennas as $x => $antenna) {
                if (!\array_key_exists($antenna, $this->antennas)) {
                    $this->antennas[$antenna] = [];
                }

                $this->antennas[$antenna][] = ['x' => $x, 'y' => $y];
            }
        }

        $this->antinodes = [];
        $this->newPairs = [];

        foreach ($this->antennas as $antennasByFrequency) {
            $pairs = $this->pairAntennas($antennasByFrequency);

            foreach ($pairs as [$a, $b]) {
                $nodes = [
                    'a' => $this->getAntinode($a, $b),
                    'b' => $this->getAntinode($b, $a),
                ];

                foreach ($nodes as $center => $node) {
                    if ($this->isOutOfBounds($node)) {
                        $this->newPairs[] = [$a, $b]; // We still want both for part 2

                        continue;
                    }

                    $this->antinodes[] = $node;
                    $this->newPairs[] = [$node, 'a' === $center ? $a : $b];
                }
            }
        }
    }

    // //////////////
    // PART 1
    // //////////////

    public function partOne(): string|int
    {
        return $this->getResult();
    }

    // //////////////
    // PART 2
    // //////////////

    public function partTwo(): string|int
    {
        $carry = [];

        // Get pairs in both directions
        foreach ($this->newPairs as [$a, $b]) {
            $carry[] = $this->getAntinodes($a, $b);
            $carry[] = $this->getAntinodes($b, $a);
        }

        $this->antinodes = array_merge($this->antinodes, ...$carry);

        return $this->getResult();
    }

    // //////////////
    // METHODS
    // //////////////

    private function pairAntennas(array $antennas, int $index = 0, array $result = []): array
    {
        if ($index < \count($antennas)) {
            $first = $antennas[$index];

            for ($i = $index + 1, $iMax = \count($antennas); $i < $iMax; $i++) {
                $result[] = [$first, $antennas[$i]];
            }

            return $this->pairAntennas($antennas, $index + 1, $result);
        }

        return $result;
    }

    private function isOutOfBounds(array $node): bool
    {
        return $this->minX > $node['x']
            || $this->maxX < $node['x']
            || $this->minY > $node['y']
            || $this->maxY < $node['y'];
    }

    private function getAntinode(array $a, array $b): array
    {
        return [
            'x' => 2 * $a['x'] - $b['x'],
            'y' => 2 * $a['y'] - $b['y'],
        ];
    }

    private function getAntinodes(array $a, array $b, array $carry = []): array
    {
        $carry[] = $a;

        $node = $this->getAntinode($a, $b);
        if ($this->isOutOfBounds($node)) {
            return $carry;
        }

        return $this->getAntinodes($node, $a, $carry);
    }

    private function getResult(): int
    {
        return \count(array_unique($this->antinodes, SORT_REGULAR));
    }
}
