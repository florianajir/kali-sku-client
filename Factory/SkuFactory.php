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
     * @var string
     */
    protected $appName;

    /**
     * @param string $classname
     * @param string $appName
     */
    public function __construct($classname, $appName)
    {
        $this->class = new ReflectionClass($classname);
        $this->appName = $appName;
    }

    /**
     * @return SkuInterface
     */
    public function create()
    {
        return $this->class->newInstance($this->appName);
    }
}
