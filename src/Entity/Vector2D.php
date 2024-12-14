<?php

namespace App\Entity;

class Vector2D
{
    public function __construct(
        public int|float $x,
        public int|float $y,
    ) {
    }

    public function add(Vector2D $v): Vector2D
    {
        return new Vector2D($this->x + $v->x, $this->y + $v->y);
    }

    public function substract(Vector2D $v): Vector2D
    {
        return new Vector2D($this->x - $v->x, $this->y - $v->y);
    }

    public function multiply(Vector2D $v): Vector2D
    {
        return new Vector2D($this->x * $v->x, $this->y * $v->y);
    }

    public function scalarMultiply(int $a): Vector2D
    {
        return new Vector2D($this->x * $a, $this->y * $a);
    }

    public function scalarProduct(Vector2D $v): float|int
    {
        return $this->x * $v->x + $this->y * $v->y;
    }

    public function isEqualTo(Vector2D $v): bool
    {
        return $this->x === $v->x && $this->y === $v->y;
    }
}
