<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2022;

use App\ConundrumSolver\AbstractConundrumSolver;

// --- Day 5: Supply Stacks ---
// PART ONE: PSNRGBTFT, PART TWO: BNTZFPMMW
class Day05ConundrumSolver extends AbstractConundrumSolver
{
    private mixed $cratePiles;
    private mixed $moves;

    public function __construct(string $year, string $day)
    {
        parent::__construct($year, $day, PHP_EOL . PHP_EOL);
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): string|int
    {
        // Init values
        [$this->cratePiles, $this->moves] = $this->getInput();

        $this->computeCratePiles();
        $this->computeMoves();

        // Let's keep them global values nice and tidy, shall we...
        $localPiles = $this->cratePiles;

        foreach ($this->moves as [$move, $from, $to]) {
            // Move to pile 'A' the last crate from pile 'B', n times
            for ($i = 0; $i < $move; $i++) {
                $localPiles[$to][] = array_pop($localPiles[$from]);
            }
        }

        return $this->writeOutput($localPiles);
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): string|int
    {
        $localPiles = $this->cratePiles;

        foreach ($this->moves as [$move, $from, $to]) {
            // Push to pile 'A' the n last crates from pile 'B'
            array_push($localPiles[$to], ...array_splice($localPiles[$from], -$move));
        }

        return $this->writeOutput($localPiles);
    }

    ////////////////
    // METHODS
    ////////////////

    private function computeCratePiles(): void
    {
        $this->cratePiles = explode(PHP_EOL, (string) $this->cratePiles);
        $this->cratePiles = array_reverse($this->cratePiles);

        // Extracts the pile keys from the input
        $pileKeys = array_filter(preg_split('/\s+/', array_shift($this->cratePiles)));
        $keysFromOffset = $this->getKeysFromOffset($pileKeys);
        $localPiles = array_fill_keys($pileKeys, []);

        // Moves each crate to the proper pile in the array
        array_walk($this->cratePiles, static function ($value) use ($keysFromOffset, &$localPiles): void {
            // Uses a REGEX to find every crate name and its offset
            preg_match_all('/\[([^]]*)]/', $value, $crates, PREG_OFFSET_CAPTURE);

            // Determines the key from the offset and puts all crates in the right pile
            foreach ($crates[1] as [$crate, $offset]) {
                $localPiles[$keysFromOffset[$offset]][] = $crate;
            }
        });

        $this->cratePiles = $localPiles;
    }

    private function getKeysFromOffset(array $pileKeys): array
    {
        $start = 1;
        $step = 4;

        return array_combine(
            range($start, ((\count($pileKeys) - 1) * $step) + $start, $step),
            $pileKeys
        );
    }

    private function computeMoves(): void
    {
        $this->moves = array_map(
            static fn($value): array => array_map('\intval', explode(' ', (string) $value)),
            array_filter(explode(PHP_EOL, str_ireplace(['move ', 'from ', 'to '], '', $this->moves)))
        );
    }

    private function writeOutput(array $cratePiles): string
    {
        array_walk($cratePiles, static function ($crates) use (&$output): void {
            $output .= end($crates);
        });

        return $output ?? '';
    }
}
