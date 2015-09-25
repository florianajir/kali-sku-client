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
use Symfony\Component\Serializer\Serializer;

/**
 * Sku manager
 *
 * @author Florian Ajir <florian@1001pharmacies.com>
 * @author Loïc Ambrosini <loic@1001pharmacies.com>
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
        $response = $this
            ->provider
            ->get(
                KaliProvider::API_ENDPOINT . '/' . $sku
            )
        ;

        return $response->json();
    }

    /**
     * @param SkuInterface $sku
     *
     * @return SkuInterface
     */
    public function create(SkuInterface $sku)
    {
        $response = $this
            ->provider
            ->post(
                KaliProvider::API_ENDPOINT . '/',
                array(),
                array(
                    'sku' => array (
                        'project' => $sku->getProject(),
                        'type' => $sku->getForeignType(),
                        'id' => $sku->getForeignId()
                    )
                )
            )
        ;

        return $response->json();
    }

    /**
     * @param SkuInterface $sku
     *
     * @return SkuInterface
     */
    public function update(SkuInterface $sku)
    {
        $response = $this
            ->provider
            ->put(
                KaliProvider::API_ENDPOINT . '/' . $sku->getCode(),
                array(),
                array(
                    'sku' => array (
                        'project' => $sku->getProject(),
                        'type' => $sku->getForeignType(),
                        'id' => $sku->getForeignId()
                    )
                )
            )
        ;

        return $response->json();
    }

    /**
     * @param SkuInterface $sku
     *
     * @return SkuInterface
     */
    public function delete(SkuInterface $sku)
    {
        $response = $this
            ->provider
            ->delete(
                KaliProvider::API_ENDPOINT . '/' . $sku->getCode()
            )
        ;

        return $response->json();
    }
}
