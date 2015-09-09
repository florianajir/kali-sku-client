<?php
namespace Meup\Bundle\KaliClientBundle\Manager;

use Meup\Bundle\KaliClientBundle\Model\SkuInterface;

/**
 * SkuManagerInterface
 *
 * @author Florian Ajir <florian@1001pharmacies.com>
 */
interface SkuManagerInterface
{
    /**
     * @param string $sku
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
}
