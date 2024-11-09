<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\DependencyInjection\Compiler;

use Cxxi\ClientProviderBundle\Exception\ProviderDefinitionException;
use Cxxi\ClientProviderBundle\Contracts\ProviderInterface;
use Cxxi\ClientProviderBundle\Attribute\AsProvider;
use Cxxi\ClientProviderBundle\Util\ProviderResolver;
use Cxxi\ClientProviderBundle\Util\AttributeReader;
use Cxxi\ClientProviderBundle\Util\AutowiringChecker;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class ProviderCompilerPass implements CompilerPassInterface
{   
    public function process(ContainerBuilder $container)
    {
        $providerClasses = ProviderResolver::findClassesWithAttribute(AsProvider::class);

        foreach($providerClasses as $className)
        {
            $reflector = new \ReflectionClass($className);

            if (!$container->hasDefinition($className)) {
                $container->setDefinition($className, new Definition($className));

            } else if (!$reflector->isAbstract()) {
                throw new ProviderDefinitionException($className);
            }

            $asProvider = AttributeReader::get($reflector, AsProvider::class);

            $container->getDefinition($className)
                ->addTag(AsProvider::TAG, [ 'alias' => $asProvider->getTagAlias() ])
                ->setAbstract(true)
                ->setPublic(true)
                ->setLazy(true)
            ;

            $alias = sprintf('%s $%sProvider', ProviderInterface::class, $asProvider->getName());

            $container->setAlias($alias, !is_null($asProvider->getDefault())
                ? $container->getAlias(sprintf('cxxi.client_provider.client.%s', $asProvider->getDefault()))
                : $className
            );

            $container->setAlias($asProvider->getServiceId(), $className);

            // TRY TO MOUNT ENV PROPERTY
            // $container->setParameter(sprintf('client_provider.%s.default', $asProvider->getName()), $asProvider->getDefault());
        }

        AutowiringChecker::checkAppDependencyInjection($container, ProviderInterface::class);
    }
}

// $container->registerAttributeForAutoconfiguration(SensitiveElement::class, static function (ChildDefinition $definition, SensitiveElement $attribute, \ReflectionClass $reflector): void {
//     // Apply the 'app.sensitive_element' tag to all classes with SensitiveElement
//     // attribute, and attach the token value to the tag
//     $definition->addTag('app.sensitive_element', ['token' => $attribute->getToken()]);
// });