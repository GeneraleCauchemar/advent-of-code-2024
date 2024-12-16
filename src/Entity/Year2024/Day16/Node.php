<?php

declare(strict_types=1);

namespace App\Entity\Year2024\Day16;

use App\Entity\PathFinding\AStar\AbstractNode;
use App\Enum\Direction;

class Node extends AbstractNode
{
    public const string START = 'S';
    public const string END = 'E';
    public const string WALL = '#';

    public function __construct(
        int $x,
        int $y,
        ?Direction $direction,
        public string $symbol,
    ) {
        parent::__construct($x, $y, $direction);
    }

    public function isStart(): bool
    {
        return self::START === $this->symbol;
    }

    public function isEnd(): bool
    {
        return self::END === $this->symbol;
    }

    public function isWall(): bool
    {
        return self::WALL === $this->symbol;
    }
}
