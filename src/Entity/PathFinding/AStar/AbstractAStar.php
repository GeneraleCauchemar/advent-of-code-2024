<?php

declare(strict_types=1);

namespace App\Entity\PathFinding\AStar;

use App\Entity\Vector2D;
use App\Enum\Direction;

abstract class AbstractAStar
{
    protected array $openList;
    protected array $closedList;

    public function __construct(protected DomainLogicInterface $domainLogic)
    {
        $this->openList = [];
        $this->closedList = [];
    }

    protected function getBest(): NodeInterface
    {
        usort($this->openList, static function (NodeInterface $a, NodeInterface $b) {
            return ($a->getG() + $a->getH()) - ($b->getG() + $b->getH());
        });

        return array_shift($this->openList);
    }

    protected function getAdjacentNodesWithTotalScore(NodeInterface $from, NodeInterface $to): array
    {
        $adjacentNodes = $this->domainLogic->getAdjacentNodes($from);

        /** @var NodeInterface $adjacentNode */
        foreach ($adjacentNodes as $adjacentNode) {
            if (\array_key_exists((string) $adjacentNode, $this->closedList)) {
                continue;
            }

            $adjacentNode->setG($from->getG() + $this->domainLogic->getRealCost($from, $adjacentNode));

            if (
                $this->domainLogic->getPenaliseDirectionChange()
                && null !== $from->getDirection()
            ) {
                $directionTo = $this->getDirectionBetween($from, $adjacentNode);
                $adjacentNode->setDirection($directionTo);

                if ($adjacentNode->getDirection() !== $from->getDirection()) {
                    $adjacentNode->setG($adjacentNode->getG() + $this->domainLogic->getPenalty());
                }
            }

            $adjacentNode->setH($this->domainLogic->getHeuristic($adjacentNode, $to));
        }

        return $adjacentNodes;
    }

    protected function evaluateSuccessors(iterable $successors, NodeInterface $parent): void
    {
        /** @var NodeInterface $successor */
        foreach ($successors as $successor) {
            if (
                $this->nodeIsInListWithBetterOrSameCost($successor, $this->openList)
                || $this->nodeIsInListWithBetterOrSameCost($successor, $this->closedList)
            ) {
                continue;
            }

            $successor->setParent($parent);

            if (\array_key_exists((string) $successor, $this->closedList)) {
                unset($this->closedList[(string) $successor]);
            }

            $this->openList[(string) $successor] = $successor;
        }
    }

    protected function getPathFromStartTo(NodeInterface $node): iterable
    {
        $path = [];

        $currentNode = $node;
        while (null !== $currentNode) {
            array_unshift($path, $currentNode);

            $currentNode = $currentNode->getParent();
        }

        return $path;
    }

    protected function nodeIsInListWithBetterOrSameCost(NodeInterface $node, array $list): bool
    {
        if (\array_key_exists((string) $node, $list)) {
            $nodeInList = $list[(string) $node];

            if ($node->getG() >= $nodeInList->getG()) {
                return true;
            }
        }

        return false;
    }

    protected function getDirectionBetween(NodeInterface $from, NodeInterface $to): Direction
    {
        $vector = new Vector2D($to->getX() - $from->getX(), $to->getY() - $from->getY());

        return Direction::getDirectionFromVector($vector);
    }
}
