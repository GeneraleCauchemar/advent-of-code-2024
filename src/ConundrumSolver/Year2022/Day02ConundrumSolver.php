<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2022;

use App\ConundrumSolver\AbstractConundrumSolver;

// --- Day 2: Rock Paper Scissors ---
// PART ONE: 10816, PART TWO: 11657
class Day02ConundrumSolver extends AbstractConundrumSolver
{
    private const int WIN = 6;
    private const int DRAW = 3;
    private const int LOSE = 0;
    private const int ROCK = 1;
    private const int PAPER = 2;
    private const int SCISSORS = 3;
    private const array OUTCOMES = [
        'A X' => self::DRAW,
        'A Y' => self::WIN,
        'A Z' => self::LOSE,
        'B X' => self::LOSE,
        'B Y' => self::DRAW,
        'B Z' => self::WIN,
        'C X' => self::WIN,
        'C Y' => self::LOSE,
        'C Z' => self::DRAW,
    ];
    private const array MOVE_SCORE = [
        'X' => self::ROCK,
        'Y' => self::PAPER,
        'Z' => self::SCISSORS,
    ];
    private const array OUTCOME_SCORE = [
        'X' => self::LOSE,
        'Y' => self::DRAW,
        'Z' => self::WIN,
    ];

    public function __construct(string $year, string $day)
    {
        parent::__construct($year, $day);
    }

    ////////////////
    // PART 1
    ////////////////

    #[\Override]
    public function partOne(): string|int
    {
        $outcomes = self::OUTCOMES;
        $tacticalScores = self::MOVE_SCORE;
        $scores = array_map(
            static fn($value): int => $outcomes[$value] + $tacticalScores[substr((string) $value, -1)],
            $this->getInput()
        );

        return array_sum($scores);
    }

    ////////////////
    // PART 2
    ////////////////

    #[\Override]
    public function partTwo(): string|int
    {
        $results = array_map(static function ($value): int {
            $tacticalScores = self::OUTCOME_SCORE;
            $strategies = [
                'X' => [
                    'A' => self::MOVE_SCORE['Z'],
                    'B' => self::MOVE_SCORE['X'],
                    'C' => self::MOVE_SCORE['Y'],
                ],
                'Y' => [
                    'A' => self::MOVE_SCORE['X'],
                    'B' => self::MOVE_SCORE['Y'],
                    'C' => self::MOVE_SCORE['Z'],
                ],
                'Z' => [
                    'A' => self::MOVE_SCORE['Y'],
                    'B' => self::MOVE_SCORE['Z'],
                    'C' => self::MOVE_SCORE['X'],
                ],
            ];
            $params = preg_split('/\s+/', $value);

            return $strategies[$params[1]][$params[0]] + $tacticalScores[$params[1]];
        }, $this->getInput());

        return array_sum($results);
    }
}
