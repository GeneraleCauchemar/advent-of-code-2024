<?php

namespace App\Entity\Year2022\Day12;

class AStarAlgorithm
{
    private Grid $grid;

    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    public function search(Node $end, ?Node $start = null): array
    {
        $heap = new ScoreHeap();

        $heap->insert($end);

        $current = $this->fillHeap($heap, $end, $start);

        if ($current !== $start) {
            return [];
        }
        dd($this->getReversedPath($current));
        return $this->getReversedPath($current);
    }

    private function fillHeap(ScoreHeap $heap, Node $current, ?Node $start): Node
    {
        while ($heap->valid() && $start !== $current) {
            /*** @var Node $current */
            $current = $heap->extract();

            /** @var Node $adjacent */
            foreach ($this->grid->getAdjacent($current) as $adjacent) {
                $score = $current->getScore();
                $visited = $adjacent->visited();

                // if (!$visited || $score <= $adjacent->getScore()) {
                if (!$visited) {
                    $adjacent->visit();
                    $adjacent->setParent($current);
                    // $adjacent->setGuessedScore($this->manhattanComparison($adjacent, $start));
                    // $adjacent->setScore($score);
                    // $adjacent->setTotalScore($adjacent->getScore() + $adjacent->getGuessedScore());

                    if (!$visited) {
                        $heap->insert($adjacent);
                    }
                }
            }
        }

        return $current;
    }

    private function getReversedPath(Node $current): array
    {
        $result = [];

        while ($current->getParent()) {
            $result[] = $current;
            $current = $current->getParent();
        }

        $result[] = $current;

        return array_reverse($result);
    }

    private function manhattanComparison(Node $node, Node $goal): int
    {
        $deltaX = abs($node->getX() - $goal->getX());
        $deltaY = abs($node->getY() - $goal->getY());

        return $deltaX + $deltaY;
    }
}
