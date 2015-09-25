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
use Meup\Bundle\KaliClientBundle\Provider\KaliProvider;
use Meup\Bundle\KaliClientBundle\Provider\KaliProviderInterface;

/**
 * Sku manager
 *
 * @author Florian Ajir <florian@1001pharmacies.com>
 */
class SkuManager implements SkuManagerInterface
{
    /**
     * @var KaliProviderInterface
     */
    private $provider;

    /**
     * SkuManager constructor.
     *
     * @param KaliProviderInterface $provider
     */
    public function __construct(KaliProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param string $sku sku code
     *
     * @return SkuInterface
     */
    public function get($sku)
    {
        return $this
            ->provider
            ->get(
                KaliProvider::API_ENDPOINT . '/' . $sku
            )
        ;
    }

    /**
     * @param SkuInterface $sku
     *
     * @return SkuInterface
     */
    public function create(SkuInterface $sku)
    {
        return $this
            ->provider
            ->post(
                KaliProvider::API_ENDPOINT . '/',
                array(),
                array(
                    'project' => $sku->getProject(),
                    'type' => $sku->getForeignType(),
                    'id' => $sku->getForeignId()
                )
            )
        ;
    }

    /**
     * @param SkuInterface $sku
     *
     * @return SkuInterface
     */
    public function update(SkuInterface $sku)
    {
        return $this
            ->provider
            ->put(
                KaliProvider::API_ENDPOINT . '/' . $sku->getCode(),
                array(),
                array(
                    'project' => $sku->getProject(),
                    'type' => $sku->getForeignType(),
                    'id' => $sku->getForeignId()
                )
            )
        ;
    }

    /**
     * @param SkuInterface $sku
     *
     * @return SkuInterface
     */
    public function delete(SkuInterface $sku)
    {
        return $this
            ->provider
            ->delete(
                KaliProvider::API_ENDPOINT . '/' . $sku->getCode()
            )
        ;
    }
}
