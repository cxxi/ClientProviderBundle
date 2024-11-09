<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\DependencyInjection\Compiler;

use Cxxi\ClientProviderBundle\Contracts\ProviderRegistryInterface;
use Cxxi\ClientProviderBundle\Registry\ProviderRegistry;
use Cxxi\ClientProviderBundle\Util\AutowiringChecker;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class RegistryCompilerPass implements CompilerPassInterface
{   
    public function process(ContainerBuilder $container)
    {
    	if ($container->has(ProviderRegistry::class)) return;

        $container->setAlias(ProviderRegistryInterface::class, ProviderRegistry::class);
        $container->setAlias('cxxi.client_provider.registry', ProviderRegistry::class);

        $taggedClients = array_keys($container->findTaggedServiceIds('client'));
        $clientReferences = array_map(fn($serviceId) => new Reference($serviceId), $taggedClients);

        $container->setDefinition(ProviderRegistry::class, $this->createRegistryDefinition($clientReferences));

        // $container->setAlias(
        //     sprintf('%s $providerRegistry', ProviderRegistryInterface::class), 
        //     ProviderRegistry::class
        // );

        $taggedProviders = array_keys($container->findTaggedServiceIds('provider'));

        foreach($taggedProviders as $serviceId)
        {
            $serviceDefinition = $container->getDefinition($serviceId);
            $alias = substr(strrchr($serviceDefinition->getTag('provider')[0]['alias'], '.'), 1);

            $registryServiceId = sprintf('cxxi.client_provider.registry.%s', $alias);
            $container->setDefinition($registryServiceId, $this->createRegistryDefinition($clientReferences, $alias));

            $container->setAlias(
                sprintf('%s $%sProviderRegistry', ProviderRegistryInterface::class, $alias),
                $registryServiceId
            );
        }

        // AutowiringChecker::checkAppDepencencyInjection($container, ProviderRegistryInterface::class);
    }

    private function createRegistryDefinition(array $clientReferences, ?string $providerType = null): Definition
    {
        return (new Definition(ProviderRegistry::class))
            ->setArgument('$clients', $clientReferences)
            ->setArgument('$providerType', $providerType)
            ->setPublic(true)
        ;
    }
}