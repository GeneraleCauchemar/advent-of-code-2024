<?php

namespace App\Service;

use Webmozart\Assert\Assert;

class CompassHelper
{
    public const string NORTH = 'N';
    public const string SOUTH = 'S';
    public const string EAST = 'E';
    public const string WEST = 'W';
    public const string NORTH_EAST = 'NE';
    public const string SOUTH_EAST = 'SE';
    public const string NORTH_WEST = 'NW';
    public const string SOUTH_WEST = 'SW';
    public const array DIRECTIONS = [
        self::NORTH,
        self::SOUTH,
        self::EAST,
        self::WEST,
        self::NORTH_EAST,
        self::SOUTH_EAST,
        self::NORTH_WEST,
        self::SOUTH_WEST,
    ];

    public static function isOnADiagonalAxis(string $direction): bool
    {
        Assert::oneOf($direction, self::DIRECTIONS);

        return \in_array($direction, [self::NORTH_EAST, self::SOUTH_EAST, self::NORTH_WEST, self::SOUTH_WEST], true);
    }

    public static function getDirectionFromDiff(int $xDiff, int $yDiff): ?string
    {
        return match (true) {
            (0 === $xDiff && -1 === $yDiff) => self::NORTH,
            (0 === $xDiff && 1 === $yDiff) => self::SOUTH,
            (1 === $xDiff && 0 === $yDiff) => self::EAST,
            (-1 === $xDiff && 0 === $yDiff) => self::WEST,
            (-1 === $xDiff && -1 === $yDiff) => self::NORTH_WEST,
            (-1 === $xDiff && 1 === $yDiff) => self::SOUTH_WEST,
            (1 === $xDiff && 1 === $yDiff) => self::SOUTH_EAST,
            (1 === $xDiff && -1 === $yDiff) => self::NORTH_EAST,
            default => null,
        };
    }

    public static function getDiffFromDirection(string $direction, int $x, int $y): array
    {
        Assert::oneOf($direction, self::DIRECTIONS);

        return match ($direction) {
            self::NORTH => [$x, $y - 1],
            self::SOUTH => [$x, $y + 1],
            self::EAST => [$x + 1, $y],
            self::WEST => [$x - 1, $y],
            self::NORTH_WEST => [$x - 1, $y - 1],
            self::SOUTH_WEST => [$x - 1, $y + 1],
            self::SOUTH_EAST => [$x + 1, $y + 1],
            self::NORTH_EAST => [$x + 1, $y - 1],
        };
    }

    public static function getOppositeDirection(string $direction): string
    {
        Assert::oneOf($direction, self::DIRECTIONS);

        return match ($direction) {
            self::NORTH => self::SOUTH,
            self::SOUTH => self::NORTH,
            self::EAST => self::WEST,
            self::WEST => self::EAST,
            self::NORTH_WEST => self::SOUTH_EAST,
            self::SOUTH_WEST => self::NORTH_EAST,
            self::SOUTH_EAST => self::NORTH_WEST,
            self::NORTH_EAST => self::SOUTH_WEST,
        };
    }
}
