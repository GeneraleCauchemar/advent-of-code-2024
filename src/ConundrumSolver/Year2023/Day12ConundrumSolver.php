<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2023;

use App\ConundrumSolver\AbstractConundrumSolver;

// /// Day 12: Hot Springs ///
class Day12ConundrumSolver extends AbstractConundrumSolver
{
    public function __construct(string $year, string $day)
    {
        parent::__construct($year, $day);
    }

    public function warmup(): void
    {
        /**
         * TODO
         * This part is a mess and was left a mess after Dec 12, 2023
         * I might go back and try to do it again later.
         */
        foreach ($this->getInput() as $line) {
            [$damagedRecord, $wholeRecord] = explode(' ', $line);
            $groups = array_map('\intval', explode(',', $wholeRecord));

            preg_match_all('/(\?+|#+|\.+)/', $damagedRecord, $matches, PREG_OFFSET_CAPTURE);
            $matches = $matches[0];
            dump($damagedRecord, $groups, $matches);
            die;

            foreach ($groups as $groupLength) {
                foreach ($matches as $key => [$match, $offset]) {
                    if (str_contains($match, '?')) {

                    }
                }
            }

            $before = '';

            foreach ($matches[0] as $key => [$match, $offset]) {
                $after = $matches[0][$key + 1] ?? '';
// TODO : essayer de caler les plus gros groupes d'abord ?
                if (str_contains((string) $match, '?')) {
                    $length = strlen((string) $match);

                    // En fonction de la longueur, combien de combinaisons possible ?
                    // Ca dépend aussi du caractère avant/après
                    dd($match, $offset, $length);
                }

                $before = substr((string) $match, -1);
            }
            dump($damagedRecord, $matches[0], $groups);
            die;
            // preg_match_all('/(\?+)/', $damagedRecord, $missing, PREG_OFFSET_CAPTURE);
            // preg_match_all('/(#+)/', $damagedRecord, $broken, PREG_OFFSET_CAPTURE);
            // dump($damagedRecord, $missing[0], $broken[0]);
            // dd($damagedRecord, $groups, $matches['missing'], $matches['broken']);
        }
        die;
        dd($this->getInput());
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): string|int
    {
        return self::UNDETERMINED;
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): string|int
    {
        return self::UNDETERMINED;
    }

    ////////////////
    // METHODS
    ////////////////

}
