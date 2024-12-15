<?php

namespace App\ConundrumSolver\Year2024;

use App\ConundrumSolver\AbstractConundrumSolver;
use App\Entity\Grid;
use App\Entity\Vector2D;
use App\Entity\Year2024\Day14\Robot;

/**
 * ❄️ Day 14: Restroom Redoubt ❄️
 *
 * @see https://adventofcode.com/2024/day/14
 */
final class Day14ConundrumSolver extends AbstractConundrumSolver
{
    private const int TEST_GRID_WIDTH = 11;
    private const int TEST_GRID_HEIGHT = 7;
    private const int GRID_WIDTH = 101;
    private const int GRID_HEIGHT = 103;

    private array $robots = [];
    private Grid $grid;

    public function __construct()
    {
        parent::__construct('2024', '14');
    }

    #[\Override]
    public function warmup(): void
    {
        if ($this->testMode) {
            $grid = new Grid(self::TEST_GRID_WIDTH - 1, self::TEST_GRID_HEIGHT - 1);
        } else {
            $grid = new Grid(self::GRID_WIDTH - 1, self::GRID_HEIGHT - 1);
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
        $quadrants = $this->getQuadrants();
        $result = array_fill_keys(['A', 'B', 'C', 'D'], 0);

        $positions = $this->simulateRobotsPositions(100);
        foreach ($positions as [$x, $y]) {
            foreach ($quadrants as $key => $quadrant) {
                if ($quadrant->isInside($x, $y)) {
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

    /**
     * @see https://en.wikipedia.org/wiki/Chinese_remainder_theorem
     */
    public function partTwo(): string|int
    {
        if ($this->testMode) {
            return self::NOT_TESTABLE;
        }

        // Determine at which time variance is at its lowest point for both X and Y
        $bestX = $this->getMinTimeThroughMinVariance(range(0, self::GRID_WIDTH - 1), true);
        $bestY = $this->getMinTimeThroughMinVariance(range(0, self::GRID_HEIGHT - 1), false);

        // Get inverse of width % height
        $inverse = (int) gmp_invert(self::GRID_WIDTH, self::GRID_HEIGHT);

        return $bestX + (($inverse * ($bestY - $bestX)) % self::GRID_HEIGHT) * self::GRID_WIDTH;
    }

    // //////////////
    // METHODS
    // //////////////

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

    /**
     * Simulate robots positions at time t, making sure
     * we stay within bounds by using modulo of height
     * and width and readjusting when values become
     * negative
     */
    private function simulateRobotsPositions(int $t): array
    {
        $robots = [];
        $w = $this->testMode ? self::TEST_GRID_WIDTH : self::GRID_WIDTH;
        $h = $this->testMode ? self::TEST_GRID_HEIGHT : self::GRID_HEIGHT;

        foreach ($this->robots as $robot) {
            $x = ($robot->x + $t * $robot->velocity->x) % $w;
            $y = ($robot->y + $t * $robot->velocity->y) % $h;

            $x += 0 > $x ? $w : 0;
            $y += 0 > $y ? $h : 0;

            $robots[] = [$x, $y];
        }

        return $robots;
    }

    private function getMinTimeThroughMinVariance(array $timeRange, bool $forX): int
    {
        $minT = null;
        $minVariance = PHP_INT_MAX;

        foreach ($timeRange as $t) {
            $variance = $this->getMinVarianceForTime($t, $forX);

            if ($minVariance > $variance) {
                $minVariance = $variance;
                $minT = $t;
            }
        }

        return $minT;
    }

    private function getMinVarianceForTime(int $t, bool $forX): float|int
    {
        $positions = $this->simulateRobotsPositions($t);

        return $this->getVariance(array_column($positions, $forX ? 0 : 1));
    }

    /**
     * Getting the average squared deviation
     * from the mean
     */
    private function getVariance(array $values): int|float
    {
        $meanValue = array_sum($values) / \count($values);
        $squaredDifferences = array_map(static function ($value) use ($meanValue) {
            return ($value - $meanValue) ** 2;
        }, $values);

        return array_sum($squaredDifferences) / \count($squaredDifferences);
    }
}
