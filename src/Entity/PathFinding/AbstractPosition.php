<?php

namespace App\Entity\PathFinding;

use JMGQ\AStar\Node\NodeIdentifierInterface;

abstract class AbstractPosition implements PositionInterface, NodeIdentifierInterface, \Stringable
{
    public function __construct(public int $row, public int $column)
    {
    }

    #[\Override]
    public function getUniqueNodeId(): string
    {
        return ((string) $this->row) . 'x' . ((string) $this->column);
    }

    public function isEqualTo(PositionInterface $position): bool
    {
        return $this->row === $position->row && $this->column === $position->column;
    }

    public function isAdjacentTo(PositionInterface $position): bool
    {
        $rowDiff = $this->getRowDiff($position, true);
        $columnDiff = $this->getColumnDiff($position, true);

        return (1 === $rowDiff && 0 === $columnDiff) || (1 === $columnDiff && 0 === $rowDiff);
    }

    protected function getRowDiff(PositionInterface $position, bool $absolute = false): int
    {
        return $absolute ? abs($this->row - $position->row) : $this->row - $position->row;
    }

    protected function getColumnDiff(PositionInterface $position, bool $absolute = false): int
    {
        return $absolute ? abs($this->column - $position->column) : $this->column - $position->column;
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->getUniqueNodeId();
    }
}
