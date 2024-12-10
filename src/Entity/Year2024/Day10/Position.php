<?php

namespace App\Entity\Year2024\Day10;

use App\Entity\PathFinding\AbstractPosition;

class Position extends AbstractPosition
{
    public const int START = 0;
    public const int END = 9;

    public function __construct(public int $row, public int $column, public int $height)
    {
        parent::__construct($row, $column);
    }
}
