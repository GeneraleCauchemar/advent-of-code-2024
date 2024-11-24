<?php

namespace App\Entity\Year2023\Day11;

use App\Entity\PathFinding\AbstractPosition;

class Position extends AbstractPosition
{
    public function __construct(public int $row, public int $column, public int $id)
    {
        parent::__construct($row, $column);
    }
}
