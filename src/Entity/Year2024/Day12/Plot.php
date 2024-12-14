<?php

namespace App\Entity\Year2024\Day12;

use App\Entity\LinearAlgebra\Vector2D;

class Plot implements \Stringable
{
    public string $id;

    public function __construct(
        public Angle $ne,
        public Angle $se,
        public Angle $sw,
        public Angle $nw,
        public string $cropType,
        public ?Region $region = null
    ) {
        $this->id = \sprintf('%ux%u', $this->nw->x, $this->nw->y);
    }

    public function getVector(): Vector2D
    {
        return new Vector2D($this->nw->x, $this->nw->y);
    }

    public function getEdges(): array
    {
        return [
            [$this->ne, $this->se],
            [$this->se, $this->sw],
            [$this->sw, $this->nw],
            [$this->nw, $this->ne],
        ];
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
