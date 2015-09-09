<?php
namespace Meup\Bundle\KaliClientBundle\Manager;

use Meup\Bundle\KaliClientBundle\Model\SkuInterface;
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
        // TODO: Implement get() method.
    }

    /**
     * @param SkuInterface $sku
     *
     * @return SkuInterface
     */
    public function create(SkuInterface $sku)
    {
        // TODO: Implement create() method.
    }
}
