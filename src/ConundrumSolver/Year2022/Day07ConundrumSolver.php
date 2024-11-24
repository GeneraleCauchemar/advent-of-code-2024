<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2022;

use App\ConundrumSolver\AbstractConundrumSolver;

// --- Day 7: No Space Left On Device ---
// PART ONE: 1325919, PART TWO: 2050735
class Day07ConundrumSolver extends AbstractConundrumSolver
{
    private const string CD = '$ cd ';
    private const string LIST = '$ ls';
    private const string UP = '$ cd ..';
    private const string ROOT = '/';

    private const int DISK_SPACE = 70000000;
    private const int UPDATE_WEIGHT = 30000000;

    private array $tree = [];
    private string $pointer = '';
    private array $weightByFolder = [];

    private int $totalWeight = 0;

    public function __construct(string $year, string $day)
    {
        parent::__construct($year, $day);
    }

    #[\Override]
    public function warmup(): void
    {
        $currentDir = [];
        $this->pointer = 'root';

        foreach ($this->getInput() as $instruction) {
            // Ignoring list instructions
            if (self::LIST === $instruction) {
                continue;
            }

            // Changing folders
            if (str_starts_with($instruction, self::CD)) {
                // Pushes current dir to tree before moving pointer
                $this->pushToTree($currentDir, $this->getKeysFromPointer());

                // Moves up in tree and goes to next instruction
                if (self::UP === $instruction) {
                    $this->movePointerUp();

                    continue;
                }

                // Else moves pointer down to new dir
                $this->movePointerDown($this->getDirName($instruction));

                continue;
            }

            // Managing content (files and dirs)
            [$option, $name] = explode(' ', $instruction);
            $currentDir[$name] = 'dir' === $option ? [] : $option;
        }

        // Pushes current dir to tree
        $this->pushToTree($currentDir, $this->getKeysFromPointer());

        // Compute folder weights recursively
        $this->computeFolderWeights($this->tree, $this->weightByFolder);

        $this->totalWeight = $this->weightByFolder['root'];
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): string|int
    {
        return array_sum(array_filter($this->weightByFolder, static fn($value): bool => 100000 >= $value));
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): string|int
    {
        $freespace = self::DISK_SPACE - $this->totalWeight;
        $minSpaceGain = self::UPDATE_WEIGHT - $freespace;

        return array_reduce($this->weightByFolder, static function ($carry, $item) use ($minSpaceGain) {
            if (null === $carry) {
                return $item;
            }

            // Return the closest solution that is higher than the desired space gain
            return abs($minSpaceGain - $carry) > abs($item - $minSpaceGain) && $item > $minSpaceGain
                ? $item
                : $carry;
        });
    }

    ////////////////
    // METHODS
    ////////////////

    private function getDirName(string $instruction): string
    {
        return str_ireplace(self::CD, '', $instruction);
    }

    private function movePointerUp(): void
    {
        $exploded = explode('/', $this->pointer);
        array_pop($exploded);

        $this->pointer = implode(self::ROOT, $exploded);
    }

    private function movePointerDown(string $dirname): void
    {
        if (!str_starts_with($dirname, self::ROOT)) {
            $dirname = self::ROOT . $dirname;
        }

        $this->pointer .= $dirname;
    }

    private function pushToTree(array &$content, array $keys): void
    {
        $contentDir = $content;
        $keys = array_reverse($keys);

        foreach ($keys as $k => $key) {
            $contentDir = [$key => (0 === $k ? $content : $contentDir)];
        }

        $this->tree = array_merge_recursive($this->tree, $contentDir);
        $content = [];
    }

    private function getKeysFromPointer(): array
    {
        return array_filter(explode('/', $this->pointer));
    }

    private function getIntVal(mixed $value): int
    {
        return \is_string($value) ? (int) $value : 0;
    }

    private function computeFolderWeights(array $folderContent, array &$weightByFolder, string $path = ''): void
    {
        $folderWeight = 0;

        foreach ($folderContent as $elementName => $value) {
            // Element is a folder, unfold it
            if (\is_array($value)) {
                $this->computeFolderWeights($value, $weightByFolder, $path . '/' . $elementName);
            } else {
                $folderWeight += $this->getIntVal($value);
            }
        }

        $pathPart = '';

        if ('' !== $path) {
            // For each parent folder, adds folder weight
            foreach (array_filter(explode('/', $path)) as $key) {
                $pathPart .= $key;

                $weightByFolder[$pathPart] = \array_key_exists($pathPart, $weightByFolder) ?
                    $weightByFolder[$pathPart] + $folderWeight :
                    $folderWeight;

                $pathPart .= '/';
            }
        }
    }
}
