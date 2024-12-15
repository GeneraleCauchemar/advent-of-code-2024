<?php

namespace App\Enum;

use App\Entity\Vector2D;

enum Direction
{
    case North;
    case East;
    case South;
    case West;

    public function getVector(): Vector2D
    {
        return match ($this) {
            self::North => new Vector2D(0, -1),
            self::East => new Vector2D(1, 0),
            self::South => new Vector2D(0, 1),
            self::West => new Vector2D(-1, 0),
        };
    }

    public static function getDirectionFromVector(Vector2D $vector): ?Direction
    {
        return array_find(self::cases(), static fn($direction) => $direction->getVector() == $vector);
    }
}
