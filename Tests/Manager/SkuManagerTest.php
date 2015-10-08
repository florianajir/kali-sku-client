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
use Meup\Bundle\KaliClientBundle\Provider\KaliProvider;
use PHPUnit_Framework_TestCase;

/**
 * Class SkuManagerTest
 *
 * @author Florian Ajir <florian@1001pharmacies.com>
 */
class SkuManagerTest extends PHPUnit_Framework_TestCase
{
    public function testAllocateWithoutProjectName()
    {
        $this->setExpectedException('InvalidArgumentException');
        $provider = $this->getKaliProviderMock();
        $factory = $this->getSkuFactoryMock();
        $manager = new SkuManager($provider, $factory);
        $manager->allocate();
    }

    /**
     *
     */
    public function testAllocateWithProjectNameInConstructor()
    {
        $code = '1234567';
        $appName = 'app_name';
        $provider = $this->getKaliProviderMock();
        $provider
            ->expects($this->once())
            ->method('allocate')
            ->with($appName)
            ->willReturn(
                array(
                    'project' => $appName,
                    'code' => $code
                )
            );
        $factory = $this->getSkuFactoryMock();
        $factory
            ->expects($this->once())
            ->method('create')
            ->willReturn(new Sku());
        $manager = new SkuManager($provider, $factory, $appName);
        $data = $manager->allocate();
        $this->assertNotNull($data);
        $this->assertInstanceOf('Meup\Bundle\KaliClientBundle\Model\SkuInterface', $data);
        $this->assertEquals($code, $data->getCode());
        $this->assertEquals($appName, $data->getProject());
    }

    /**
     *
     */
    public function testAllocateWithProjectNameInParam()
    {
        $appName = 'app_name';
        $provider = $this->getKaliProviderMock();
        $provider
            ->expects($this->once())
            ->method('allocate')
            ->with($appName)
            ->willReturn(
                array(
                    'project' => $appName,
                    'code' => '1234567'
                )
            );
        $factory = $this->getSkuFactoryMock();
        $factory
            ->expects($this->once())
            ->method('create')
            ->willReturn(new Sku());
        $manager = new SkuManager($provider, $factory);
        $data = $manager->allocate($appName);
        $this->assertEquals($appName, $data->getProject());
    }

    /**
     *
     */
    public function testAllocateFailed()
    {
        $provider = $this->getKaliProviderMock();
        $provider
            ->expects($this->once())
            ->method('allocate')
            ->willReturn(null);
        $factory = $this->getSkuFactoryMock();
        $manager = new SkuManager($provider, $factory, 'app_name');
        $data = $manager->allocate();
        $this->assertNull($data);
    }

    /**
     * Test get sku succeed
     */
    public function testGetSku()
    {
        $code = '1234567';
        $provider = $this->getKaliProviderMock();
        $provider
            ->expects($this->once())
            ->method('get')
            ->with($code)
            ->willReturn(
                array(
                    'code' => $code
                )
            );
        $factory = $this->getSkuFactoryMock();
        $factory
            ->expects($this->once())
            ->method('create')
            ->willReturn(new Sku());
        $manager = new SkuManager($provider, $factory, 'app_name');
        $result = $manager->get($code);
        $this->assertInstanceOf('Meup\Bundle\KaliClientBundle\Model\SkuInterface', $result);
        $this->assertEquals($code, $result->getCode());
    }

    /**
     * Test get sku not found
     */
    public function testGetNotFoundSku()
    {
        $code = '1234567';
        $provider = $this->getKaliProviderMock();
        $provider
            ->expects($this->once())
            ->method('get')
            ->with($code)
            ->willReturn(null);
        $factory = $this->getSkuFactoryMock();
        $manager = new SkuManager($provider, $factory, 'app_name');
        $result = $manager->get($code);
        $this->assertNull($result);
    }

    /**
     *
     */
    public function testCreateSku()
    {
        $project = 'app_name';
        $type = 'product';
        $id = 1;
        $permalink = 'url';
        $code = '1234567';
        $sku = new Sku();
        $sku
            ->setProject($project)
            ->setForeignType($type)
            ->setForeignId($id)
            ->setPermalink($permalink);
        $provider = $this->getKaliProviderMock();
        $provider
            ->expects($this->once())
            ->method('create')
            ->with($project, $type, $id, $permalink)
            ->willReturn(
                array(
                    'project' => $project,
                    'type' => $type,
                    'id' => $id,
                    'permalink' => $permalink,
                    'code' => $code
                )
            );
        $factory = $this->getSkuFactoryMock();
        $factory
            ->expects($this->once())
            ->method('create')
            ->willReturn(new Sku());
        $manager = new SkuManager($provider, $factory, 'app_name');
        $result = $manager->create($sku);
        $this->assertInstanceOf('Meup\Bundle\KaliClientBundle\Model\Sku', $result);
        $this->assertEquals($project, $result->getProject());
        $this->assertEquals($type, $result->getForeignType());
        $this->assertEquals($id, $result->getForeignId());
        $this->assertEquals($permalink, $result->getPermalink());
        $this->assertEquals($code, $result->getCode());
    }

    /**
     *
     */
    public function testCreateSkuWithoutResponse()
    {
        $project = 'app_name';
        $sku = new Sku();
        $sku->setProject($project);
        $provider = $this->getKaliProviderMock();
        $provider
            ->expects($this->once())
            ->method('create')
            ->with($project, null, null, null)
            ->willReturn(null);
        $factory = $this->getSkuFactoryMock();
        $manager = new SkuManager($provider, $factory, 'app_name');
        $result = $manager->create($sku);
        $this->assertNull($result);
    }

    /**
     *
     */
    public function testUpdateSku()
    {
        $project = 'app_name';
        $type = 'product';
        $id = 1;
        $permalink = 'url';
        $code = '1234567';
        $sku = new Sku();
        $sku
            ->setCode($code)
            ->setProject($project)
            ->setForeignType($type)
            ->setForeignId($id)
            ->setPermalink($permalink);
        $provider = $this->getKaliProviderMock();
        $provider
            ->expects($this->once())
            ->method('update')
            ->with($code, $project, $type, $id, $permalink)
            ->willReturn(
                array(
                    'project' => $project,
                    'type' => $type,
                    'id' => $id,
                    'permalink' => $permalink,
                    'code' => $code
                )
            );
        $factory = $this->getSkuFactoryMock();
        $factory
            ->expects($this->once())
            ->method('create')
            ->willReturn(new Sku());
        $manager = new SkuManager($provider, $factory, 'app_name');
        $result = $manager->update($sku);
        $this->assertInstanceOf('Meup\Bundle\KaliClientBundle\Model\Sku', $result);
        $this->assertEquals($project, $result->getProject());
        $this->assertEquals($type, $result->getForeignType());
        $this->assertEquals($id, $result->getForeignId());
        $this->assertEquals($permalink, $result->getPermalink());
        $this->assertEquals($code, $result->getCode());
    }

    /**
     *
     */
    public function testUpdateSkuWithError()
    {
        $project = 'app_name';
        $type = 'product';
        $id = 1;
        $permalink = 'url';
        $code = '1234567';
        $sku = new Sku();
        $sku
            ->setCode($code)
            ->setProject($project)
            ->setForeignType($type)
            ->setForeignId($id)
            ->setPermalink($permalink);
        $provider = $this->getKaliProviderMock();
        $provider
            ->expects($this->once())
            ->method('update')
            ->with($code, $project, $type, $id, $permalink)
            ->willReturn(null);
        $factory = $this->getSkuFactoryMock();
        $manager = new SkuManager($provider, $factory, 'app_name');
        $result = $manager->update($sku);
        $this->assertNull($result);
    }

    /**
     *
     */
    public function testDeleteSkuSucceed()
    {
        $provider = $this->getKaliProviderMock();
        $provider
            ->expects($this->once())
            ->method('delete')
            ->willReturn(true);
        $factory = $this->getSkuFactoryMock();
        $manager = new SkuManager($provider, $factory, 'app_name');
        $result = $manager->delete('1234567');
        $this->assertTrue($result);
    }

    /**
     *
     */
    public function testNotDeletedSku()
    {
        $provider = $this->getKaliProviderMock();
        $provider
            ->expects($this->once())
            ->method('delete')
            ->willReturn(false);
        $factory = $this->getSkuFactoryMock();
        $manager = new SkuManager($provider, $factory, 'app_name');
        $result = $manager->delete('1234567');
        $this->assertFalse($result);
    }

    /**
     *
     */
    public function testDesactivateSku()
    {
        $code = '1234567';
        $provider = $this->getKaliProviderMock();
        $provider
            ->expects($this->once())
            ->method('desactivate')
            ->willReturn(
                array(
                    'code' => $code,
                    'active' => false
                )
            );
        $factory = $this->getSkuFactoryMock();
        $factory
            ->expects($this->once())
            ->method('create')
            ->willReturn(new Sku());
        $manager = new SkuManager($provider, $factory, 'app_name');
        $result = $manager->desactivate($code);
        $this->assertInstanceOf('Meup\Bundle\KaliClientBundle\Model\Sku', $result);
        $this->assertFalse($result->isActive());
        $this->assertEquals($code, $result->getCode());
    }

    /**
     *
     */
    public function testNotDesactivatedSku()
    {
        $provider = $this->getKaliProviderMock();
        $provider
            ->expects($this->once())
            ->method('desactivate')
            ->willReturn(null);
        $factory = $this->getSkuFactoryMock();
        $manager = new SkuManager($provider, $factory, 'app_name');
        $result = $manager->desactivate('1234567');
        $this->assertNull($result);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|KaliProvider
     */
    private function getKaliProviderMock()
    {
        return $this
            ->getMockBuilder('Meup\Bundle\KaliClientBundle\Provider\KaliProvider')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|SkuFactory
     */
    private function getSkuFactoryMock()
    {
        return $this
            ->getMockBuilder('Meup\Bundle\KaliClientBundle\Factory\SkuFactory')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
