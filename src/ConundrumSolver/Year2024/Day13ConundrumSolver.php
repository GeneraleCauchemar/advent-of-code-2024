<?php

namespace App\ConundrumSolver\Year2024;

use App\ConundrumSolver\AbstractConundrumSolver;
use App\Exception\UnwinnablePrizeException;

/**
 * ❄️ Day 13: Claw Contraption ❄️
 *
 * @see https://adventofcode.com/2024/day/13
 */
final class Day13ConundrumSolver extends AbstractConundrumSolver
{
    private const int A_PRICE = 3;
    private const int PRIZE_UPDATE = 10000000000000;
    private array $machineConfigurations = [];

    public function __construct()
    {
        parent::__construct('2024', '13', PHP_EOL . PHP_EOL);
    }

    #[\Override]
    public function warmup(): void
    {
        foreach ($this->getInput() as $machineConf) {
            [$A, $B, $prize] = explode(PHP_EOL, $machineConf);

            $A = $this->getCoordinates($A);
            $B = $this->getCoordinates($B);
            $prize = $this->getCoordinates($prize);

            $this->machineConfigurations[] = [
                'A'            => $A,
                'B'            => $B,
                'prize'        => $prize,
                'updatedPrize' => $this->updatePrize($prize),
            ];
        }
    }

    // //////////////
    // PART 1
    // //////////////

    public function partOne(): string|int
    {
        $tokensByWinnableRound = [];
        foreach ($this->machineConfigurations as $config) {
            try {
                $minPushes = $this->solve($config['A'], $config['B'], $config['prize']);
                $tokensByWinnableRound[] = $minPushes['A'] * self::A_PRICE + $minPushes['B'];
            } catch (UnwinnablePrizeException) {
                // noop
            }
        }

        return array_sum($tokensByWinnableRound);
    }

    // //////////////
    // PART 2
    // //////////////

    public function partTwo(): string|int
    {
        $tokensByWinnableRound = [];
        foreach ($this->machineConfigurations as $config) {
            try {
                $minPushes = $this->solve($config['A'], $config['B'], $config['updatedPrize']);
                $tokensByWinnableRound[] = $minPushes['A'] * self::A_PRICE + $minPushes['B'];
            } catch (UnwinnablePrizeException) {
                // noop
            }
        }

        return array_sum($tokensByWinnableRound);
    }

    // //////////////
    // METHODS
    // //////////////

    private function getCoordinates(string $line): array
    {
        preg_match('/X[+=](?<x>\d+), Y[+=](?<y>\d+)/', $line, $matches);

        return [
            'x' => (int) $matches['x'],
            'y' => (int) $matches['y'],
        ];
    }

    private function updatePrize(array $prize): array
    {
        array_walk($prize, static function (&$value) {
            $value += self::PRIZE_UPDATE;
        });

        return $prize;
    }

    /**
     * @throws UnwinnablePrizeException
     */
    private function solve(array $A, array $B, array $prize): array
    {
        // ax + by = c & dx + ey = f
        $a = $A['x'];
        $b = $B['x'];
        $c = $prize['x'];
        $d = $A['y'];
        $e = $B['y'];
        $f = $prize['y'];

        $determinant = $a * $e - $b * $d;
        if (0 === $determinant) {
            throw new \LogicException();
        }

        $result = [
            'A' => ($c * $e - $b * $f) / $determinant,
            'B' => ($a * $f - $c * $d) / $determinant,
        ];

        if (!\is_int($result['A']) || !\is_int($result['B'])) {
            throw new UnwinnablePrizeException();
        }

        return $result;
    }
}
