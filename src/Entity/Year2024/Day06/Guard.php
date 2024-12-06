<?php

namespace App\Entity\Year2024\Day06;

use App\Entity\PathFinding\AbstractPosition;
use App\Entity\PathFinding\PositionInterface;

class Guard extends AbstractPosition
{
    public function __construct(public int $row, public int $column, public string $direction)
    {
        parent::__construct($row, $column);
    }

    public function moveTo(PositionInterface $position): void
    {
        $this->row = $position->row;
        $this->column = $position->column;
    }

    public function turnTo(string $direction): void
    {
        $this->direction = $direction;
    }
}
