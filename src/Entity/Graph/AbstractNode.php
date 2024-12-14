<?php

namespace App\Entity\Graph;

abstract class AbstractNode implements NodeInterface
{
    public array $edges = [];

    public function __construct(public int $id)
    {
    }

    public function isEqualTo(NodeInterface $node): bool
    {
        return $this->id === $node->id;
    }

    abstract public function addEdgeTo(NodeInterface $to): void;
}
