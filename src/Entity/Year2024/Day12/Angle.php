<?php

namespace App\Entity\Year2024\Day12;

class Angle implements \Stringable
{
    public string $id;

    public function __construct(
        public int $x,
        public int $y,
    ) {
        $this->id = \sprintf('%ux%u', $x, $y);
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
