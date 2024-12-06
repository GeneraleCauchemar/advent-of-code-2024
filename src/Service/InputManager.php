<?php

namespace App\Service;

use App\ConundrumSolver\AbstractConundrumSolver;
use App\Exception\InputFileNotFoundException;

class InputManager
{
    private const string INPUT_PATH_FORMAT = '%s/../Resources/input/%s%s/%s%s.txt';

    private bool $keepAsString;
    private string $separator;
    private string $year;
    private string $day;

    public function setParameters(string $year, string $day, bool $keepAsString, string $separator): void
    {
        $this->year = $year;
        $this->day = $day;
        $this->keepAsString = $keepAsString;
        $this->separator = $separator;
    }

    /**
     * @throws InputFileNotFoundException
     */
    public function initInput(): array|string
    {
        $path = $this->getPathToInput();

        if (!file_exists($path)) {
            throw new InputFileNotFoundException(
                \sprintf('Missing input file at path "%s".', $path)
            );
        }

        return $this->getContent($path);
    }

    /**
     * @throws InputFileNotFoundException
     */
    public function initTestInputs(): array
    {
        $inputs = [];
        $paths = [
            AbstractConundrumSolver::PART_ONE => $this->getPathToInput(true, AbstractConundrumSolver::PART_ONE),
            AbstractConundrumSolver::PART_TWO => $this->getPathToInput(true, AbstractConundrumSolver::PART_TWO),
        ];

        // If there is a different test input for each part
        foreach ($paths as $part => $path) {
            if (!file_exists($path)) {
                continue;
            }

            $inputs[$part] = $this->getContent($path);
        }

        // If no separate test inputs were found, tries to find a common one
        if (empty($inputs)) {
            $path = $this->getPathToInput(true);

            if (file_exists($path)) {
                $inputs = array_fill_keys([
                    AbstractConundrumSolver::PART_ONE,
                    AbstractConundrumSolver::PART_TWO,
                ], $this->getContent($path));
            }
        }

        if (empty($inputs)) {
            throw new InputFileNotFoundException('Missing input file(s) for testing.');
        }

        return $inputs;
    }

    private function getPathToInput(bool $forTesting = false, ?int $part = null): string
    {
        $values = [
            __DIR__,
            'Year' . $this->year,
            $forTesting ? '/test' : '',
            $this->day,
            null !== $part ? ('_' . $part) : '',
        ];

        return \sprintf(
            self::INPUT_PATH_FORMAT,
            ...$values
        );
    }

    private function getContent(string $path): array|string
    {
        return $this->keepAsString ?
            trim(file_get_contents($path)) :
            array_filter(explode($this->separator, file_get_contents($path)));
    }
}
