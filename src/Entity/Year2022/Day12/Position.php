<?php

namespace App\Entity\Year2022\Day12;

use App\Entity\PathFinding\AbstractPosition;

class Position extends AbstractPosition
{
    public const string START = 'S';
    public const string END = 'E';

    public int $elevation;
    public bool $isEndingPoint;
    private bool $isStartingPoint;

    public function __construct(public int $row, public int $column, public string $letter)
    {
        parent::__construct($row, $column);

        $this->isStartingPoint = self::START === $this->letter;
        $this->isEndingPoint = self::END === $this->letter;
        $this->elevation = $this->getNumericValueForLetter($letter);
    }

    private function getNumericValueForLetter(string $letter): int
    {
        if ($this->isStartingPoint) {
            $letter = 'a';
        } elseif ($this->isEndingPoint) {
            $letter = 'z';
        }

        return \ord($letter) - 96;
    }
}
