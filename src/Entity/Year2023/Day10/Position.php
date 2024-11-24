<?php

namespace App\Entity\Year2023\Day10;

use App\Entity\PathFinding\AbstractPosition;

class Position extends AbstractPosition
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
        parent::__construct($row, $column);

        $this->isGround = self::GROUND === $this->symbol;
        $this->isStartingPoint = self::START === $this->symbol;
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
}
