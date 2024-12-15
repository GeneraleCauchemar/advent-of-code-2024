<?php

namespace App\ConundrumSolver\Year2024;

use App\ConundrumSolver\AbstractConundrumSolver;
use App\Entity\Vector2D;
use App\Enum\Direction;

/**
 * ❄️ Day 15: Warehouse Woes ❄️
 *
 * @see https://adventofcode.com/2024/day/15
 */
final class Day15ConundrumSolver extends AbstractConundrumSolver
{
    private const string WALL = '#';
    private const string BOX = 'O';
    private const string EMPTY_SPACE = '.';
    private const string ROBOT = '@';
    private const string CRATE = '[]';

    private array $map;
    private array $robot;
    private array $moves;
    private array $cratesToMove;

    public function __construct()
    {
        parent::__construct('2024', '15', PHP_EOL . PHP_EOL);
    }

    #[\Override]
    public function warmup(): void
    {
        $this->resetMap($this->getInput());
    }

    // //////////////
    // PART 1
    // //////////////

    public function partOne(): string|int
    {
        while (!empty($this->moves)) {
            $move = array_shift($this->moves);
            [$x, $y] = $this->robot;

            $this->computeSimpleMove($move, $x, $y);
        }

        return $this->getResult();
    }

    // //////////////
    // PART 2
    // //////////////

    public function partTwo(): string|int
    {
        $this->resetMap($this->getInput(), true);

        while (!empty($this->moves)) {
            $move = array_shift($this->moves);
            [$x, $y] = $this->robot;

            $this->computeComplexMove($move, $x, $y, Direction::getDirectionFromVector($move));
        }

        return $this->getResult(true);
    }

    // //////////////
    // METHODS
    // //////////////

    private function resetMap(array $input, bool $partTwo = false): void
    {
        $this->robot = [];
        $this->map = [];

        $mapRows = explode(PHP_EOL, $input[0]);
        foreach ($mapRows as $y => $row) {
            $this->map[$y] = $this->splitRow($row, $partTwo);

            if (empty($this->robot) && str_contains($row, self::ROBOT)) {
                $this->robot = [
                    strpos($partTwo ? implode('', $this->map[$y]) : $row, self::ROBOT),
                    $y,
                ];
            }
        }

        $this->moves = [];

        $moves = str_replace(PHP_EOL, '', $input[1]);
        foreach (str_split($moves) as $move) {
            $vector = match ($move) {
                '^' => Direction::North->getVector(),
                '>' => Direction::East->getVector(),
                'v' => Direction::South->getVector(),
                '<' => Direction::West->getVector(),
            };

            $this->moves[] = $vector;
        }
    }

    private function splitRow(string $row, bool $partTwo = false): array
    {
        if (!$partTwo) {
            return str_split($row);
        }

        $return = '';
        foreach (str_split($row) as $char) {
            $return .= match ($char) {
                self::WALL => self::WALL . self::WALL,
                self::BOX => self::CRATE,
                self::EMPTY_SPACE => self::EMPTY_SPACE . self::EMPTY_SPACE,
                self::ROBOT => self::ROBOT . self::EMPTY_SPACE
            };
        }

        return str_split($return);
    }

    /**
     * If you'd move into an object, recursively check if it can move as well
     * Only move when given the go ahead
     */
    private function computeSimpleMove(Vector2D $move, int $x, int $y): bool
    {
        [$toX, $toY] = $this->getNextPosition($move, $x, $y);
        $wouldMoveInto = $this->map[$toY][$toX];

        if (
            self::WALL !== $wouldMoveInto
            && (self::EMPTY_SPACE === $wouldMoveInto || true === $this->computeSimpleMove($move, $toX, $toY))
        ) {
            $this->move([$x, $y], [$toX, $toY]);

            return true;
        }

        return false;
    }

    /**
     * As always, move if next position is free, stop if it's a wall.
     * If it contains half of a crate, check if the whole crate (and
     * any crate it might push) is free to move; if so, move all of
     * them and then move robot
     */
    private function computeComplexMove(Vector2D $vector, int $x, int $y, Direction $direction): void
    {
        [$toX, $toY] = $this->getNextPosition($vector, $x, $y);
        $wouldMoveInto = $this->map[$toY][$toX];
        if (self::WALL === $wouldMoveInto) {
            return;
        }

        if (self::EMPTY_SPACE === $wouldMoveInto) {
            $this->move([$x, $y], [$toX, $toY]);

            return;
        }

        $this->cratesToMove = [];
        $canMove = \in_array($direction, [Direction::North, Direction::South], true)
            ? $this->canMoveCrateUpOrDown($vector, $toX, $toY)
            : $this->computeSimpleMove($vector, $toX, $toY);

        if ($canMove) {
            foreach ($this->cratesToMove as $cratePosition) {
                $this->moveCrate($vector, $cratePosition);
            }

            $this->move([$x, $y], [$toX, $toY]);
        }
    }

    private function move(array $from, array $to): void
    {
        [$x, $y] = $from;
        [$toX, $toY] = $to;

        $char = $this->map[$y][$x];
        $this->map[$toY][$toX] = $char;
        $this->map[$y][$x] = self::EMPTY_SPACE;

        if (self::ROBOT === $char) {
            $this->robot = [$toX, $toY];
        }
    }

    /**
     * Check both halves of the crate for obstruction.
     * - If you encounter a wall, the crate won't move
     * - If you encounter empty space, always check that
     *   the other half can move as well
     * - If you are trying to push another crate, recursively
     *   check it as well. Any obstruction down the chain
     *   stops the whole process.
     *
     * Push any movable crate to an array, to be moved when
     * every checks have been run
     */
    private function canMoveCrateUpOrDown(Vector2D $vector, int $x, int $y): bool
    {
        $canMove = false;

        $cratePosition = $this->getCratePosition($x, $y); // [[x, y], [x, y]]
        foreach ($cratePosition as [$elX, $elY]) {
            [$toX, $toY] = $this->getNextPosition($vector, $elX, $elY);

            $wouldMoveInto = $this->map[$toY][$toX];
            if (self::WALL === $wouldMoveInto) {
                return false;
            }

            if (self::EMPTY_SPACE === $wouldMoveInto) {
                $canMove = true;

                continue;
            }

            $canMove = $this->canMoveCrateUpOrDown($vector, $toX, $toY);
            if (!$canMove) {
                return false;
            }
        }

        if ($canMove && !\in_array($cratePosition, $this->cratesToMove, true)) {
            $this->cratesToMove[] = $cratePosition;
        }

        return $canMove;
    }

    private function moveCrate(Vector2D $vector, array $crateElements): void
    {
        foreach ($crateElements as [$elX, $elY]) {
            $this->move([$elX, $elY], $this->getNextPosition($vector, $elX, $elY));
        }
    }

    private function getCratePosition(int $x, int $y): array
    {
        if (']' === $this->map[$y][$x]) {
            return [[$x - 1, $y], [$x, $y]];
        }

        return [[$x, $y], [$x + 1, $y]];
    }

    private function getNextPosition(Vector2D $vector, int $x, int $y): array
    {
        return [
            $vector->x + $x,
            $vector->y + $y,
        ];
    }

    private function getResult(bool $partTwo = false): int
    {
        $result = 0;
        foreach ($this->map as $y => $row) {
            $row = implode('', $row);
            $object = $partTwo ? self::CRATE[0] : self::BOX;

            if (str_contains($row, $object)) {
                $keys = array_keys($this->map[$y], $object, true);

                foreach ($keys as $x) {
                    $result += $this->getGpsCoordinates($x, $y);
                }
            }
        }

        return $result;
    }

    private function getGpsCoordinates(int $x, int $y): int
    {
        return 100 * $y + $x;
    }
}
