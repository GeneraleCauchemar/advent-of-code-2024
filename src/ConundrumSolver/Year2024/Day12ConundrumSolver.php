<?php

namespace App\ConundrumSolver\Year2024;

use App\ConundrumSolver\AbstractConundrumSolver;
use App\Entity\Vector2D;
use App\Entity\Year2024\Day12\Angle;
use App\Entity\Year2024\Day12\Plot;
use App\Entity\Year2024\Day12\Region;
use App\Enum\Direction;
use App\Exception\OutsideOfGridException;

/**
 * ❄️ Day 12: Garden Groups ❄️
 *
 * @see https://adventofcode.com/2024/day/12
 */
final class Day12ConundrumSolver extends AbstractConundrumSolver
{
    private array $angles;
    private array $garden;
    private array $regions;
    private array $areaAndPerimeters;
    private int $xMax;
    private int $yMax;

    public function __construct()
    {
        parent::__construct('2024', '12');
    }

    #[\Override]
    public function warmup(): void
    {
        $this->resetMaps();
    }

    // //////////////
    // PART 1
    // //////////////

    /**
     * @see https://math.stackexchange.com/a/4281635
     */
    public function partOne(): string|int
    {
        foreach ($this->regions as $regions) {
            foreach ($regions as $region) {
                $this->computeAreaAndPerimeter($region);
            }
        }

        $results = array_map(static function ($areaAndPerimeter) {
            return array_product($areaAndPerimeter);
        }, $this->areaAndPerimeters);

        return array_sum($results);
    }

    // //////////////
    // PART 2
    // //////////////

    public function partTwo(): string|int
    {
        if ($this->testMode) {
            $this->resetMaps(true);
        }

        foreach ($this->regions as $regions) {
            foreach ($regions as $region) {
                $this->computeAreaAndPerimeter($region);
            }
        }

        foreach ($this->regions as $regions) {
            /** @var Region $region */
            foreach ($regions as $region) {
                $pathLength = 0;

                while (!empty($region->edges)) {
                    $nwEdge = $region->getMostNorthWesternEdge();

                    $this->mapPathLength($nwEdge[0], $region, $nwEdge, $pathLength);
                }

                $this->areaAndPerimeters[(string) $region][1] = $pathLength;
            }
        }

        $results = array_map(static function ($areaAndPerimeter) {
            return array_product($areaAndPerimeter);
        }, $this->areaAndPerimeters);

        return array_sum($results);
    }

    // //////////////
    // METHODS
    // //////////////

    private function resetMaps(bool $partTwo = false): void
    {
        $this->garden = [];
        $this->angles = [];
        $this->regions = [];
        $this->areaAndPerimeters = [];

        $input = $this->getInput($partTwo ? self::PART_TWO : self::PART_ONE);
        $gardenWidth = \strlen($input[0]);

        for ($y = 0, $yMax = \count($input); $y <= $yMax; $y++) {
            $this->angles[] = $this->getAngles($y, $gardenWidth);
        }

        $this->mapGarden($input);

        foreach ($this->garden as $y => $plots) {
            foreach ($plots as $x => $plot) {
                /** @var Plot $plot */
                if (null === $plot->region) {
                    $this->mapRegion($x, $y, $plot);
                }
            }
        }
    }

    private function mapGarden(array $input): void
    {
        $this->xMax = \strlen($input[0]) - 1;
        $this->yMax = \count($input) - 1;

        foreach ($input as $y => $line) {
            $line = str_split($line);

            foreach ($line as $x => $letter) {
                $this->createPlot($x, $y, $letter);
            }
        }
    }

    private function getAngles(int $y, int $gardenWidth): array
    {
        $angles = [];

        for ($x = 0; $x <= $gardenWidth; $x++) {
            $angles[] = new Angle($x, $y);
        }

        return $angles;
    }

    private function createPlot(int $x, int $y, string $letter): void
    {
        $plot = new Plot(
            $this->getAngle($x + 1, $y),
            $this->getAngle($x + 1, $y + 1),
            $this->getAngle($x, $y + 1),
            $this->getAngle($x, $y),
            $letter
        );

        $this->garden[$y][$x] = $plot;
    }

    private function mapRegion(int $x, int $y, Plot $plot, ?Region $region = null): void
    {
        if (null === $region) {
            $region = $this->createRegion($x, $y, $plot);
        }

        $region->addPlot($plot);
        $plot->region = $region;
        $position = $plot->getVector();

        foreach (Direction::cases() as $direction) {
            try {
                $nextPosition = $this->getNextPosition($position, $direction);
            } catch (OutsideOfGridException) {
                continue;
            }

            $nextPlot = $this->garden[$nextPosition->y][$nextPosition->x];
            if ($plot->cropType !== $nextPlot->cropType || null !== $nextPlot->region) {
                continue;
            }

            $this->mapRegion($x, $y, $nextPlot, $region);
        }
    }

    /**
     * @throws OutsideOfGridException
     */
    private function getNextPosition(Vector2D $position, Direction $direction): Vector2D
    {
        $nextPosition = $position->add($direction->getVector());

        if (
            0 > $nextPosition->x
            || $this->xMax < $nextPosition->x
            || 0 > $nextPosition->y
            || $this->yMax < $nextPosition->y
        ) {
            throw new OutsideOfGridException();
        }

        return $nextPosition;
    }

    private function createRegion(int $x, int $y, Plot $plot): Region
    {
        $region = new Region($x, $y, $plot->cropType);
        if (!\array_key_exists($plot->cropType, $this->regions)) {
            $this->regions[$plot->cropType] = [];
        }

        $this->regions[$plot->cropType][(string) $region] = $region;

        return $region;
    }

    private function getAngle(int $x, int $y): Angle
    {
        return $this->angles[$y][$x];
    }

    private function computeAreaAndPerimeter(Region $region): void
    {
        $area = \count($region->plots);
        $edges = [];

        /** @var Plot $plot */
        foreach ($region->plots as $plot) {
            foreach ($plot->getEdges() as $edge) {
                $reverse = array_search(array_reverse($edge), $edges); // NOT strict
                if (false !== $reverse) {
                    unset($edges[$reverse]);

                    continue;
                }

                $edges[] = $edge;
            }
        }

        $region->edges = array_values($edges);
        $this->areaAndPerimeters[(string) $region] = [$area, \count($region->edges)];
    }

    private function mapPathLength(Angle $start, Region $region, array $edge, int &$pathLength): void
    {
        $b = $edge[1];
        $region->removeEdge($edge);

        if ((string) $b === (string) $start) {
            $pathLength++;

            return;
        }

        $nextEdge = $region->getNextEdgeOnPerimeter($b);
        if ($this->changesDirection($edge, $nextEdge)) {
            $pathLength++;
        }

        $this->mapPathLength($start, $region, $nextEdge, $pathLength);
    }

    private function changesDirection(array $a, array $b): bool
    {
        $vectorA = new Vector2D($a[1]->x - $a[0]->x, $a[1]->y - $a[0]->y);
        $vectorB = new Vector2D($b[1]->x - $b[0]->x, $b[1]->y - $b[0]->y);

        return !$vectorA->isEqualTo($vectorB);
    }
}
