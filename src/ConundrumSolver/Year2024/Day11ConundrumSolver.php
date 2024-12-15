<?php

namespace App\ConundrumSolver\Year2024;

use App\ConundrumSolver\AbstractConundrumSolver;
use App\Entity\Graph\NodeInterface;
use App\Entity\Graph\OrientedGraph;
use App\Entity\Year2024\Day11\Node;
use App\Exception\NodeAlreadyInGraphException;

/**
 * ❄️ Day 11: Plutonian Pebbles ❄️
 *
 * @see https://adventofcode.com/2024/day/11
 */
final class Day11ConundrumSolver extends AbstractConundrumSolver
{
    private array $stones = [];
    private ?OrientedGraph $cache = null;

    public function __construct()
    {
        parent::__construct('2024', '11');
    }

    // //////////////
    // PART 1
    // //////////////

    public function partOne(): string|int
    {
        $this->resetStones();

        for ($i = 0; $i < 25; $i++) {
            $this->blink();
        }

        return \count($this->stones);
    }

    // //////////////
    // PART 2
    // //////////////

    public function partTwo(): string|int
    {
        $this->resetStones();
        $this->cache = new OrientedGraph();

        // Count the original stones
        $stones = array_fill_keys($this->stones, 0);
        foreach ($this->stones as $stone) {
            $stones[$stone]++;
        }

        for ($i = 0; $i < 75; $i++) {
            $nextTurn = [];

            /**
             * For every stone of this turn, get
             * next node
             * For every edge coming from the node,
             * update the counter for the following
             * node in the list of stones for next
             * turn
             */
            foreach ($stones as $stone => $count) {
                ini_set('xdebug.max_nesting_level', 10000);
                $node = $this->getFromCache($stone);
                ini_restore('xdebug.max_nesting_level');
                $edges = $node->edges;

                foreach ($edges as $edge) {
                    $toId = $edge->to->id;

                    if (null === ($nextTurn[$toId] ?? null)) {
                        $nextTurn[$toId] = 0;
                    }

                    // some nodes split into equal nodes, so weigh twice more
                    $nextTurn[$toId] += $count * $edge->weight;
                }
            }

            $stones = $nextTurn;
        }

        return \array_sum($stones);
    }

    // //////////////
    // METHODS
    // //////////////

    private function resetStones(): void
    {
        $this->stones = array_map(static fn($value) => (int) $value, explode(' ', $this->getInput()[0]));
    }

    private function blink(): void
    {
        $heap = [];
        foreach ($this->stones as $stone) {
            array_push($heap, ...$this->getNext($stone));
        }

        $this->stones = $heap;
    }

    private function getNext(int $stone): array
    {
        $stones = match (true) {
            0 === $stone => 1,
            0 === \strlen((string) $stone) % 2 => $this->split((string) $stone),
            default => $stone * 2024
        };

        if (!\is_array($stones)) {
            $stones = [$stones];
        }

        return $stones;
    }

    private function split(string $stone): array
    {
        return array_map('\intval', str_split($stone, \strlen($stone) / 2));
    }

    private function getFromCache(int $id): NodeInterface
    {
        $node = $this->cache->getNode($id);
        if ($node instanceof NodeInterface) {
            return $node;
        }

        $node = new Node($id);

        try {
            $this->cache->addNode($node);
        } catch (NodeAlreadyInGraphException) {
            // noop (should never happen)
        }

        /**
         * For every next node, follow
         * the graph recursively until
         * you find a node that already
         * exists in cache, adding those
         * we compute along the way to
         * the cache
         */
        $next = $this->getNext($id);
        foreach ($next as $stone) {
            $next = $this->getFromCache($stone);
            $this->cache->addEdge($node, $next);
        }

        return $node;
    }
}
