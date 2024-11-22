<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Tests;

use Cxxi\ClientProviderBundle\ClientProviderBundle;
use Cxxi\ClientProviderBundle\DependencyInjection\Compiler\ClientProviderCompilerPass;
use Cxxi\ClientProviderBundle\DependencyInjection\Compiler\ProviderCompilerPass;
use Cxxi\ClientProviderBundle\DependencyInjection\Compiler\RegistryCompilerPass;
use Cxxi\ClientProviderBundle\DependencyInjection\Compiler\MakerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use PHPUnit\Framework\TestCase;

class ClientProviderBundleTest extends TestCase
{
    public function testBuildAddsCompilerPasses()
    {
        $container = $this->createMock(ContainerBuilder::class);

        $calls = [];

        $container->expects($this->exactly(4))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(CompilerPassInterface::class))
            ->willReturnCallback(function($compilerPass) use (&$calls, $container) {
                $calls[] = $compilerPass;
                return $container;
            })
        ;

        $bundle = new ClientProviderBundle();
        $bundle->build($container);

        $this->assertCount(4, $calls);

        $this->assertInstanceOf(MakerCompilerPass::class, $calls[0]);
        $this->assertInstanceOf(ClientProviderCompilerPass::class, $calls[1]);
        $this->assertInstanceOf(ProviderCompilerPass::class, $calls[2]);
        $this->assertInstanceOf(RegistryCompilerPass::class, $calls[3]);
    }
}