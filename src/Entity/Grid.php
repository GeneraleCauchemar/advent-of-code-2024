<?php

namespace App\Entity;

class Grid
{
    public function __construct(
        public int $xMax,
        public int $yMax,
        public int $xMin = 0,
        public int $yMin = 0,
    ) {
    }

    public function isInside(int $x, int $y): bool
    {
        return $this->xMin <= $x && $this->xMax >= $x
            && $this->yMin <= $y && $this->yMax >= $y;
    }
}
