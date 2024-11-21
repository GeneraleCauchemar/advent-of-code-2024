<?php

declare(strict_types=1);

namespace App\Command;

use App\ConundrumSolver\AbstractConundrumSolver;
use App\ConundrumSolver\ConundrumSolverInterface;
use App\Exception\SolverNotFoundException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:resolve-conundrums')]
class ResolveConundrumsCommand extends Command
{
    private string $year;
    private string $day;
    private bool $testMode;

    public function __construct()
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
                    '<christmas_red%s>%s</>',
                    $this->testMode ? '_test' : '',
                    str_pad(
                        strtoupper(
                            \sprintf(
                                '%s December %s, 2023 ',
                                $this->testMode ? ' // TEST //' : '',
                                $this->day,
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

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $banner = \sprintf(
                '<error>%s</>',
                str_repeat(' ', Helper::width(Helper::removeDecoration($io->getFormatter(), $error)))
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
        $day = $this->getDay($this->day);
        $className = \sprintf(
            'App\\ConundrumSolver\\%s\\Day%sConundrumSolver',
            'Year' . $this->year,
            $day
        );

        return class_exists($className) ?
            new $className($this->year, $day) :
            throw new SolverNotFoundException(
                \sprintf('<error>There is no solver available for day %s of %s!</error>', $this->day, $this->year)
            );
    }

    private function getDay(string $day): string
    {
        return str_pad($day, 2, '0', STR_PAD_LEFT);
    }

    private function formatResultForDisplay(array $result): string
    {
        $line = explode('|', ' Solution | to | part | one | is | %s | and | solution | to | part | two | is | %s |.');

        array_walk($line, function (&$word, $key): void {
            $color = match (true) {
                str_contains($word, '%s') => 'green' . ($this->testMode ? '_test' : ''),
                !($key & 1) => 'red' . ($this->testMode ? '_test' : ''),
                default => 'white',
            };

            $word = \sprintf('<christmas_%s>%s</>', $color, $word);
        });

        return '<christmas_white> 🎄 </>' . \sprintf(implode('', $line), ...$result) . '<christmas_white> 🎄 </>';
    }

    private function getIo(InputInterface $input, OutputInterface $output): SymfonyStyle
    {
        $io = new SymfonyStyle($input, $output);
        $styles = [
            'christmas_red'        => new OutputFormatterStyle(null, '#ff0000'),
            'christmas_white'      => new OutputFormatterStyle('black', '#fff'),
            'christmas_green'      => new OutputFormatterStyle(null, '#009930'),
            'christmas_red_test'   => new OutputFormatterStyle(null, 'bright-blue'),
            'christmas_green_test' => new OutputFormatterStyle(null, 'bright-yellow'),
        ];

        foreach ($styles as $name => $style) {
            $io->getFormatter()->setStyle($name, $style);
        }

        return $io;
    }
}
