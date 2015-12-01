<?php
/**
 * This file is part of the Meup Kali Client Bundle.
 *
 * (c) 1001pharmacies <http://github.com/1001pharmacies/kali-client>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meup\Bundle\KaliClientBundle\Manager;

use Meup\Bundle\KaliClientBundle\Model\SkuInterface;

/**
 * SkuManagerInterface
 *
 * @author Florian Ajir <florian@1001pharmacies.com>
 * @author Loïc AMBROSINI <loic@1001pharmacies.com>
 */
interface SkuManagerInterface
{
    /**
     * @param string $sku sku code
     *
     * @return SkuInterface
     */
    public function get($sku);

    /**
     * @param SkuInterface $sku
     *
     * @return SkuInterface
     */
    public function create(SkuInterface $sku);

    /**
     * @param SkuInterface $sku
     * @param bool $returnExisting
     *
     * @return SkuInterface
     */
    public function update(SkuInterface $sku, $returnExisting = false);

    /**
     * @param string $sku
     *
     * @return bool
     */
    public function delete($sku);

    /**
     * @param string $project
     *
     * @return SkuInterface
     */
    public function allocate($project = null);

    /**
     * @param string $sku
     *
     * @return SkuInterface
     */
    public function disable($sku);
}
