<?php

namespace Cxxi\ClientProviderBundle\Maker;

use Cxxi\ClientProviderBundle\Contracts\ProviderInterface;
use Cxxi\ClientProviderBundle\Attribute\AsProvider;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

final class ProviderMaker extends AbstractMaker
{
    public function __construct(
        private FileManager $fileManager,
        private Generator $generator
    ){}

    public static function getCommandName(): string
    {
        return 'make:provider';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a new provider class';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the provider class')
            ->setDescription(self::getCommandDescription())
            ->setHelp(file_get_contents(__DIR__.'/../Resources/help/MakeProvider.txt'))
        ;
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $name = $input->getArgument('name');
        
        $classNameDetails = $generator->createClassNameDetails(
            $name, 
            'Provider\\',
            'Provider'
        );

        $useStatements = new UseStatementGenerator([
            ProviderInterface::class,
            AsProvider::class
        ]);

        $generator->generateClass(
            $classNameDetails->getFullName(),
            __DIR__ . '/../Resources/skeleton/Provider.tpl.php',
            [
                'use_statements' => $useStatements,
                'class_name' => $classNameDetails->getShortName(),
                'provider_name' => strtolower($name)
            ]
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
        $io->text([
            'Next: open your new command class and customize it!',
            'Find the documentation at <fg=yellow>https://symfony.com/doc/current/console.html</>',
        ]);
    }

    public function configureDependencies(DependencyBuilder $dependencies): void {}
}