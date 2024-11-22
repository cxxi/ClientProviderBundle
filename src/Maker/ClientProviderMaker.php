<?php

namespace Cxxi\ClientProviderBundle\Maker;

use Cxxi\ClientProviderBundle\Attribute\AsClientProvider;
use Cxxi\ClientProviderBundle\Contracts\ProviderInterface;
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
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the client provider')
            ->setDescription(self::getCommandDescription())
            ->setHelp(file_get_contents(sprintf('%s/../Resources/help/MakeClientProvider.txt', __DIR__)))
        ;
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $clientName = $input->getArgument('name');

        $providerName = $io->ask('The name of provider', AsClientProvider::STANDALONE, function($value) {
            return $value ?? ''; 
        });
        
        $classNameDetails = $generator->createClassNameDetails($clientName, 'Provider\\Client\\');

        $generator->generateClass(
            $classNameDetails->getFullName(),
            sprintf('%s/../Resources/skeleton/ClientProvider.tpl.php', __DIR__),
            [
                'class_name' => $classNameDetails->getShortName(),
                'use_statements' => $this->generateUseStatements($providerName),
                'attribute' => $this->generateAttribute($providerName, $clientName),
                'ancestor' => $this->generateAncestor($providerName)
            ]
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text([
            'Next: open your new client provider class and customize it!',
            'Find the documentation at <fg=yellow>https://symfony.com/doc/current/console.html</>',
        ]);
    }

    public function configureDependencies(DependencyBuilder $dependencies): void {}

    private function generateUseStatements(string $providerName): string
    {
        $useStatements = [ AsClientProvider::class ];

        if ($providerName === AsClientProvider::STANDALONE) {
            $useStatements[] = ProviderInterface::class;
        }

        return new UseStatementGenerator($useStatements);
    }

    private function generateAttribute(string $providerName, string $clientName): string
    {
        $parameters = [ sprintf("name: '%s'", $clientName) ];

        if ($providerName === AsClientProvider::STANDALONE) {
            $parameters[] = sprintf("standalone: true");
        }

        $attributeClass = basename(str_replace('\\', '/', AsClientProvider::class));

        return sprintf("#[%s(%s)]", $attributeClass, implode(', ', $parameters));
    }

    private function generateAncestor(string $providerName): string
    {
        $interfaceClass = basename(str_replace('\\', '/', ProviderInterface::class));

        return $providerName === AsClientProvider::STANDALONE
            ? sprintf('implements %s', $interfaceClass)
            : sprintf('extends %s', $providerClass)
        ;
    }
}