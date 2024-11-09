<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle;

use Cxxi\ClientProviderBundle\DependencyInjection\Compiler;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

/**
 * @author Louis-Antoine Lumet <contact@cxxi.io>
 */
class ClientProviderBundle extends AbstractBundle
{
    public const VERSION = '1.0.0';
    
    public function build(ContainerBuilder $container)
    {        
        parent::build($container);

        $container->addCompilerPass(
            new Compiler\MakerCompilerPass(),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            100
        );

        $container->addCompilerPass(
            new Compiler\ClientProviderCompilerPass(),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            30
        );

        $container->addCompilerPass(
            new Compiler\ProviderCompilerPass(),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            20
        );

        $container->addCompilerPass(
            new Compiler\RegistryCompilerPass(),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            10
        );
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import('DependencyInjection/Configuration.php');
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {    
        // dd($config);
        // the "$config" variable is already merged and processed so you can
        // use it directly to configure the service container (when defining an
        // extension class, you also have to do this merging and processing)
        // $container->services()
        //     ->get('acme_social.twitter_client')
        //     ->arg(0, $config['twitter']['client_id'])
        //     ->arg(1, $config['twitter']['client_secret'])
        // ;

        // // load an XML, PHP or YAML file
        // $container->import('../config/services.xml');

        // // you can also add or replace parameters and services
        // $container->parameters()
        //     ->set('acme_hello.phrase', $config['phrase'])
        // ;

        // if ($config['scream']) {
        //     $container->services()
        //         ->get('acme_hello.printer')
        //             ->class(ScreamingPrinter::class)
        //     ;
        // }
    }
}