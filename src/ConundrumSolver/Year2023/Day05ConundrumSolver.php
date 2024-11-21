<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2023;

use App\ConundrumSolver\AbstractConundrumSolver;
use App\Entity\Year2023\Day05\Map;
use App\Entity\Year2023\Day05\Range;

// /// Day 5: If You Give A Seed A Fertilizer ///
class Day05ConundrumSolver extends AbstractConundrumSolver
{
    private const string SEED = 'seed';
    private const string LOCATION = 'location';

    private array $maps = [];
    private array $availableSeeds = [];
    private ?int $minLocation = null;

    public function __construct(string $year, string $day)
    {
        parent::__construct($year, $day, PHP_EOL . PHP_EOL);
    }

    #[\Override]
    public function warmup(): void
    {
        $input = $this->getInput();

        preg_match_all('/\d+/', (string) array_shift($input), $matches, PREG_PATTERN_ORDER);

        $this->availableSeeds = $matches[0];
        $this->maps = $input;
    }

    ////////////////
    // PART 1
    ////////////////

    #[\Override]
    public function partOne(): string|int
    {
        foreach ($this->availableSeeds as $seed) {
            $source = $seed;

            foreach ($this->maps as $map) {
                $map = explode(PHP_EOL, (string) $map);
                array_shift($map);

                foreach (array_filter($map) as $line) {
                    [$destinationRangeStart, $sourceRangeStart, $length] = array_map(static fn($value): int => (int) $value,
                        explode(' ', $line));
                    $sourceRangeEnd = $sourceRangeStart + $length - 1;

                    if ($sourceRangeStart > $source || $sourceRangeEnd < $source) {
                        $destination = $source;

                        continue;
                    }

                    $destination = $source === $sourceRangeStart ?
                        $destinationRangeStart :
                        $destinationRangeStart + ($source - $sourceRangeStart);

                    break;
                }

                $source = $destination ?? $source;
            }

            $this->updateMinLocation($source);
        }

        return $this->minLocation;
    }

    ////////////////
    // PART 2
    ////////////////

    #[\Override]
    public function partTwo(): string|int
    {
        $start = $this->availableSeeds[0];
        $rangesOfSeeds = $maps = [];

        // Initialize seed ranges
        foreach ($this->availableSeeds as $key => $seed) {
            // Odd
            if (1 === ($key + 1) % 2) {
                $start = $seed;

                continue;
            }

            $length = $seed;
            $rangesOfSeeds[] = new Range((int) $start, ($start + $length - 1));
        }

        // Initialize maps
        foreach ($this->maps as $map) {
            $map = explode(PHP_EOL, (string) $map);
            $object = (new Map())->setSourceAndDestination(array_shift($map));

            foreach (array_filter($map) as $line) {
                $object->computeLine($line);
            }

            $maps[] = $object;
        }

        $locations = $this->computeLocationRanges($maps, $rangesOfSeeds);

        if (empty($locations)) {
            return self::UNDETERMINED;
        }

        return min(array_map(static fn(Range $range): int => $range->from, $locations));
    }

    ////////////////
    // METHODS
    ////////////////

    private function updateMinLocation(int $location): void
    {
        if (null === $this->minLocation || $this->minLocation > $location) {
            $this->minLocation = $location;
        }
    }

    private function computeLocationRanges(array $maps, array $rangesOfSeeds): array
    {
        // For every map, converts every range from source
        // into one or more destination range
        /** @var Map $map */
        foreach ($maps as $map) {
            $rangesByMaps[$map->destination] = [];
            $ranges = self::SEED === $map->source ? $rangesOfSeeds : $rangesByMaps[$map->source];

            foreach ($ranges as $rangeList) {
                $rangesByMaps[$map->destination] = array_merge(
                    $rangesByMaps[$map->destination],
                    $map->convert($rangeList)
                );
            }
        }

        return $rangesByMaps[self::LOCATION] ?? [];
    }
}
