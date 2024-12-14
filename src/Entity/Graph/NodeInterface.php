<?php

namespace App\Entity\Graph;

interface NodeInterface
{
    public function addEdgeTo(NodeInterface $to): void;
}
