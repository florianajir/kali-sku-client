<?php
/**
 * This file is part of the Meup Kali Client Bundle.
 *
 * (c) 1001pharmacies <http://github.com/1001pharmacies/kali-client>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meup\Bundle\KaliClientBundle\Factory;

use Meup\Bundle\KaliClientBundle\Model\SkuInterface;
use ReflectionClass;

/**
 * Class SkuFactory
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class SkuFactory
{
    /**
     * @var ReflectionClass
     */
    protected $class;

    /**
     * @param string $classname
     */
    public function __construct($classname)
    {
        $this->class = new ReflectionClass($classname);
    }

    /**
     * @return SkuInterface
     */
    public function create()
    {
        return $this->class->newInstance();
    }
}
