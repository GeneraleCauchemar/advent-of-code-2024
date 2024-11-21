<?php

declare(strict_types=1);

namespace App\ConundrumSolver;

interface ConundrumSolverInterface
{
    public function execute(): array;

    public function partOne(): string|int;

    public function partTwo(): string|int;
}
