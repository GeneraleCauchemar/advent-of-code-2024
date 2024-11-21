<?php

namespace App\Entity\Year2023\Day10;

use JMGQ\AStar\Node\NodeIdentifierInterface;

class Position implements NodeIdentifierInterface, \Stringable
{
    public const string START = 'S';
    private const string GROUND = '.';
    private const array SYMBOL_TO_ORIENTATION = [
        '|' => ['N', 'S'],
        '-' => ['W', 'E'],
        'L' => ['N', 'E'],
        'J' => ['N', 'W'],
        '7' => ['W', 'S'],
        'F' => ['S', 'E'],
    ];
    public bool $isGround;
    public bool $isStartingPoint;

    public function __construct(public int $row, public int $column, public string $symbol)
    {
        $this->isGround = self::GROUND === $this->symbol;
        $this->isStartingPoint = self::START === $this->symbol;
    }

    #[\Override]
    public function getUniqueNodeId(): string
    {
        return ((string) $this->row) . 'x' . ((string) $this->column);
    }

    public function isEqualTo(Position $position): bool
    {
        return $this->row === $position->row && $this->column === $position->column;
    }

    public function isAdjacentTo(Position $position): bool
    {
        $rowDiff = $this->getRowDiff($position, true);
        $columnDiff = $this->getColumnDiff($position, true);

        return (1 === $rowDiff && 0 === $columnDiff) || (1 === $columnDiff && 0 === $rowDiff);
    }

    public function isOrientedTowards(Position $position): bool
    {
        return !$this->isGround && \in_array(
                $this->mustFace($position),
                self::SYMBOL_TO_ORIENTATION[$this->symbol],
                true
            );
    }

    private function mustFace(Position $position): ?string
    {
        $rowDiff = $this->getRowDiff($position);
        $columnDiff = $this->getColumnDiff($position);

        return match (true) {
            -1 === $rowDiff => 'S',
            1 === $rowDiff => 'N',
            -1 === $columnDiff => 'E',
            1 === $columnDiff => 'W',
            default => ''
        };
    }

    private function getRowDiff(Position $position, bool $absolute = false): int
    {
        return $absolute ? abs($this->row - $position->row) : $this->row - $position->row;
    }

    private function getColumnDiff(Position $position, bool $absolute = false): int
    {
        return $absolute ? abs($this->column - $position->column) : $this->column - $position->column;
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->getUniqueNodeId();
    }
}
