<?php

namespace App\ConundrumSolver\Year2024;

use App\ConundrumSolver\AbstractConundrumSolver;
use App\Entity\PathFinding\AStar\AStar;
use App\Entity\Year2024\Day16\DomainLogic;
use App\Entity\Year2024\Day16\Node;
use App\Enum\Direction;

/**
 * ❄️ Day 16: ... ❄️.
 *
 * @see https://adventofcode.com/2024/day/16
 */
final class Day16ConundrumSolver extends AbstractConundrumSolver
{
    private Node $start;
    private Node $end;

    public function __construct()
    {
        parent::__construct('2024', '16');
    }

    // //////////////
    // PART 1
    // //////////////

    public function partOne(): string|int
    {
        $maze = [];
        foreach ($this->getInput() as $y => $line) {
            $maze[$y] = [];

            foreach (str_split($line) as $x => $symbol) {
                $node = new Node($x, $y, null, $symbol);

                if ($node->isStart()) {
                    $node->setDirection(Direction::East);
                    $this->start = $node;
                } elseif ($node->isEnd()) {
                    $this->end = $node;
                }

                $maze[$y][$x] = $node;
            }
        }

        $domainLogic = new DomainLogic($maze, true, 1000);
        $aStar = new AStar($domainLogic);
        $path = $aStar->run($this->start, $this->end);

        if (empty($path)) {
            throw new \LogicException();
        }

        return end($path)->getG();
    }

    // //////////////
    // PART 2
    // //////////////

    public function partTwo(): string|int
    {
        // We need a way to get all paths
        // with best score and then get
        // every single tile from them
        // but objects are updated and
        // I dont know how to reset them
        // properly
        // If I reset them, whoops, infinite
        // loop

        return self::UNDETERMINED;
    }

    // //////////////
    // METHODS
    // //////////////

}
