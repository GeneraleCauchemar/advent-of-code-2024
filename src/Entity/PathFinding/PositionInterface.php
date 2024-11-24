<?php

namespace App\Entity\PathFinding;

interface PositionInterface
{
    public function isEqualTo(PositionInterface $position): bool;

    public function isAdjacentTo(PositionInterface $position): bool;
}
