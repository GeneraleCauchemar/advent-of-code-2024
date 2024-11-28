<?php

declare(strict_types=1);

namespace App\ConundrumSolver;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.conundrum_solver')]
interface ConundrumSolverInterface
{
    public function supports(string $year, string $day): bool;

    public function execute(): array;

    public function partOne(): string|int;

    public function partTwo(): string|int;
}
