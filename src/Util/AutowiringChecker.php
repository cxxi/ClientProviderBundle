<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Util;

use Cxxi\ClientProviderBundle\Exception\ProviderAutowireException;
use Cxxi\ClientProviderBundle\Contracts\ProviderRegistryInterface;
use Cxxi\ClientProviderBundle\Contracts\ProviderInterface;
use Cxxi\ClientProviderBundle\Attribute\AsProvider;
use Cxxi\ClientProviderBundle\Util\AttributeReader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class AutowiringChecker
{
    public static function checkAppDependencyInjection(ContainerBuilder $container, string $interfaceName): void
    {
        foreach ($container->getDefinitions() as $serviceId => $definition)
        {
            if (!self::isValidDefinition($definition)) continue;

            if ($constructor = (new \ReflectionClass($definition->getClass()))->getConstructor()) {
                $parameters = self::findRelatedParameters($constructor);
                self::checkParameters($parameters, $container, $serviceId, $interfaceName);
            }
        }
    }

    private static function isValidDefinition(Definition $definition): bool
    {
        return $definition->getClass() && class_exists($definition->getClass()) && !$definition->isAbstract();
    }

    private static function findRelatedParameters(\ReflectionMethod $constructor): array
    {
        return array_filter($constructor->getParameters(), function(\ReflectionParameter $parameter) {
            return $parameter->hasType() && $parameter->getType() instanceof \ReflectionNamedType;
        });
    }

    private static function checkParameters(array $parameters, ContainerBuilder $container, string $serviceId, string $interfaceName): void
    {
        foreach ($parameters as $parameter)
        {
            if ($parameter->getType()->isBuiltin() || $parameter->getType()->getName() !== $interfaceName) continue;

            $alias = sprintf('%s $%s', $interfaceName, $parameter->getName());
            
            if (!$container->hasAlias($alias) || !class_exists((string) $container->getAlias($alias))) continue;
            
            $reflector = new \ReflectionClass((string) $container->getAlias($alias));

            if ($reflector->isAbstract()) {
                $providerName = AttributeReader::get($reflector, AsProvider::class, 'name');
                throw new ProviderAutowireException($parameter->getName(), $providerName);
            }
        }
    }
}