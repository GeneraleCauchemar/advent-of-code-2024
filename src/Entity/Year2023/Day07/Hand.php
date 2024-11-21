<?php

namespace App\Entity\Year2023\Day07;

class Hand
{
    public array $groupedCards = [];
    public ?Type $type = null;

    public function __construct(
        public string $cards,
        public int $bid
    )
    {
    }
}
