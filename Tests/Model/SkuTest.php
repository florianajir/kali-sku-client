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
use Meup\Bundle\KaliClientBundle\Tests\BaseTestCase;

class SkuTest extends BaseTestCase
{
    public function testConstructor()
    {
        $Sku = new Sku();
        $this->assertInstanceOf('Meup\Bundle\KaliClientBundle\Model\Sku', $Sku);
    }

    public function testAccessors()
    {
        $Sku = new Sku();
        $Sku
            ->setCode('0123456789')
            ->setForeignId('0123456789')
            ->setforeignType('type')
            ->setPermalink('http://')
            ->setProject('project')
        ;

        $this->assertEquals('0123456789', $Sku->getCode());
        $this->assertEquals('0123456789', $Sku->getForeignId());
        $this->assertEquals('type', $Sku->getForeignType());
        $this->assertEquals('http://', $Sku->getPermalink());
        $this->assertEquals('project', $Sku->getProject());
    }
}
