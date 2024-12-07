<?php

namespace App\Entity\Year2024\Day07;

readonly class Equation
{
    public function __construct(
        public int $result,
        public array $operands
    )
    {
    }
}
