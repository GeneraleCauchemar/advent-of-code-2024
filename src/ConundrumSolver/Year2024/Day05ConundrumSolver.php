<?php

declare(strict_types=1);

namespace App\ConundrumSolver\Year2024;

use App\ConundrumSolver\AbstractConundrumSolver;

// /// Day 5: Print Queue ///
class Day05ConundrumSolver extends AbstractConundrumSolver
{
    private array $pageOrderingRules;
    private array $rulesByPage;
    private array $partOneResult;
    private array $partTwoResult;

    public function __construct()
    {
        parent::__construct('2024', '05', PHP_EOL . PHP_EOL);
    }

    #[\Override]
    public function warmup(): void
    {
        [$pageOrderingRules, $updates] = $this->getInput();
        $updates = array_map(
            static fn($update) => array_map('\intval', explode(',', $update)),
            array_filter(explode(PHP_EOL, $updates))
        );

        $i = 0;
        foreach (explode(PHP_EOL, $pageOrderingRules) as $rule) {
            $pages = array_map('\intval', explode('|', $rule));
            $this->pageOrderingRules[$i] = $pages;

            foreach ($pages as $page) {
                $this->rulesByPage[$page][] = $i;
            }

            $i++;
        }

        $this->partOneResult = [];
        $this->partTwoResult = [];

        foreach ($updates as $update) {
            foreach ($update as $key => $page) {
                for ($i = $key + 1, $iMax = \count($update); $i < $iMax; $i++) {
                    if (!$this->pagesAreInTheRightOrder($page, $update[$i])) {
                        $this->reorderUpdatePages($update);
                        $this->partTwoResult[] = $this->getMiddlePage($update);

                        continue 3;
                    }
                }
            }

            $this->partOneResult[] = $this->getMiddlePage($update);
        }
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): string|int
    {
        return array_sum($this->partOneResult);
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): string|int
    {
        return array_sum($this->partTwoResult);
    }

    ////////////////
    // METHODS
    ////////////////

    private function pagesAreInTheRightOrder(int $a, int $b): bool
    {
        return !\in_array([$a, $b], $this->pageOrderingRules, true);
    }

    private function reorderUpdatePages(array &$update): void
    {
        $rules = $this->getRulesSpecificToUpdate($update);
        $order = array_fill_keys($update, 0);

        foreach ($rules as $id) {
            $pageId = reset($this->pageOrderingRules[$id]);
            $order[$pageId]++;
        }

        arsort($order);

        $update = array_keys($order);
    }

    /**
     * We only want the IDs of rules where both pages
     * are part of the update
     */
    private function getRulesSpecificToUpdate(array $update): array
    {
        $rules = [];
        foreach ($update as $page) {
            $rules[$page] = $this->rulesByPage[$page];
        }

        $rules = array_merge(...$rules);
        $rules = array_count_values($rules);
        $count = array_filter($rules, static function ($v) {
            return 2 === $v;
        });

        return array_keys($count);
    }

    private function getMiddlePage(array $update)
    {
        return $update[(int) floor(\count($update) / 2)];
    }
}
