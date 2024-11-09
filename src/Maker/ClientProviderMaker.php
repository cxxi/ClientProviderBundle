<?php

namespace Cxxi\ClientProviderBundle\Maker;

use Cxxi\ClientProviderBundle\Attribute\AsClientProvider;
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

final class ClientProviderMaker extends AbstractMaker
{
    public function __construct(
        private FileManager $fileManager,
        private Generator $generator
    ){}

    public static function getCommandName(): string
    {
        return 'make:provider:client';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a new client provider class';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the client provider class')
            ->setDescription(self::getCommandDescription())
            ->setHelp(file_get_contents(__DIR__.'/../Resources/help/MakeClientProvider.txt'))
        ;
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $name = $input->getArgument('name');
        
        $classNameDetails = $generator->createClassNameDetails(
            $name, 
            'Provider\\Client\\',
            ''
        );

        $useStatements = new UseStatementGenerator([
            AsClientProvider::class
        ]);

        $generator->generateClass(
            $classNameDetails->getFullName(),
            __DIR__ . '/../Resources/skeleton/ClientProvider.tpl.php',
            [
                'use_statements' => $useStatements,
                'class_name' => $classNameDetails->getShortName(),
                'client_provider_name' => strtolower($name),
                'provider_class_name' => 'Lol'
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