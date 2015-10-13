<?php
/**
 * This file is part of the Meup Kali Client Bundle.
 *
 * (c) 1001pharmacies <http://github.com/1001pharmacies/kali-client>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meup\Bundle\KaliClientBundle\Tests\DependencyInjection;

use Meup\Bundle\KaliClientBundle\DependencyInjection\MeupKaliClientExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ConfigurationTest
 *
 * @author Loïc AMBROSINI <loic@1001pharmacies.com>
 * @author Florian Ajir <florian@1001pharmacies.com>
 */
class MeupKaliClientExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MeupKaliClientExtension
     */
    private $extension;

    /**
     * Root name of the configuration
     *
     * @var string
     */
    private $root;

    /**
     * @return MeupKaliClientExtension
     */
    protected function getExtension()
    {
        return new MeupKaliClientExtension();
    }

    /**
     * @return ContainerBuilder
     */
    private function getContainer()
    {
        return new ContainerBuilder();
    }

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();

        $this->extension = $this->getExtension();
        $this->root = "meup_kali_client";
    }

    /**
     *
     */
    public function testBundleAlias()
    {
        $this->assertEquals('meup_kali_client', $this->getExtension()->getAlias());
    }

    /**
     *
     */
    public function testGetConfig()
    {
        $this->extension->load(
            array(
                $this->root => array(
                    'kali_server' => 'http://',
                    'kali_public_key' => '123',
                    'kali_secret_key' => '123'
                )
            ),
            $this->getContainer()
        );
    }
}
