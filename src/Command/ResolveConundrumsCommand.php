<?php

declare(strict_types=1);

namespace App\Command;

use App\Console\ChristmasStyle;
use App\Console\ChristmasTestStyle;
use App\ConundrumSolver\AbstractConundrumSolver;
use App\ConundrumSolver\ConundrumSolverInterface;
use App\ConundrumSolver\SolverHandler;
use App\Exception\SolverNotFoundException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;

#[AsCommand(name: 'app:resolve-conundrums')]
class ResolveConundrumsCommand extends Command
{
    private string $year;
    private string $day;
    private bool $testMode;

    public function __construct(private readonly SolverHandler $solverHandler)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addArgument('year', InputArgument::REQUIRED)
            ->addArgument('day', InputArgument::REQUIRED)
            ->addOption('with-test-input', 'T')
        ;
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->year = $input->getArgument('year');
        $this->day = $input->getArgument('day');
        $this->testMode = $input->getOption('with-test-input');

        $io = $this->getIo($input, $output);

        try {
            // Solve
            /** @var AbstractConundrumSolver $conundrumSolver */
            $conundrumSolver = $this->getSolverForDate();
            $result = $conundrumSolver->execute($this->testMode);

            // Display results
            $result = $this->formatResultForDisplay($result);
            $banner = \sprintf(
                '<christmas_white>%s</>',
                str_repeat(' ', Helper::width(Helper::removeDecoration($io->getFormatter(), $result)))
            );

            $io->text([
                \sprintf(
                    '<christmas_red>%s</>',
                    str_pad(
                        strtoupper(
                            \sprintf(
                                '%s December %s, %s ',
                                $this->testMode ? ' // TEST //' : '',
                                $this->day,
                                $this->year
                            )
                        ),
                        Helper::width(Helper::removeDecoration($io->getFormatter(), $result)),
                    )
                ),
                $banner,
                $result,
                $banner,
                '',
            ]);

            if (!$this->testMode) {
                $io->table(
                    ['Part one', 'Part two'],
                    [$conundrumSolver->getExecutionTimes()]
                );
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $error = \sprintf(
                '%s [%s, l. %s]',
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            );
            $banner = \sprintf(
                '<error>%s</>',
                str_repeat(' ', Helper::width(Helper::removeDecoration($io->getFormatter(), $error))),
            );

            $io->text([
                $banner,
                $error,
                $banner,
                '',
            ]);
        }

        return Command::FAILURE;
    }

    /**
     * @throws SolverNotFoundException
     */
    private function getSolverForDate(): ConundrumSolverInterface
    {
        return $this->solverHandler->getSolverForDate($this->year, $this->getDay($this->day));
    }

    private function getDay(string $day): string
    {
        return str_pad($day, 2, '0', STR_PAD_LEFT);
    }

    private function formatResultForDisplay(array $result): string
    {
        $line = explode('|', ' Solution | to | part | one | is | %s | and | solution | to | part | two | is | %s |.');

        array_walk($line, static function (&$word, $key): void {
            $color = match (true) {
                str_contains($word, '%s') => 'green',
                !($key & 1) => 'red',
                default => 'white',
            };

            $word = \sprintf('<christmas_%s>%s</>', $color, $word);
        });

        return '<christmas_white> 🎄 </>' . \sprintf(implode('', $line), ...$result) . '<christmas_white> 🎄 </>';
    }

    private function getIo(InputInterface $input, OutputInterface $output): StyleInterface
    {
        return $this->testMode
            ? new ChristmasTestStyle($input, $output)
            : new ChristmasStyle($input, $output);
    }
}
