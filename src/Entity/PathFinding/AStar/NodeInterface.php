<?php

declare(strict_types=1);

namespace App\Entity\PathFinding\AStar;

use App\Enum\Direction;

interface NodeInterface extends \Stringable
{
    public function getX(): int;

    public function setX(int $x): self;

    public function getY(): int;

    public function setY(int $y): self;

    public function getDirection(): ?Direction;

    public function setDirection(?Direction $direction): self;

    public function getG(): float|int;

    public function setG(float|int $g): self;

    public function getH(): float|int;

    public function setH(float|int $h): self;

    public function getParent(): ?NodeInterface;

    public function setParent(?NodeInterface $parent): self;

    public function getPath(): array;

    public function setPath(array $path): self;

    public function isEqualTo(NodeInterface $node): bool;
}
