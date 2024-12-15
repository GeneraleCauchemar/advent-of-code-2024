<?php

namespace App\ConundrumSolver\Year2024;

use App\ConundrumSolver\AbstractConundrumSolver;
use App\Entity\Grid;
use App\Entity\Vector2D;
use App\Entity\Year2024\Day14\Robot;

// ❄️ Day 14: Restroom Redoubt ❄️
// @see https://adventofcode.com/2024/day/14
final class Day14ConundrumSolver extends AbstractConundrumSolver
{
    private const int SECONDS_PART_ONE = 100;
    private const array TREE_PATTERN = [
        '.*..............*..............*.',
        '.*.............***.............*.',
        '.*............*****............*.',
        '.*...........*******...........*.',
        '.*..........*********..........*.',
    ];

    private array $robots = [];
    private Grid $grid;
    private array $print = [];

    public function __construct()
    {
        parent::__construct('2024', '14');
    }

    #[\Override]
    public function warmup(): void
    {
        if ($this->isTestMode()) {
            $grid = new Grid(10, 6);
        } else {
            $grid = new Grid(100, 102);
        }

        $this->grid = $grid;

        foreach ($this->getInput() as $line) {
            preg_match('/p=(?<p>-?\d+,-?\d+) v=(?<v>-?\d+,-?\d+)/', $line, $matches);

            $p = explode(',', $matches['p']);
            $v = explode(',', $matches['v']);
            $robot = new Robot(
                (int) $p[0],
                (int) $p[1],
                new Vector2D((int) $v[0], (int) $v[1]),
            );

            $this->robots[] = $robot;
        }
    }

    // //////////////
    // PART 1
    // //////////////

    public function partOne(): string|int
    {
        for ($i = 0; $i < self::SECONDS_PART_ONE; $i++) {
            foreach ($this->robots as $robot) {
                $x = $robot->x + $robot->velocity->x;
                $y = $robot->y + $robot->velocity->y;

                if ($this->grid->isInside($x, $y)) {
                    $robot->x = $x;
                    $robot->y = $y;

                    continue;
                }

                $this->teleport($x, $y, $robot);
            }
        }

        $quadrants = $this->getQuadrants();
        $result = array_fill_keys(['A', 'B', 'C', 'D'], 0);
        foreach ($this->robots as $robot) {
            foreach ($quadrants as $key => $quadrant) {
                if ($quadrant->isInside($robot->x, $robot->y)) {
                    $result[$key]++;

                    continue 2;
                }
            }
        }

        return array_product($result);
    }

    // //////////////
    // PART 2
    // //////////////

    public function partTwo(): string|int
    {
        // TODO : chinese remainder theorem
        if ($this->isTestMode()) {
            return 'not testable';
        }

        for ($i = 1; $i <= 7850; $i++) {
            $this->resetPrint();

            foreach ($this->robots as $robot) {
                $x = $robot->x + $robot->velocity->x;
                $y = $robot->y + $robot->velocity->y;

                if ($this->grid->isInside($x, $y)) {
                    $this->move($x, $y, $robot, true);

                    continue;
                }

                $this->teleport($x, $y, $robot, true);
            }

            if ($this->checkForCluster()) {
                return self::SECONDS_PART_ONE + $i;
            }
        }

        return parent::partTwo();
    }

    // //////////////
    // METHODS
    // //////////////

    private function teleport(int $x, int $y, Robot $robot, bool $partTwo = false): void
    {
        $x = match (true) {
            0 > $x => $this->grid->xMax + $x + 1,
            $this->grid->xMax < $x => $x - $this->grid->xMax - 1,
            default => $x
        };

        $y = match (true) {
            0 > $y => $this->grid->yMax + $y + 1,
            $this->grid->yMax < $y => $y - $this->grid->yMax - 1,
            default => $y
        };

        $this->move($x, $y, $robot, $partTwo);
    }

    private function move(int $x, int $y, Robot $robot, bool $partTwo = false): void
    {
        $robot->x = $x;
        $robot->y = $y;

        if ($partTwo) {
            $this->print[$y][$x] = '*';
        }
    }

    private function getQuadrants(): array
    {
        $leftX = [0, $this->grid->xMax / 2 - 1];
        $topY = [0, $this->grid->yMax / 2 - 1];
        $rightX = [$this->grid->xMax / 2 + 1, $this->grid->xMax];
        $bottomY = [$this->grid->yMax / 2 + 1, $this->grid->yMax];

        return [
            'A' => new Grid($leftX[1], $topY[1], $leftX[0], $topY[0]),
            'B' => new Grid($rightX[1], $topY[1], $rightX[0], $topY[0]),
            'C' => new Grid($leftX[1], $bottomY[1], $leftX[0], $bottomY[0]),
            'D' => new Grid($rightX[1], $bottomY[1], $rightX[0], $bottomY[0]),
        ];
    }

    private function resetPrint(): void
    {
        $this->print = array_fill_keys(range(0, $this->grid->yMax), []);

        foreach ($this->print as &$row) {
            $row = array_fill_keys(range(0, $this->grid->xMax), '.');
        }
    }

    private function checkForCluster(): bool
    {
        foreach ($this->print as $y => $row) {
            $row = implode('', $row);
            $check = strpos($row, self::TREE_PATTERN[0]);

            if ((false !== $check) && $this->scanForTree($y, $check, 1)) {
                return true;
            }
        }

        return false;
    }

    private function scanForTree(int $y, int $check, int $i): bool
    {
        $row = implode('', $this->print[$y + 1] ?? []);
        $nextCheck = strpos($row, self::TREE_PATTERN[$i]);

        if ($check === $nextCheck) {
            if (3 === $i) {
                return true;
            }

            return $this->scanForTree($y + 1, $nextCheck, $i + 1);
        }

        return false;
    }

    private function printTree(): void
    {
        for ($y = 0; $y < $this->grid->yMax; $y++) {
            print implode('', $this->print[$y]);
            echo PHP_EOL;
        }
    }
}
