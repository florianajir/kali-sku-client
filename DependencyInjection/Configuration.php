<?php
/**
 * This file is part of the Meup Kali Client Bundle.
 *
 * (c) 1001pharmacies <http://github.com/1001pharmacies/kali-client>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meup\Bundle\KaliClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @author Loïc AMBROSINI <loic@1001pharmacies.com>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('meup_kali_client');

        $rootNode
            ->children()
                ->scalarNode('kali_server')
                    ->isRequired()
                    ->defaultValue('')
                ->end()
                ->scalarNode('kali_public_key')
                    ->isRequired()
                    ->defaultValue('')
                ->end()
                ->scalarNode('kali_secret_key')
                    ->isRequired()
                    ->defaultValue('')
                ->end()
                ->scalarNode('kali_ssl_cert')
                    ->defaultValue('false')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
