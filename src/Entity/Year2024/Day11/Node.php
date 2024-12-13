<?php

namespace App\Entity\Year2024\Day11;

use App\Entity\Graph\AbstractNode;
use App\Entity\Graph\NodeInterface;

class Node extends AbstractNode
{
    public function addEdgeTo(NodeInterface $to): void
    {
        if ($this->isEqualTo($to)) {
            throw new \LogicException();
        }

        if (!\array_key_exists($to->id, $this->edges)) {
            $this->edges[$to->id] = new Edge($to);

            return;
        }

        $this->edges[$to->id]->incrementWeight($to);
    }
}
