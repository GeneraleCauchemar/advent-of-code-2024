<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2022;

use App\ConundrumSolver\AbstractConundrumSolver;
use App\Entity\Year2022\Day11\Monkey;

// --- Day 11: Monkey in the Middle ---
// PART ONE: 67830, PART TWO: 15305381442
class Day11ConundrumSolver extends AbstractConundrumSolver
{
    private const string STARTING_ITEMS = 'Starting items: ';
    private const string OPERATION = 'Operation: new = old ';

    private array $monkeys;
    private int $supermodulo;

    public function __construct()
    {
        parent::__construct('2022', '11', 'Monkey ');
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): string|int
    {
        $this->initMonkeys();
        $this->playRounds(20);

        return $this->getMonkeyBusiness();
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): string|int
    {
        $this->initMonkeys();
        $this->playRounds(10000, self::PART_TWO);

        return $this->getMonkeyBusiness();
    }

    ////////////////
    // METHODS
    ////////////////

    private function initMonkeys(): void
    {
        $input = $this->getInput();
        $this->supermodulo = 1;

        foreach ($input as $info) {
            $info = array_combine([
                'id',
                'items',
                'operation',
                'test',
                'if_true',
                'if_false',
            ], array_filter(explode(PHP_EOL, $info)));
            $args = [
                'id'        => FILTER_SANITIZE_NUMBER_INT,
                'items'     => [
                    'filter'  => FILTER_CALLBACK,
                    'options' => function ($value): array {
                        $value = str_replace(self::STARTING_ITEMS, '', trim($value));

                        return array_map('\intval', explode(', ', $value));
                    },
                ],
                'operation' => [
                    'filter'  => FILTER_CALLBACK,
                    'options' => fn($value): array => [
                        'operand'  => str_contains((string) $value, '*') ? Monkey::MULTIPLY : Monkey::ADD,
                        'modifier' => filter_var($value, FILTER_CALLBACK, [
                            'options' => function ($value): string {
                                $value = str_replace(self::OPERATION, '', trim((string) $value));
                                $value = explode(' ', $value);

                                return $value[1];
                            },
                        ]),
                    ],
                ],
                'test'      => FILTER_SANITIZE_NUMBER_INT,
                'if_true'   => FILTER_SANITIZE_NUMBER_INT,
                'if_false'  => FILTER_SANITIZE_NUMBER_INT,
            ];

            $info = filter_var_array($info, $args);
            $test = (int) $info['test'];

            $this->monkeys[(int) $info['id']] = new Monkey(
                (int) $info['id'],
                $info['items'],
                $info['operation']['operand'],
                $info['operation']['modifier'],
                $test,
                (int) $info['if_true'],
                (int) $info['if_false']
            );

            $this->supermodulo *= $info['test'];
        }
    }

    private function playRounds(int $rounds, int $part = self::PART_ONE): void
    {
        for ($i = 0; $i < $rounds; $i++) {
            /** @var Monkey $monkey */
            foreach ($this->monkeys as $monkey) {
                $moves = $monkey->processTurn($part, $this->supermodulo);

                foreach ($moves as $monkeyId => $items) {
                    $this->monkeys[$monkeyId]->receiveItems($items);
                }
            }
        }
    }

    private function getMonkeyBusiness(): int
    {
        $monkeyBusiness = [];

        foreach ($this->monkeys as $monkey) {
            $monkeyBusiness[$monkey->getId()] = $monkey->getInspectedItems();
        }

        rsort($monkeyBusiness, SORT_NUMERIC);

        return (int) $monkeyBusiness[0] * $monkeyBusiness[1];
    }
}
