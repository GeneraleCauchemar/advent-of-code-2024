<?php

namespace App\Entity\Year2022\Day12;

class ScoreHeap extends \SplHeap
{
    protected function compare($value1, $value2): int
    {
        if ($value1->getTotalScore() === $value2->getTotalScore()) {
            return 0;
        }

        return ($value1->getTotalScore() < $value2->getTotalScore()) ? 1 : -1;
    }
}
