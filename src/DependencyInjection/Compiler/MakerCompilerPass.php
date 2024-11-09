<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\DependencyInjection\Compiler;

use Cxxi\ClientProviderBundle\Maker\ProviderMaker;
use Cxxi\ClientProviderBundle\Maker\ClientProviderMaker;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class MakerCompilerPass implements CompilerPassInterface
{   
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('maker.generator')) return;

        $container->setDefinition('cxxi.client_provider.maker.make_provider', (new Definition(ProviderMaker::class))
	        ->setArguments([
	            new Reference('maker.file_manager'),
	            new Reference('maker.generator')
	        ])
	        ->addTag('maker.command')
	        ->setPublic(true)
	   	);

	   	$container->setDefinition('cxxi.client_provider.maker.make_client_provider', (new Definition(ClientProviderMaker::class))
	        ->setArguments([
	            new Reference('maker.file_manager'),
	            new Reference('maker.generator')
	        ])
	        ->addTag('maker.command')
	        ->setPublic(true)
	   	);

        if (!$container->has('maker.file_manager')) {
            throw new \RuntimeException('maker.file_manager service not found. Is MakerBundle properly installed?');
        }
    }
}