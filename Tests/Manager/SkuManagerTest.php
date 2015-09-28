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

use Meup\Bundle\KaliClientBundle\Factory\SkuFactory;
use Meup\Bundle\KaliClientBundle\Manager\SkuManager;
use Meup\Bundle\KaliClientBundle\Model\Sku;
use Meup\Bundle\KaliClientBundle\Model\SkuInterface;
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
        $expectedSku = $this->getSkuModel();
        $expectedSku->setCode('0123456789');

        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('json')
            ->willReturn(
                array(
                    'code' => '0123456789'
                )
            )
        ;

        $provider = $this->getKaliProviderMock();
        $provider
            ->expects($this->any())
            ->method('get')
            ->with('/api/0123456789')
            ->willReturn($response)
        ;

        $manager = new SkuManager($provider, $this->skuFactoryMock($expectedSku));
        $sku = $manager->get('0123456789');

        $this->assertInstanceOf('Meup\Bundle\KaliClientBundle\Model\SkuInterface', $sku);
    }

    public function testGetNotFoundSku()
    {
        $expectedSku = $this->getSkuModel();
        $expectedSku->setCode('0123456789');

        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('json')
            ->willReturn(array())
        ;

        $provider = $this->getKaliProviderMock();
        $provider
            ->expects($this->any())
            ->method('get')
            ->with('/api/0123456789')
            ->willReturn($response)
        ;

        $manager = new SkuManager($provider, $this->skuFactoryMock(null));
        $sku = $manager->get('0123456789');

        $this->assertNull($sku);
    }

    public function testCreateSku()
    {
        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('json')
            ->willReturn(
                array(
                    'code' => '0123456789',
                    'project' => 'testProject',
                    'type' => 'testType',
                    'id' => 'testId'
                )
            )
        ;

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
                '/api/',
                array(),
                array(
                    'sku' => array (
                        'project' => $sku->getProject(),
                        'type' => $sku->getForeignType(),
                        'id' => $sku->getForeignId()
                    )
                )
            )
            ->willReturn($response)
        ;

        $manager = new SkuManager($provider, $this->skuFactoryMock($sku));
        $resultSku = $manager->create($sku);

        $this->assertInstanceOf('Meup\Bundle\KaliClientBundle\Model\Sku', $resultSku);

    }

    public function testUpdateSku()
    {
        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('json')
            ->willReturn(
                array(
                    'code' => '0123456789',
                    'project' => 'testProject',
                    'type' => 'testType',
                    'id' => 'testId'
                )
            )
        ;

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
                '/api/',
                array(),
                array(
                    'sku' => array (
                        'project' => $sku->getProject(),
                        'type' => $sku->getForeignType(),
                        'id' => $sku->getForeignId()
                    )
                )
            )
            ->willReturn($response)
        ;

        $manager = new SkuManager($provider, $this->skuFactoryMock($sku));
        $resultSku = $manager->update($sku);

        $this->assertInstanceOf('Meup\Bundle\KaliClientBundle\Model\Sku', $resultSku);

    }


    public function testDeleteSku()
    {
        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('json')
            ->willReturn(array())
        ;

        $sku = "0123456789";

        $provider = $this->getKaliProviderMock();
        $provider
            ->expects($this->any())
            ->method('delete')
            ->with(
                '/api/' . $sku
            )
            ->willReturn($response)
        ;

        $manager = new SkuManager($provider, $this->skuFactoryMock(null));
        $result = $manager->delete($sku);

        $this->assertNull($result);

    }

    public function testISku()
    {
        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('json')
            ->willReturn(
                array(
                    'code' => '0123456789',
                    'project' => 'testProject',
                    'type' => 'testType',
                    'id' => 'testId'
                )
            )
        ;

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
                '/api/',
                array(),
                array(
                    'sku' => array (
                        'project' => $sku->getProject(),
                        'type' => $sku->getForeignType(),
                        'id' => $sku->getForeignId()
                    )
                )
            )
            ->willReturn($response)
        ;

        $manager = new SkuManager($provider, $this->skuFactoryMock($sku));
        $resultSku = $manager->create($sku);

        $this->assertInstanceOf('Meup\Bundle\KaliClientBundle\Model\Sku', $resultSku);

    }

    private function getResponseMock()
    {
        return $this
            ->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    /**
     * @param SkuInterface $sku
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|SkuFactory
     */
    private function skuFactoryMock($sku)
    {
        $factory = $this
            ->getMockBuilder('Meup\Bundle\KaliClientBundle\Factory\SkuFactory')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $factory
            ->expects($this->any())
            ->method('create')
            ->willReturn($sku)
        ;

        return $factory;
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
