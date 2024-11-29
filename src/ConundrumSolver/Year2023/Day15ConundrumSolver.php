<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2023;

use App\ConundrumSolver\AbstractConundrumSolver;

// /// Day 15: Lens library ///
class Day15ConundrumSolver extends AbstractConundrumSolver
{
    private array $steps = [];

    public function __construct()
    {
        parent::__construct('2023', '15');
    }

    public function warmup(): void
    {
        foreach ($this->getInput() as $initializationSequence) {
            foreach (explode(',', $initializationSequence) as $step) {
                preg_match(
                    '/(?<label>[a-z]+)(?<operation>[=-])(?<focal_length>\d?)/',
                    $step,
                    $matches,
                );

                $this->steps[] = [
                    'label'       => $matches['label'],
                    'hash'        => $this->hash($step),
                    'operation'   => $matches['operation'],
                    'focalLength' => $matches['focal_length'] ?? null,
                ];
            }
        }
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): string|int
    {
        return array_sum(array_column($this->steps, 'hash'));
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): string|int
    {
        $boxes = array_fill(0, 255, []);

        foreach ($this->steps as $step) {
            $label = $step['label'];
            $hash = $this->hash($label);
            $operation = $step['operation'];

            if (
                '-' === $operation
                && \array_key_exists($label, $boxes[$hash])
            ) {
                unset($boxes[$hash][$label]);
            } elseif ('=' === $operation) {
                $focalLength = (int) $step['focalLength'];

                if (!\array_key_exists($label, $boxes[$hash])) {
                    unset($boxes[$hash][$label]);
                }

                $boxes[$hash][$label] = $focalLength;
            }
        }

        $focusingPower = 0;
        foreach ($boxes as $key => $box) {
            $slots = array_keys($box);

            foreach ($box as $label => $focalLength) {
                $focusingPower += $this->computeFocusingLength(
                    $key,
                    array_search($label, $slots, true) + 1,
                    $focalLength
                );
            }
        }

        return $focusingPower;
    }

    ////////////////
    // METHODS
    ////////////////

    private function hash(string $value): int
    {
        $hashedValue = 0;

        // unpack('C*', $value): after testing, \ord() seems marginally faster
        foreach (str_split($value) as $char) {
            $this->applyHashToChar($hashedValue, $char);
        }

        return $hashedValue;
    }

    private function applyHashToChar(int &$hashed, string $char): void
    {
        $hashed += \ord($char);
        $hashed *= 17;
        $hashed %= 256;
    }

    private function computeFocusingLength(int $boxKey, int $slot, int $focalLength): int
    {
        return (1 + $boxKey) * $slot * $focalLength;
    }
}
