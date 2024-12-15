<?php

declare(strict_types=1);

namespace App\ConundrumSolver;

use App\Exception\InputFileNotFoundException;
use App\Service\InputManager;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractConundrumSolver implements ConundrumSolverInterface
{
    public const int PART_ONE = 1;
    public const int PART_TWO = 2;
    protected const string UNDETERMINED = 'to be determined';

    private bool $testMode = false;
    private array|string $input;
    private array $testInputs;
    protected ?float $executionTime = null;
    private InputManager $inputManager;

    public function __construct(
        protected readonly string $year = '',
        protected readonly string $day = '',
        private readonly ?string $separator = PHP_EOL,
        private readonly bool $keepAsString = false,
    ) {
    }

    #[Required]
    public function setInputManager(InputManager $inputManager): void
    {
        $this->inputManager = $inputManager;

        $this->inputManager->setParameters(
            $this->year,
            $this->day,
            $this->keepAsString,
            $this->separator
        );
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

        if (!$testMode) {
            return $this->trackAndSolve();
        }

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

    public function getExecutionTime(): ?string
    {
        return null !== $this->executionTime
            ? (number_format($this->executionTime, 6) . 's')
            : null;
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

    protected function waitForNextStep(): void
    {
        echo 'Continue?';
        $handle = fopen ('php://stdin', 'rb');
        fgets($handle);
        fclose($handle);
    }

    /**
     * @throws InputFileNotFoundException
     */
    private function init(): void
    {
        $this->isTestMode() ? $this->initTestInputs() : $this->initInput();
    }

    /**
     * @throws InputFileNotFoundException
     */
    private function initInput(): void
    {
        $this->input = $this->inputManager->initInput();
    }

    /**
     * @throws InputFileNotFoundException
     */
    private function initTestInputs(): void
    {
        $this->testInputs = $this->inputManager->initTestInputs();
    }

    private function trackAndSolve(): array
    {
        $this->warmup();

        $startTime = microtime(true);
        $result = [
            $this->partOne(),
            $this->partTwo(),
        ];
        $endTime = microtime(true);

        $this->executionTime = $endTime - $startTime;

        return $result;
    }
}
