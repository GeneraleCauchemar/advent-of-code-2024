<?php

declare(strict_types=1);

namespace App\Controller;

use App\ConundrumSolver\SolverHandler;
use App\Exception\SolverNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    '/{year}/{day}',
    name: 'app_resolve',
    requirements: [
        'year' => '\d{4}',
        'day'  => '\d{1,2}',
    ])]
class ResolveAction extends AbstractController
{
    public function __construct(private SolverHandler $solverHandler)
    {
    }

    public function __invoke(int $year, int $day, Request $request)
    {
        $testMode = $request->query->getBoolean('test');

        try {
            $solver = $this->solverHandler->getSolverForDate(
                (string) $year,
                str_pad((string) $day, 2, '0', STR_PAD_LEFT)
            );
        } catch (SolverNotFoundException) {
            // TODO : afficher une erreur
            throw new NotFoundHttpException();
        }

        try {
            $result = $solver->execute($testMode);
        } catch (\Exception $e) {
            // TODO : afficher une erreur
            throw new NotFoundHttpException();
        }
    }
}
