<?php

namespace App\Entity\Graph;

abstract class AbstractEdge implements EdgeInterface
{
    public int $weight = 1;

    public function __construct(public readonly NodeInterface $to)
    {
    }

    public function incrementWeight(): void
    {
        $this->weight++;
    }
}
