<?php

namespace App\Entity\CycleDetection;

class Node
{
    public function __construct(
        public mixed $data,
        public ?Node $next = null,
        public mixed $previousData = null
    ) {
    }

    public function isEqualTo(?Node $node): bool
    {
        return $node instanceof self
            && $node->data === $this->data
            && $node->next?->data === $this->next->data
            && $node->previousData === $this->previousData;
    }
}
