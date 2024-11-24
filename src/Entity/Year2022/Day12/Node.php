<?php

namespace App\Entity\Year2022\Day12;

class Node
{
    private int $x;
    private int $y;
    private int $z;
    private bool $visited = false;
    private ?Node $parent = null;

    private int $totalScore = 0;
    private int $guessedScore = 0;
    private int $score;

    public function __construct(int $y, int $x, int $z)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;

        $this->score = 1;
    }

    public function getCoordinates(): string
    {
        return \sprintf('%sx%sx%s', $this->getX(), $this->getY(), $this->getZ());
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function getZ(): int
    {
        return $this->z;
    }

    public function getTotalScore(): int
    {
        return $this->totalScore;
    }

    public function setTotalScore($totalScore): self
    {
        $this->totalScore = $totalScore;

        return $this;
    }

    public function visited(): bool
    {
        return $this->visited;
    }

    public function visit(): self
    {
        $this->visited = true;

        return $this;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getParent(): Node
    {
        return $this->parent;
    }

    public function setParent(Node $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getGuessedScore(): int
    {
        return $this->guessedScore;
    }

    public function setGuessedScore(int $guessedScore): self
    {
        $this->guessedScore = $guessedScore;

        return $this;
    }
}
