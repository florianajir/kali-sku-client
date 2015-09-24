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

/**
 * Class SkuManagerTest
 *
 * @author Loïc AMBROSINI <loic@1001pharmacies.com>
 */
class SkuManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSku()
    {
        $provider = $this->getKaliProviderMock();
        $provider
            ->expects($this->any())
            ->method('get')
            ->with('0123456789')
            ->willReturn($this->getSkuModel())
        ;

        $manager = new SkuManager($provider);
        $sku = $manager->get('0123456789');

        $this->assertInstanceOf('Meup\Bundle\KaliClientBundle\Model\Sku', $sku);
    }

    public function testGetNotFoundSku()
    {
        $provider = $this->getKaliProviderMock();
        $provider
            ->expects($this->any())
            ->method('get')
            ->with('0123456789')
            ->willReturn(null)
        ;

        $manager = new SkuManager($provider);
        $sku = $manager->get('0123456789');

        $this->assertNull($sku);
    }

    public function testCreateSku()
    {
        $sku = $this->getSkuModel();
        $sku
            ->setProject('testProject')
            ->setforeignType('testType')
            ->setForeignId('testId')
        ;

        $provider = $this->getKaliProviderMock();
        $provider
            ->expects($this->any())
            ->method('post')
            ->with(
                $sku->getProject(),
                $sku->getForeignType(),
                $sku->getForeignId()
            )
            ->willReturn($sku)
        ;

        $manager = new SkuManager($provider);
        $resultSku = $manager->create($sku);

        $this->assertInstanceOf('Meup\Bundle\KaliClientBundle\Model\Sku', $resultSku);

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
