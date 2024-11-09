<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Tests;

use Cxxi\ClientProviderBundle\ClientProviderBundle;
use Cxxi\ClientProviderBundle\DependencyInjection\ClientProviderCompilerPass;
use Cxxi\ClientProviderBundle\DependencyInjection\ProviderCompilerPass;
use Cxxi\ClientProviderBundle\DependencyInjection\RegistryCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use PHPUnit\Framework\TestCase;

class ClientProviderBundleTest extends TestCase
{
    public function testBuildAddsCompilerPasses()
    {
        $container = $this->createMock(ContainerBuilder::class);

        $calls = [];

        $container->expects($this->exactly(3))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(CompilerPassInterface::class))
            ->willReturnCallback(function($compilerPass) use (&$calls, $container) {
                $calls[] = $compilerPass;
                return $container;
            })
        ;

        $bundle = new ClientProviderBundle();
        $bundle->build($container);

        $this->assertCount(3, $calls);

        $this->assertInstanceOf(ClientProviderCompilerPass::class, $calls[0]);
        $this->assertInstanceOf(ProviderCompilerPass::class, $calls[1]);
        $this->assertInstanceOf(RegistryCompilerPass::class, $calls[2]);
    }
}