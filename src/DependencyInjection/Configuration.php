<?php

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $definition): void
{
    $definition->rootNode()
        ->children()
            ->arrayNode('providers')
                ->useAttributeAsKey('name')
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('class')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('default_client')->isRequired()->end()
                        ->arrayNode('clients')
                            ->arrayPrototype()
                                ->children()
                                    ->booleanNode('default')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end()
    ;
};