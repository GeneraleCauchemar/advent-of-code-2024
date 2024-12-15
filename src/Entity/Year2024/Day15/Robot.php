<?php

namespace App\Entity\Year2024\Day15;

use App\Entity\Vector2D;

class Robot
{
    public function __construct(
        public int $x,
        public int $y,
        public array $moves
    )
    {
    }
}
