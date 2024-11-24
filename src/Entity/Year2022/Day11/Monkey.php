<?php

declare(strict_types=1);

namespace App\Entity\Year2022\Day11;

class Monkey
{
    public const string MULTIPLY = 'multiply';
    public const string ADD = 'add';
    private int|float|array|null $currentWorryLevel = null;
    private int $inspectedItems;

    public function __construct(
        private readonly int $id,
        private array $items,
        private readonly string $operand,
        private readonly string|int $modifier,
        private $test,
        private readonly int $toMonkeyIfTrue,
        private readonly int $toMonkeyIfFalse
    ) {
        $this->inspectedItems = 0;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getInspectedItems(): int
    {
        return $this->inspectedItems;
    }

    public function receiveItems(array $items): void
    {
        foreach ($items as $item) {
            $this->items[] = $item;
        }
    }

    public function processTurn(int $part, int $supermodulo): array
    {
        $moveTo = [];
        $this->resetWorryLevel();

        foreach ($this->items as $item) {
            $this->inspect($item, $supermodulo);

            if (1 === $part) {
                $this->computeRelief();
            }

            $moveTo[$this->isTest() ? $this->toMonkeyIfTrue : $this->toMonkeyIfFalse][] = $this->currentWorryLevel;

            $this->resetWorryLevel();
        }

        $this->items = [];

        return $moveTo;
    }

    private function resetWorryLevel(): void
    {
        $this->currentWorryLevel = 0;
    }

    private function inspect($item, int $supermodulo): void
    {
        $worryLevel = $item;
        $modifier = 'old' === $this->modifier ? $worryLevel : $this->modifier;

        $this->currentWorryLevel = self::MULTIPLY === $this->operand
            ? $worryLevel * $modifier
            : $worryLevel + $modifier;
        $this->currentWorryLevel %= $supermodulo;

        $this->inspectedItems++;
    }

    private function computeRelief(): void
    {
        $this->currentWorryLevel = (int) floor($this->currentWorryLevel / 3);
    }

    private function isTest(): bool
    {
        return 0 === $this->currentWorryLevel % $this->test;
    }
}
