<?php

namespace App\ConundrumSolver;

use App\Exception\SolverNotFoundException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

readonly class SolverHandler
{
    public function __construct(
        #[AutowireIterator('app.conundrum_solver')] private iterable $conundrumSolvers
    )
    {
    }

    /**
     * @throws SolverNotFoundException
     */
    public function getSolverForDate(string $year, string $day): ConundrumSolverInterface
    {
        /** @var ConundrumSolverInterface $solver */
        foreach ($this->conundrumSolvers as $solver) {
            if ($solver->supports($year, $day)) {
                return $solver;
            }
        }

        throw new SolverNotFoundException(
            \sprintf('There is no solver available for day %s of %s!', $day, $year)
        );
    }
}
