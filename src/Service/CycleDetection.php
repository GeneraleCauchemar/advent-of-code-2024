<?php

namespace App\Service;

use App\Entity\CycleDetection\Node;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @see https://en.wikipedia.org/wiki/Cycle_detection
 */
class CycleDetection
{
    #[ArrayShape(['lambda' => 'int', 'mu' => 'int'])]
    public function applyBrentsAlgorithm(Node $head): ?array
    {
        $lambda = $this->getLength($head);
        $tortoise = $hare = $head;

        // Finding the position µ of the first repetition of length λ
        // Hare moves forward λ times
        for ($i = 0; $i < $lambda; $i++) {
            $hare = $hare->next;
        }

        $mu = 0;

        // Tortoise and hare moving at same speed until they land
        // on nodes that are considered equivalent: tortoise has
        // arrived at µ
        while (null !== $hare && false === $tortoise?->isEqualTo($hare)) {
            $tortoise = $tortoise?->next;
            $hare = $hare->next;
            $mu++;
        }

        // No loop
        if (null === $hare) {
            throw new \LogicException();
        }

        return [$lambda, $mu];
    }

    private function getLength(Node $head): int
    {
        // Search successive powers of two
        $power = $lambda = 1;
        $tortoise = $head;   // x0
        $hare = $head->next; // f(x0)

        // Will run until finding a loop
        while (null !== $hare && false === $tortoise->isEqualTo($hare)) {
            // Smallest power of two indicates the start of a cycle
            if ($lambda === $power) {
                $tortoise = $hare;
                $power *= 2;
                $lambda = 0;
            }

            $hare = $hare->next;
            ++$lambda;
        }

        // No loop
        if (null === $hare) {
            throw new \LogicException();
        }

        return $lambda;
    }
}
