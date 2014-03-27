<?php

namespace Malwarebytes\TestBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('testbundle');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $rootNode
            ->children()
                ->scalarNode('doctrine_migration_test_driver')
                    ->defaultValue('DropMigrate')
                    ->info('Configures which driver')
                    ->example('DropMigrate | Transactions')
                ->end()
                ->booleanNode('force_different_test_db')
                    ->defaultTrue()
                    ->info('Enforces a check to make sure tests utilize a different DB than prod or dev.')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
