<?php

declare(strict_types=1);

namespace App\Entity\PathFinding\AStar;

class AStar extends AbstractAStar
{
    public function run(NodeInterface $start, NodeInterface $goal): ?iterable
    {
        $this->reset();

        $start->setG(0)
              ->setH($this->domainLogic->getHeuristic($start, $goal))
        ;

        $this->openList[(string) $start] = $start;

        while (!empty($this->openList)) {
            $currentNode = $this->getBest();

            if ($currentNode->isEqualTo($goal)) {
                return $this->getPathFromStartTo($currentNode);
            }

            $this->closedList[(string) $currentNode] = $currentNode;

            $this->evaluateSuccessors(
                $this->getAdjacentNodesWithTotalScore($currentNode, $goal),
                $currentNode
            );
        }

        // Aucun chemin trouvé
        return null;
    }

    private function reset(): void
    {
        $this->openList = [];
        $this->closedList = [];
    }
}
