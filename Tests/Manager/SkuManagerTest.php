<?php
/**
 * This file is part of the Meup Kali Client Bundle.
 *
 * (c) 1001pharmacies <http://github.com/1001pharmacies/kali-client>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meup\Bundle\KaliClientBundle\Tests\Manager;

use Meup\Bundle\KaliClientBundle\Manager\SkuManager;
use Meup\Bundle\KaliClientBundle\Model\Sku;
use Meup\Bundle\KaliClientBundle\Provider\KaliProvider;
use Meup\Bundle\KaliClientBundle\Tests\BaseTestCase;

class SkuManagerTest extends BaseTestCase
{
    public function testGetSku()
    {
        $provider = $this->getKaliProviderMock();
        $manager = new SkuManager($provider);

        $manager->get('0123456789');
    }

    public function testCreateSku()
    {
        $provider = $this->getKaliProviderMock();
        $manager = new SkuManager($provider);

        $manager->create($this->getSkuModel());
    }

    /**
     * @return Sku
     */
    private function getSkuModel()
    {
        return new Sku();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|KaliProvider
     */
    private function getKaliProviderMock()
    {
        return $this
            ->getMockBuilder('Meup\Bundle\KaliClientBundle\Provider\KaliProvider')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
}
