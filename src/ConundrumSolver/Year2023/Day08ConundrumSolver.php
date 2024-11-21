<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2023;

use App\ConundrumSolver\AbstractConundrumSolver;
use App\Entity\Year2023\Day08\Node;

// /// Day 8: Haunted Wasteland ///
class Day08ConundrumSolver extends AbstractConundrumSolver
{
    private array $instructions;
    private int $instructionsLength;
    private array $nodes;
    private array $startingNodes;

    public function __construct(string $year, string $day)
    {
        parent::__construct($year, $day);
    }

    #[\Override]
    public function warmup(): void
    {
        if (!$this->isTestMode()) {
            $this->initVariables();
        }
    }

    ////////////////
    // PART 1
    ////////////////

    #[\Override]
    public function partOne(): string|int
    {
        if ($this->isTestMode()) {
            $this->initVariables();
        }

        $i = 0;
        $moves = 0;
        $node = $this->nodes['AAA'];

        while ('ZZZ' !== $node->name) {
            $this->computeMove($i, $node, $moves);
        }

        return $moves;
    }

    ////////////////
    // PART 2
    ////////////////

    #[\Override]
    public function partTwo(): string|int
    {
        if ($this->isTestMode()) {
            $this->initVariables(self::PART_TWO);
        }

        $paths = [];

        foreach ($this->startingNodes as $node) {
            $i = 0;
            $moves = 0;

            while (!str_ends_with($node->name, 'Z')) {
                $this->computeMove($i, $node, $moves);
            }

            $paths[] = gmp_init($moves);
        }

        return (int) $this->getLCM($paths);
    }

    ////////////////
    // METHODS
    ////////////////

    private function initVariables(int $part = self::PART_ONE): void
    {
        $input = $this->getInput($part);
        $this->instructions = str_split((string) array_shift($input));
        $this->instructionsLength = \count($this->instructions);
        $this->nodes = [];
        $this->startingNodes = [];

        array_walk($input, function (string $line): void {
            preg_match('/(?<node>[A-Z\d]+) = \((?<left>[A-Z\d]+), (?<right>[A-Z\d]+)\)/', $line, $matches);
            $node = new Node($matches['node'], $matches['left'], $matches['right']);
            $this->nodes[$node->name] = $node;

            if (str_ends_with($node->name, 'A')) {
                $this->startingNodes[$node->name] = $node;
            }
        });
    }

    private function computeMove(int &$i, Node &$node, int &$moves): void
    {
        $instructionKey = $i % $this->instructionsLength;
        $nextMove = $node->getInstruction($this->instructions[$instructionKey]);
        $node = $this->nodes[$nextMove];

        $i++;
        $moves++;
    }

    private function getLCM(array $paths): \GMP
    {
        // Calculating the LCM from all shortest paths
        return array_reduce($paths, static fn(\GMP $carry, \GMP $item): \GMP => gmp_lcm($carry, $item), $paths[0]);
    }
}
