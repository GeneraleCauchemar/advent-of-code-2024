<?php

declare(strict_types=1);

namespace App\Command;

use App\Console\ChristmasStyle;
use App\Service\InputGrabber;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsCommand(name: 'app:grab-input')]
class GrabInputCommand extends Command
{
    public function __construct(private readonly InputGrabber $inputGrabber)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addArgument('year', InputArgument::REQUIRED)
            ->addArgument('day', InputArgument::REQUIRED)
            ->addUsage(' <year> <day>')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $year = (int) $input->getArgument('year');
        $day = (int) $input->getArgument('day');
        $io = new ChristmasStyle($input, $output);

        try {
            $this->inputGrabber->grabInput($year, $day);
        } catch (TransportExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            $io->error([
                'There was a problem while getting your input.',
                $e->getMessage(),
            ]);

            return Command::FAILURE;
        }

        $io->write('Input successfully downloaded!');

        return Command::SUCCESS;
    }
}
