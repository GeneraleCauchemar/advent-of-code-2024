<?php

namespace App\Entity\Year2024\Day07;

class Equation
{
    public function __construct(
        public readonly int $result,
        public readonly array $operands
    )
    {
    }
}
