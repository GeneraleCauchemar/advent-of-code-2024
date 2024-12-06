<?php

namespace App\Entity\Year2024\Day06;

use App\Entity\PathFinding\AbstractPosition;

class Position extends AbstractPosition
{
    public function __construct(
        public int $row,
        public int $column,
        public bool $visited = false,
        public ?string $direction = null
    ) {
        parent::__construct($row, $column);
    }
}
