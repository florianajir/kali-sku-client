<?php
/**
 * This file is part of the Meup Kali Client Bundle.
 *
 * (c) 1001pharmacies <http://github.com/1001pharmacies/kali-client>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meup\Bundle\KaliClientBundle\Tests\Model;

use Meup\Bundle\KaliClientBundle\Model\Sku;

/**
 * Class SkuTest
 *
 * @author Loïc AMBROSINI <loic@1001pharmacies.com>
 * @author Florian Ajir <florian@1001pharmacies.com>
 */
class SkuTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $sku = new Sku('test');
        $this->assertInstanceOf('Meup\Bundle\KaliClientBundle\Model\Sku', $sku);
    }

    public function testAccessors()
    {
        $sku = new Sku('test');
        $sku
            ->setCode('0123456789')
            ->setForeignId('0123456789')
            ->setForeignType('type')
            ->setPermalink('http://')
            ->setProject('project')
        ;

        $this->assertEquals('0123456789', $sku->getCode());
        $this->assertEquals('0123456789', $sku->getForeignId());
        $this->assertEquals('type', $sku->getForeignType());
        $this->assertEquals('http://', $sku->getPermalink());
        $this->assertEquals('project', $sku->getProject());
    }

    /**
     *
     */
    public function testSerialize()
    {
        $sku = new Sku('test');
        $sku
            ->setCode('0123456789')
            ->setForeignId('0123456789')
            ->setForeignType('type')
            ->setPermalink('http://')
            ->setProject('project')
            ->enable()
        ;
        $serialized = $sku->serialize();
        $serializedArray = json_decode($serialized, true);
        $this->assertEquals($serializedArray['code'], $sku->getCode());
        $this->assertEquals($serializedArray['id'], $sku->getForeignId());
        $this->assertEquals($serializedArray['type'], $sku->getForeignType());
        $this->assertEquals($serializedArray['permalink'], $sku->getPermalink());
        $this->assertEquals($serializedArray['project'], $sku->getProject());
        $this->assertTrue($sku->isActive());
    }

    public function testActivation()
    {
        $sku = new Sku('test');
        $sku->setActive(true);
        $this->assertTrue($sku->isActive());
        $sku->setActive(false);
        $this->assertFalse($sku->isActive());
        $sku->enable();
        $this->assertTrue($sku->isActive());
        $sku->disable();
        $this->assertFalse($sku->isActive());
    }

    public function testUnserializeString()
    {
        $data = <<<JSON
{
    "active": true,
    "code": "1234567"
}
JSON;

        $sku = new Sku('test');
        $result = $sku->unserialize($data);
        $this->assertInstanceOf('Meup\Bundle\KaliClientBundle\Model\Sku', $result);
        $this->assertEquals($result, $sku);
    }
}
