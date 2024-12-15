<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2024;

use App\ConundrumSolver\AbstractConundrumSolver;

// /// Day 3: Mull It Over ///
class Day03ConundrumSolver extends AbstractConundrumSolver
{
    public function __construct()
    {
        parent::__construct('2024', '03');
    }

    private array $mulInstructions = [];

    public function warmup(): void
    {
        $this->prepareInstructions();
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): string|int
    {
        $results = [];

        foreach ($this->mulInstructions as $instruction) {
            $results[] = array_sum($instruction);
        }

        return array_sum($results);
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): string|int
    {
        // Two sets on test input
        if ($this->testMode) {
            $this->prepareInstructions(true);
        }

        $results = [];
        $enabled = true;

        foreach ($this->getInput(self::PART_TWO) as $key => $memoryLine) {
            $subResults = [];
            $switches = [0 => $enabled];

            preg_match_all("/(?<capture>(don't|do)\(\))/", $memoryLine, $matches, PREG_OFFSET_CAPTURE);

            foreach ($matches['capture'] as $match) {
                $switches[$match[1]] = 'do()' === $match[0];
            }

            foreach ($this->mulInstructions[$key] as $mKey => $mulResult) {
                if ($this->isEnabled($mKey, $switches)) {
                    $subResults[] = $mulResult;
                }
            }

            // Keep state for next memory line
            $enabled = end($switches);
            $results[] = array_sum($subResults);
        }

        return array_sum($results);
    }

    ////////////////
    // METHODS
    ////////////////

    private function prepareInstructions(bool $partTwo = false): void
    {
        foreach ($this->getInput($partTwo ? self::PART_TWO : self::PART_ONE) as $key => $memoryLine) {
            preg_match_all(
                '/mul\((?<a>\d{1,3}),(?<b>\d{1,3})\)/',
                $memoryLine,
                $instructions,
                PREG_SET_ORDER | PREG_OFFSET_CAPTURE
            );

            $this->mulInstructions[$key] = [];

            foreach ($instructions as $instruction) {
                $offset = $instruction[0][1];
                $this->mulInstructions[$key][$offset] = (int) $instruction['a'][0] * (int) $instruction['b'][0];
            }
        }
    }

    private function isEnabled(int $key, array $instructions): bool
    {
        if (\array_key_exists($key, $instructions)) {
            return $instructions[$key];
        }

        $closest = null;
        foreach ($instructions as $iKey => $instruction) {
            if ($key < $iKey) {
                break;
            }

            $closest = $instruction;
        }

        return $closest;
    }
}
