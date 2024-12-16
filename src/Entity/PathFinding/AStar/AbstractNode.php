<?php

declare(strict_types=1);

namespace App\Entity\PathFinding\AStar;

use App\Enum\Direction;

abstract class AbstractNode implements NodeInterface
{
    private int|float $g;
    private int|float $h;
    private ?NodeInterface $parent = null;
    private array $path = [];

    public function __construct(
        private int $x,
        private int $y,
        private ?Direction $direction = null,
    ) {
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function setX(int $x): self
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function setY(int $y): self
    {
        $this->y = $y;

        return $this;
    }

    public function getDirection(): ?Direction
    {
        return $this->direction;
    }

    public function setDirection(?Direction $direction): self
    {
        $this->direction = $direction;

        return $this;
    }

    public function getG(): float|int
    {
        return $this->g;
    }

    public function setG(float|int $g): self
    {
        $this->g = $g;

        return $this;
    }

    public function getH(): float|int
    {
        return $this->h;
    }

    public function setH(float|int $h): self
    {
        $this->h = $h;

        return $this;
    }

    public function getParent(): ?NodeInterface
    {
        return $this->parent;
    }

    public function setParent(?NodeInterface $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getPath(): array
    {
        return $this->path;
    }

    public function setPath(array $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function isEqualTo(NodeInterface $node): bool
    {
        return $node->getX() === $this->x && $node->getY() === $this->y;
    }

    public function __toString(): string
    {
        return $this->x . 'x' . $this->y;
    }
}
