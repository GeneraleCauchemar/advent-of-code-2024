<?php

namespace App\Entity\Year2024\Day04;

use App\Entity\PathFinding\AbstractPosition;

class Position extends AbstractPosition
{
    public const string X = 'X';
    public const string M = 'M';
    public const string A = 'A';
    public const string S = 'S';

    public function __construct(public int $row, public int $column, public string $letter)
    {
        parent::__construct($row, $column);
    }

    public function getNextLetter(): ?string
    {
        return match ($this->letter) {
            self::X => self::M,
            self::M => self::A,
            self::A => self::S,
            default => null,
        };
    }
}
