<?php

declare(strict_types=1);

namespace App\ConundrumSolver;

use App\Exception\InputFileNotFoundException;

abstract class AbstractConundrumSolver implements ConundrumSolverInterface
{
    protected const int PART_ONE = 1;
    protected const int PART_TWO = 2;
    protected const string UNDETERMINED = 'to be determined';

    private bool $testMode = false;
    private array|string $input;
    private array $testInputs;

    public function __construct(
        protected readonly string $year = '',
        protected readonly string $day = '',
        private readonly ?string $separator = PHP_EOL,
        private readonly bool $keepAsString = false,
    ) {
    }

    public function supports(string $year, string $day): bool
    {
        return $this->year === $year && $this->day === $day;
    }

    /**
     * @throws InputFileNotFoundException
     */
    #[\Override]
    public function execute(bool $testMode = false): array
    {
        $this->testMode = $testMode;

        $this->init();
        $this->warmup();

        return [
            $this->partOne(),
            $this->partTwo(),
        ];
    }

    public function warmup(): void
    {
    }

    #[\Override]
    public function partOne(): string|int
    {
        return self::UNDETERMINED;
    }

    #[\Override]
    public function partTwo(): string|int
    {
        return self::UNDETERMINED;
    }

    protected function isTestMode(): bool
    {
        return $this->testMode;
    }

    protected function getInput(int $part = self::PART_ONE): array|string
    {
        return $this->isTestMode() ? $this->getTestInput($part) : $this->input;
    }

    protected function getTestInput(int $part = self::PART_ONE)
    {
        if (\array_key_exists($part, $this->testInputs)) {
            return $this->testInputs[$part];
        }

        return [];
    }

    protected function print(array $input): void
    {
        foreach ($input as $line) {
            echo implode('', $line) . PHP_EOL;
        }
    }

    /**
     * @throws InputFileNotFoundException
     */
    private function init(): void
    {
        $this->isTestMode() ? $this->initTestInputs() : $this->initInput();
    }

    private function initInput(): void
    {
        $path = $this->getPathToInput();

        if (!file_exists($path)) {
            throw new InputFileNotFoundException(
                \sprintf('Missing input file at path "%s".', $path)
            );
        }

        $this->input = $this->getContent($path);
    }

    private function initTestInputs(): void
    {
        $this->testInputs = [];
        $paths = [
            self::PART_ONE => $this->getPathToInput(true, self::PART_ONE),
            self::PART_TWO => $this->getPathToInput(true, self::PART_TWO),
        ];

        // If there is a different test input for each part
        foreach ($paths as $part => $path) {
            if (!file_exists($path)) {
                continue;
            }

            $this->testInputs[$part] = $this->getContent($path);
        }

        // If no separate test inputs were found, tries to find a common one
        if (empty($this->testInputs)) {
            $path = $this->getPathToInput(true);

            if (file_exists($path)) {
                $this->testInputs = array_fill_keys([self::PART_ONE, self::PART_TWO], $this->getContent($path));
            }
        }

        if (empty($this->testInputs)) {
            throw new InputFileNotFoundException('Missing input file(s) for testing.');
        }
    }

    private function getContent(string $path): array|string
    {
        return $this->keepAsString ?
            trim(file_get_contents($path)) :
            array_filter(explode($this->separator, file_get_contents($path)));
    }

    private function getPathToInput(bool $forTesting = false, ?int $part = null): string
    {
        $format = __DIR__ . '/../../Resources/input/%s%s/%s%s.txt';
        $values = [
            'Year' . $this->year,
            $forTesting ? '/test' : '',
            $this->day,
            null !== $part ? ('_' . $part) : '',
        ];

        return \sprintf(
            $format,
            ...$values
        );
    }
}
