<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2023;

use App\ConundrumSolver\AbstractConundrumSolver;

// /// Day 6: Wait For It ///
class Day06ConundrumSolver extends AbstractConundrumSolver
{
    private array $winningCombinationsArray = [];
    private int $winningCombinations = 0;

    public function __construct()
    {
        parent::__construct('2023', '06', keepAsString: true);
    }

    #[\Override]
    public function warmup(): void
    {
    }

    ////////////////
    // PART 1
    ////////////////

    #[\Override]
    public function partOne(): string|int
    {
        preg_match_all('/\d+/', $this->getInput(), $matches, PREG_SET_ORDER);
        array_walk($matches, static function (array &$match): void {
            $match = $match[0];
        });

        [$times, $records] = array_chunk($matches, \count($matches) / 2);

        $this->computeWinningCombinations($times, $records);

        return array_product($this->winningCombinationsArray);
    }

    ////////////////
    // PART 2
    ////////////////

    #[\Override]
    public function partTwo(): string|int
    {
        $input = explode(PHP_EOL, $this->getInput());
        array_walk($input, static function (string &$match): void {
            $match = filter_var($match, FILTER_SANITIZE_NUMBER_INT);
        });

        [$time, $record] = $input;

        $this->computeWinningCombinations([$time], [$record]);

        return $this->winningCombinations;
    }

    ////////////////
    // METHODS
    ////////////////

    private function computeWinningCombinations(array $times, array $records): void
    {
        foreach ($times as $key => $time) {
            $this->winningCombinations = 0;
            $best = $records[$key];

            for ($held = 1; $held <= $time; $held++) {
                $remaining = $time - $held;

                if ($best < ($held * $remaining)) {
                    $this->winningCombinations++;
                }
            }

            if (0 < $this->winningCombinations) {
                $this->winningCombinationsArray[] = $this->winningCombinations;
            }
        }
    }
}
