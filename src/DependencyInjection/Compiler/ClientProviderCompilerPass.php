<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\DependencyInjection\Compiler;

use Cxxi\ClientProviderBundle\Contracts\ProviderInterface;
use Cxxi\ClientProviderBundle\Attribute\AsClientProvider;
use Cxxi\ClientProviderBundle\Attribute\AsProvider;
use Cxxi\ClientProviderBundle\Util\ProviderResolver;
use Cxxi\ClientProviderBundle\Util\AttributeReader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class ClientProviderCompilerPass implements CompilerPassInterface
{   
    public function process(ContainerBuilder $container): void
    {
        $clientProviderClasses = ProviderResolver::findClassesWithAttribute(AsClientProvider::class);

        foreach($clientProviderClasses as $className)
        {
            $reflector = new \ReflectionClass($className);

            if (!$container->hasDefinition($className)) {
                throw new \Exception('');
            }

            $asClientProvider = AttributeReader::get($reflector, AsClientProvider::class);

            $container->getDefinition($className)
                ->addTag(AsClientProvider::TAG, [ 'alias' => $asClientProvider->getTagAlias() ])
                ->setPublic(true)
                ->setLazy(true)
            ;

            $providerType = AttributeReader::get($reflector->getParentClass(), AsProvider::class, 'name');
            $alias = sprintf('%s $%s%sProvider', ProviderInterface::class, $asClientProvider->getName(), ucfirst($providerType));

            // sprintf('client_provider.client.%s', $asClientProvider->getName())

            // $container->setAlias($alias, $className);
            $container->setAlias($asClientProvider->getServiceId(), $className);
            $container->setAlias($alias, sprintf('cxxi.client_provider.client.%s', $asClientProvider->getName()));
        }

        // AutowiringChecker::checkAppDependencyInjection($container, ProviderInterface::class);
    }
}