<?php

namespace App\ConundrumSolver\Year2024;

use App\ConundrumSolver\AbstractConundrumSolver;

/**
 * ❄️ Day 9: Disk Fragmenter ❄️
 *
 * @see https://adventofcode.com/2024/day/9
 */
final class Day09ConundrumSolver extends AbstractConundrumSolver
{
    private const string EMPTY = '.';

    public function __construct()
    {
        parent::__construct('2024', '09');
    }

    // //////////////
    // PART 1
    // //////////////

    public function partOne(): string|int
    {
        $input = $this->getInput()[0];
        $fileBlocks = $line = [];
        $i = $j = 0;

        foreach (str_split($input) as $key => $item) {
            $even = (0 === $key % 2);
            $char = $even ? $i : '.';

            for ($k = 0; $k < $item; $k++) {
                if ($even) {
                    $fileBlocks[$j] = $char;
                }

                $line[] = $char;
                $j++;
            }

            if ($even) {
                $i++;
            }
        }

        $emptySpaces = array_keys($line, self::EMPTY);
        foreach ($emptySpaces as $key) {
            $blockKey = array_key_last($fileBlocks);
            // Don't move files to the right
            if ($key > $blockKey) {
                break;
            }

            $block = array_pop($fileBlocks);
            $line[$key] = $block;
            $line[$blockKey] = self::EMPTY;
        }

        $result = [];
        foreach ($line as $key => $char) {
            if (self::EMPTY === $char) {
                break;
            }

            $result[] = $key * $char;
        }

        return array_sum($result);
    }

    // //////////////
    // PART 2
    // //////////////

    public function partTwo(): string|int
    {
        $input = $this->getInput()[0];
        $files = [];
        $i = $j = 0;

        foreach (str_split($input) as $key => $item) {
            $even = (0 === $key % 2);
            $char = $even ? $i : self::EMPTY;
            $value = $this->fill((int) $item, $char);
            $files[$j] = $value;

            // Only increment on file change
            if ($even) {
                $i++;
            }

            $j += \count($value);
        }

        $line = $files;
        $emptySpaces = array_filter($files, static fn($value) => self::EMPTY === $value[0]);
        $files = array_filter($files, static fn($value) => self::EMPTY !== $value[0]);
        $files = $this->reverse($files);

        foreach ($files as $index => $file) {
            if (0 === $index) {
                break;
            }

            $fileLength = \count($file);

            // Try to find a big enough space to the left
            foreach ($emptySpaces as $sKey => $space) {
                $length = \count($space);
                // We don't want to move a file to a space that is too small nor to the right
                if ($fileLength > $length || $sKey > $index) {
                    continue;
                }

                // Move file to its new position
                $line[$sKey] = $file;
                $remainder = $length - $fileLength;

                unset($line[$index], $emptySpaces[$sKey]);

                // If the whole of the empty space has not been filled, add it back to the pool
                if (0 < $fileLength && 0 < $remainder) {
                    $emptySpaces[$sKey + $fileLength] = $this->fill($remainder, '.');
                }

                break;
            }

            ksort($line);
            ksort($emptySpaces);
        }

        $result = [];
        foreach ($line as $key => $blocks) {
            foreach ($blocks as $bKey => $value) {
                if ('.' === $value) {
                    continue 2;
                }

                $result[] = ($key + $bKey) * $value;
            }
        }

        return array_sum($result);
    }

    // //////////////
    // METHODS
    // //////////////

    private function reverse(array $array): array
    {
        $keys = array_keys($array);
        $values = array_values($array);

        return array_combine(array_reverse($keys), array_reverse($values));
    }

    private function fill(int $length, mixed $char): array
    {
        return array_fill(0, $length, $char);
    }
}
