<?php

namespace App\Entity\Year2024\Day12;

class Region implements \Stringable
{
    public string $id;
    public array $plots = [];
    public array $edges = [];

    public function __construct(int $x, int $y, public string $cropType)
    {
        $this->id = \sprintf('%ux%ux%s', $x, $y, $cropType);
    }

    public function addPlot(Plot $plot): self
    {
        $this->plots[(string) $plot] = $plot;

        return $this;
    }

    public function getMostNorthWesternEdge(): array
    {
        usort($this->edges, static function (array $a, array $b) {
            return $b[0]->x <= $a[0]->x && $b[0]->y <= $a[0]->y;
        });

        return reset($this->edges);
    }

    public function getNextEdgeOnPerimeter(Angle $angle): array
    {
        return array_find($this->edges, static function (array $edge) use ($angle) {
            return (string) $edge[0] === (string) $angle;
        });
    }

    public function removeEdge(array $edge): void
    {
        $key = array_search($edge, $this->edges);
        if (false !== $key) {
            unset($this->edges[$key]);
        }
    }
    
    public function __toString(): string
    {
        return $this->id;
    }
}
