<?php

namespace App\AdminBundle\DependancyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('my_custom_bundle');
        
        $rootNode = $treeBuilder->getRootNode();
        // Define configuration options here
        $rootNode
            ->children()
                ->scalarNode('option')->defaultValue('default_value')->end()
            ->end();
        return $treeBuilder;
    }
}
