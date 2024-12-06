<?php

namespace App\Maker;

use App\ConundrumSolver\AbstractConundrumSolver;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Webmozart\Assert\Assert;

/**
 * @method string getCommandDescription()
 */
final class MakeSolver extends AbstractMaker
{
    public function __construct(#[Autowire('%kernel.project_dir%')] private readonly string $projectDir)
    {
    }

    public static function getCommandName(): string
    {
        return 'make:solver';
    }

    public static function getCommandDescription(): string
    {
        return 'Jumpstart what you need to solve a new day\'s conundrums.';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        // TODO
        $command
            ->addArgument('year', InputArgument::REQUIRED, 'For which year?')
            ->addArgument('day', InputArgument::REQUIRED, 'And which day?')
        ;
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $year = $input->getArgument('year');
        $day = str_pad($input->getArgument('day'), 2, '0', STR_PAD_LEFT);

        Assert::length($year, 4);
        Assert::length($day, 2);

        $yearFolder = 'Year' . $year;
        $dayFolder = 'Day' . $day;

        $classNameDetails = $generator->createClassNameDetails(
            \sprintf('%s\%s', $yearFolder, $dayFolder),
            'ConundrumSolver\\',
            'ConundrumSolver'
        );

        $useStatements = new UseStatementGenerator([AbstractConundrumSolver::class]);

        $generator->generateClass(
            $classNameDetails->getFullName(),
            $this->projectDir . '/src/Resources/skeleton/ConundrumSolver.tpl.php',
            [
                'use_statements' => $useStatements,
                'route_path'     => Str::asRoutePath($classNameDetails->getRelativeNameWithoutSuffix()),
                'route_name'     => Str::asRouteName($classNameDetails->getRelativeNameWithoutSuffix()),
                'year'           => $year,
                'day'            => $day,
            ]
        );

        fopen($this->projectDir . '/src/Resources/input/' . $yearFolder . '/test/' . $day . '.txt', 'xb');
        fopen($this->projectDir . '/src/Resources/input/' . $yearFolder . '/' . $day . '.txt', 'xb');

        $generator->writeChanges();
        $this->writeSuccessMessage($io);
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }
}
