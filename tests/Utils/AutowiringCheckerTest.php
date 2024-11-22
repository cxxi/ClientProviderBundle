<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Tests\Utils;

use Cxxi\ClientProviderBundle\Exception\ProviderAutowireException;
use Cxxi\ClientProviderBundle\Util\AutowiringChecker;
use Cxxi\ClientProviderBundle\Contracts\ProviderRegistryInterface;
use Cxxi\ClientProviderBundle\Contracts\ProviderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use PHPUnit\Framework\TestCase;

class AutowiringCheckerTest extends TestCase
{
    // public function testCheckAppDependencyInjectionThrowsExceptionWhenAbstractAlias(): void
    // {
    //     $container = new ContainerBuilder();

    //     // Mock for an abstract service with the alias being a ProviderInterface
    //     $serviceId = 'provider_service';
    //     $definition = new Definition();
    //     $definition->setClass(ProviderRegistryInterface::class);

    //     // Add a parameter in the constructor that expects a ProviderInterface
    //     $definition->addArgument(new Reference(ProviderInterface::class)); // Simulating constructor injection
        
    //     // Create an alias for the interface and make it abstract
    //     $container->setDefinition($serviceId, $definition);
    //     $container->setAlias(ProviderInterface::class, 'abstract_provider');

    //     // Mock the abstract class definition
    //     $abstractDefinition = new Definition();
    //     $abstractDefinition->setClass(ProviderInterface::class);
    //     $abstractDefinition->setAbstract(true);
    //     $container->setDefinition('abstract_provider', $abstractDefinition);

    //     // Check if the exception is thrown
    //     $this->expectException(ProviderAutowireException::class);
    //     $this->expectExceptionMessage("The service '$serviceId' has an invalid autowire for parameter '$provider'.");

    //     // Run the method under test
    //     AutowiringChecker::checkAppDependencyInjection($container, ProviderInterface::class);
    // }

    public function testCheckAppDependencyInjectionDoesNotThrowExceptionForValidAutowiring(): void
    {
        $container = new ContainerBuilder();

        // Mock for a valid service with a non-abstract provider
        $serviceId = 'valid_provider_service';
        $definition = new Definition();
        $definition->setClass(ProviderRegistryInterface::class);

        // Add a parameter in the constructor that expects a ProviderInterface
        $definition->addArgument(new Reference(ProviderInterface::class)); // Simulating constructor injection

        // Mock a valid ProviderInterface (non-abstract)
        $validProviderDefinition = new Definition();
        $validProviderDefinition->setClass(ProviderInterface::class);
        $container->setDefinition('valid_provider', $validProviderDefinition);

        // Add the service to the container
        $container->setDefinition($serviceId, $definition);
        $container->setAlias(ProviderInterface::class, 'valid_provider');

        // No exception should be thrown here
        AutowiringChecker::checkAppDependencyInjection($container, ProviderInterface::class);
        
        $this->assertTrue(true); // Ensure no exception is thrown
    }

    public function testCheckAppDependencyInjectionSkipsInvalidDefinitions(): void
    {
        $container = new ContainerBuilder();

        // Mock for an invalid service without the constructor matching the expected interface
        $serviceId = 'invalid_provider_service';
        $definition = new Definition();
        $definition->setClass(\stdClass::class); // Non-matching class

        // Add the definition to the container
        $container->setDefinition($serviceId, $definition);

        // The method should skip this invalid definition
        AutowiringChecker::checkAppDependencyInjection($container, ProviderInterface::class);
        
        // Assert that nothing breaks, no exception should be thrown
        $this->assertTrue(true); // Ensure no exception is thrown
    }
}