<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2023;

use App\ConundrumSolver\AbstractConundrumSolver;

// /// Day 12: Hot Springs ///
class Day12ConundrumSolver extends AbstractConundrumSolver
{
    private array $possibilities = [];

    public function __construct()
    {
        parent::__construct('2023', '12');
    }

    #[\Override]
    public function warmup(): void
    {
        /**
         * TODO
         * This part is a mess and was left a mess after Dec 12, 2023
         * I might go back and try to do it again later.
         */
        foreach ($this->getInput() as $line) {
            [$damagedRecord, $wholeRecord] = explode(' ', (string) $line);
            $groups = [
                'solved'   => [],
                'unsolved' => array_map('\intval', explode(',', $wholeRecord)),
            ];

            preg_match_all('/(\?+|#+|\.+)/', $damagedRecord, $matches, PREG_OFFSET_CAPTURE);
            $matches = $matches[0];

            foreach (str_split($damagedRecord) as $char) {
                dump($char);
            }
            die;

            dump($damagedRecord);
            $groupKey = 0;
            $this->tmp2(strlen($damagedRecord), str_split($damagedRecord), $groups, null, $groupKey);
            // foreach (str_split($line) as $character) {
            // }
            dump($this->possibilities);
            die;
            $this->possibilities = 0;
        }
    }

    ////////////////
    // PART 1
    ////////////////

    #[\Override]
    public function partOne(): string|int
    {
        return self::UNDETERMINED;
    }

    ////////////////
    // PART 2
    ////////////////

    #[\Override]
    public function partTwo(): string|int
    {
        return self::UNDETERMINED;
    }

    ////////////////
    // METHODS
    ////////////////

    private function tmp2(int $patternLength, array $characters, array &$groups, ?string $uid, int &$groupKey)
    {
        // TODO : pattern as keys
        // $uid = $uid ?? $this->getUid();
        // if (!isset($this->possibilities[$uid])) {
        //     $this->possibilities[$uid] = 0;
        // }

        // Si c'est vide, null
        $first = array_shift($characters);
        $uid .= $first;

        if ('.' === $first) {
            // On ne peut rien mettre, on continue
            $this->tmp2($patternLength, $characters, $groups, $uid, $groupKey);
        } elseif ('?' === $first) {
            $uid2 = substr_replace($uid, '.', -1, 1);
            $this->tmp2($patternLength, $characters, $groups, $uid2, $groupKey);

            $uid3 = substr_replace($uid, '#', -1, 1);
            $this->tmp2($patternLength, $characters, $groups, $uid3, $groupKey);
        } elseif ('#' === $first) {
            $groupLength = $groups['unsolved'][$groupKey] ?? 0;

            if (0 < $groupLength) {
                $remainingChars = $this->getRemainingCharsBeforeSpring($characters);

                if ($groupLength <= $remainingChars) {
                    $uid .= implode('', \array_slice($characters, 0, $groupLength));
                    $characters = \array_slice($characters, $groupLength);
                    $groupKey++;

                    if (
                        !empty($characters)
                        && $groupKey <= array_key_last($groups['unsolved'])
                    ) {
                        $this->tmp2($patternLength, $characters, $groups, $uid, $groupKey);
                    }
                } else {
//                     dump($remainingChars);
//                     $uid .= implode('', $characters);
// dump($uid, $groupKey);die;
//                     if (!isset($this->possibilities[$uid])) {
//                         $this->possibilities[$uid] = [
//                             $groupKey => 0,
//                         ];
//                     } else {
//                         $this->possibilities[$uid][$groupKey] = 0;
//                     }
                }
            }
        }

        if (empty($characters)) {
            $this->possibilities[$uid] = array_fill_keys(range(1, $groupKey), 1);
            // Si tu arrives jusqu'à la fin en ayant tout placé, 1, sinon 0
            // if (!isset($this->possibilities[$uid])) {
            //     $this->possibilities[$uid] = [
            //         $groupKey => 0,
            //     ];
            // } else {
            //     $this->possibilities[$uid][$groupKey] = 0;
            // }
        }
    }

    /**
     * if it starts with a ., discard the . and recursively check again.
     *
     * if it starts with a ?, replace the ? with a . and recursively check again, AND replace it with a # and
     * recursively check again.
     *
     * it it starts with a #, check if it is long enough for the first group, check if all characters in the
     * first [grouplength] characters are not '.', and then remove the first [grouplength] chars and the first
     * group number, recursively check again.
     *
     * at some point you will get to the point of having an empty string and more groups to do - that is a
     * zero. or you have an empty string with zero gropus to do - that is a one.
     *
     * there are more rules to check than these few, which are up to you to find. but this is a way to work
     * out the solution.
     */
    private function tmp(array $characters, array &$groups, ?string $uid, int &$groupKey)
    {
        // TODO : pattern as keys
        // $uid = $uid ?? $this->getUid();
        // if (!isset($this->possibilities[$uid])) {
        //     $this->possibilities[$uid] = 0;
        // }

        // Si c'est vide, null
        $first = array_shift($characters);
        $uid .= $first;

        if ('.' === $first) {
            // On ne peut rien mettre, on continue
            $this->possibilities[$uid] += $this->tmp($characters, $groups, $uid, $groupKey);
        } elseif ('?' === $first) {
            $uid2 = substr_replace($uid, '.', -1, 1);
            array_unshift($characters, '.');
            $this->possibilities[$uid2] += $this->tmp($characters, $groups, $uid2, $groupKey);

            // Reset UID (changement branche)
            // $secondaryUid = $this->getUid();
            // $this->possibilities[$secondaryUid] = $this->possibilities[$uid];

            $uid3 = substr_replace($uid, '#', -1, 1);
            $characters[0] = '#';
            $this->possibilities[$uid3] += $this->tmp($characters, $groups, $uid3, $possibilities);
        } elseif ('#' === $first) {
            /**
             * it it starts with a #, check if it is long enough for the first group, check if all characters in
             * the first [grouplength] characters are not '.', and then remove the first [grouplength] chars and
             * the first group number, recursively check again.
             */

            // if (isset($this->possibilities[$uid])) {
            //     $groupLength = $groups['unsolved'][$this->possibilities[$uid]] ?? 0;
            // } else {
            //     $groupLength = $groups['unsolved'][0];
            //     $this->possibilities[$uid] = 0;
            // }
            if (!isset($this->possibilities[$uid])) {
                $this->possibilities[$uid] = [];
                $key = 0;
            } else {
                $key = array_key_last($this->possibilities[$uid]) + 1;
            }

            $groupLength = $groups['unsolved'][$key];

            if (0 < $groupLength) {
                $remainingChars = $this->getRemainingCharsBeforeSpring($characters);
                if ($groupLength <= $remainingChars) {
                    $characters = \array_slice($characters, 0, $groupLength);
                    $this->possibilities[$uid][$key] = 1;

                    if (!empty($characters)) {
                        $this->possibilities[$uid] += $this->tmp($characters, $groups, $uid, $possibilities);
                    }
                }
            }
        }

        return (int) (\count(array_filter($this->possibilities[$uid], static fn($value) => 1 === $value))
            === \count($groups['unsolved']));
    }

    private function recursiveCheck(array $characters, array &$groups): void
    {
        // TODO : on teste pas toutes les possibilités là
        if (empty($characters)) {
            if (empty($groups['unsolved'])) {
                $this->possibilities++;
            }

            return;
        }

        $doesWork = 0;
        $first = array_shift($characters);

        if ('.' === $first) {
            $doesWork = $this->recursiveCheck($characters, $groups);
        } elseif ('?' === $first) {
            array_unshift($characters, '.');
            $doesWork = $this->recursiveCheck($characters, $groups);

            $characters[0] = '#';
            $this->recursiveCheck($characters, $groups);
        } elseif ('#' === $first) {
            /**
             * it it starts with a #, check if it is long enough for the first group, check if all characters in
             * the first [grouplength] characters are not '.', and then remove the first [grouplength] chars and
             * the first group number, recursively check again.
             */
            // $brokenSpringGroupLength = $this->getBrokenSpringGroupLength($characters);
            $groupLength = $groups['unsolved'][0] ?? 0;
            if (0 === $groupLength) {
                $this->recursiveCheck([], $groups);
            }

            $remainingChars = \count($characters);
            if ($groupLength > $remainingChars) {
                $this->recursiveCheck([], $groups);
            }

            $availableSpace = 0;
            foreach ($characters as $iValue) {
                if ('.' === $iValue) {
                    $availableSpace = 0;
                } else {
                    $availableSpace++;
                }
            }

            if ($availableSpace >= $groupLength) {
                $groups['solved'][] = array_shift($groups['unsolved']);
                $characters = \array_slice($characters, 0, $groupLength);

                $this->recursiveCheck($characters, $groups);
            }
        }
    }

    private function getUid(): string
    {
        return uniqid('', true);
    }

    private function getRemainingCharsBeforeSpring(array $characters): int
    {
        $i = 1;

        foreach ($characters as $character) {
            if ('.' !== $character) {
                ++$i;
            }
        }

        return $i;
    }
}
