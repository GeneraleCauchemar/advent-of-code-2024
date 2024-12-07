<?php

namespace App\ConundrumSolver\Year2024;

use App\ConundrumSolver\AbstractConundrumSolver;
use App\Entity\Year2024\Day07\Equation;

// ❄️ Day 07: Bridge Repair ❄️
// Depth-first search
final class Day07ConundrumSolver extends AbstractConundrumSolver
{
    private const array CALLBACKS = [
        self::PART_ONE => ['add', 'multiply'],
        self::PART_TWO => ['add', 'multiply', 'concat'],
    ];

    private array $equations = [];
    private array $partOneResults = [];
    private array $partTwoResults = [];

    public function __construct()
    {
        parent::__construct('2024', '07');
    }

    #[\Override]
    public function warmup(): void
    {
        foreach ($this->getInput() as $line) {
            [$result, $values] = $this->getEquationParts($line);
            $this->equations[] = new Equation($result, $values);
        }
    }

    /////////////////
    // PART 1
    /////////////////

    public function partOne(): string|int
    {
        $i = 1;

        /** @var Equation $equation */
        foreach ($this->equations as $equation) {
            if ($this->isCombinationPossible(
                $equation,
                $i,
                $equation->operands[0],
                array_map(
                    fn ($callback) => fn($a, $b) => $this->$callback($a, $b),
                    self::CALLBACKS[self::PART_ONE]
                )
            )) {
                $this->partOneResults[] = $equation->result;
            }
        }

        return array_sum($this->partOneResults);
    }

    /////////////////
    // PART 2
    /////////////////

    public function partTwo(): string|int
    {
        $i = 1;

        /** @var Equation $equation */
        foreach ($this->equations as $equation) {
            if ($this->isCombinationPossible(
                $equation,
                $i,
                $equation->operands[0],
                array_map(
                    fn ($callback) => fn($a, $b) => $this->$callback($a, $b),
                    self::CALLBACKS[self::PART_TWO]
                )
            )) {
                $this->partTwoResults[] = $equation->result;
            }
        }

        return array_sum($this->partTwoResults);
    }

    /////////////////
    // METHODS
    /////////////////

    private function getEquationParts(string $equation): array
    {
        preg_match_all('/\d+/', $equation, $matches);

        return [
            (int) array_shift($matches[0]),
            array_map('\intval', $matches[0]),
        ];
    }

    private function isCombinationPossible(Equation $equation, int $i, int $partialResult, array $operations): bool
    {
        // Operations don't produce the right result
        if ($equation->result < $partialResult) {
            return false;
        }

        // Last operand
        if (\count($equation->operands) === $i) {
            return $equation->result === $partialResult;
        }

        // Iterates over operations and returns true as soon
        // as the callback is satisfied for one of them
        return array_any(
            $operations,
            fn($operation) => $this->isCombinationPossible(
                $equation,
                $i + 1,
                $operation($partialResult, $equation->operands[$i]),
                $operations
            )
        );
    }

    private function add(int $a, int $b): int
    {
        return $a + $b;
    }

    private function multiply(int $a, int $b): int
    {
        return $a * $b;
    }

    private function concat(int $a, int $b): int
    {
        return (int) ($a . $b);
    }
}
