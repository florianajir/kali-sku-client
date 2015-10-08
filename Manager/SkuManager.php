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

use InvalidArgumentException;
use Meup\Bundle\KaliClientBundle\Factory\SkuFactory;
use Meup\Bundle\KaliClientBundle\Model\SkuInterface;
use Meup\Bundle\KaliClientBundle\Provider\KaliProviderInterface;

/**
 * Sku manager
 *
 * @author Florian Ajir <florian@1001pharmacies.com>
 * @author Loïc Ambrosini <loic@1001pharmacies.com>
 */
class SkuManager implements SkuManagerInterface
{
    /**
     * @var string
     */
    protected $appName;

    /**
     * @var KaliProviderInterface
     */
    private $provider;

    /**
     * @var SkuFactory
     */
    private $factory;

    /**
     * SkuManager constructor.
     *
     * @param KaliProviderInterface $provider
     * @param SkuFactory $factory
     * @param string $appName
     */
    public function __construct(KaliProviderInterface $provider, SkuFactory $factory, $appName = null)
    {
        $this->provider = $provider;
        $this->factory = $factory;
        $this->appName = $appName;
    }

    /**
     * @param string|null $appName
     *
     * @return SkuInterface|null
     */
    public function allocate($appName = null)
    {
        $project = $this->appName;
        if (!is_null($appName)) {
            $project = $appName;
        }
        if (is_null($project)) {
            throw new InvalidArgumentException('You must define your application name');
        }
        $data = $this->provider->allocate($project);
        if (!empty($data)) {
            $sku = $this
                ->factory
                ->create()
                ->unserialize($data);
        } else {
            $sku = null;
        }

        return $sku;
    }

    /**
     * @param string $sku sku code
     *
     * @return SkuInterface
     */
    public function get($sku)
    {
        $data = $this
            ->provider
            ->get($sku);

        if (!empty($data)) {
            $sku = $this
                ->factory
                ->create()
                ->unserialize($data);
        } else {
            $sku = null;
        }

        return $sku;
    }

    /**
     * @param SkuInterface $sku
     *
     * @return SkuInterface
     */
    public function create(SkuInterface $sku)
    {
        $data = $this
            ->provider
            ->create(
                $sku->getProject(),
                $sku->getForeignType(),
                $sku->getForeignId(),
                $sku->getPermalink()
            );

        if (!empty($data)) {
            $sku = $this
                ->factory
                ->create()
                ->unserialize($data);
        } else {
            $sku = null;
        }

        return $sku;
    }

    /**
     * @param SkuInterface $sku
     *
     * @return SkuInterface
     */
    public function update(SkuInterface $sku)
    {
        $data = $this
            ->provider
            ->update(
                $sku->getCode(),
                $sku->getProject(),
                $sku->getForeignType(),
                $sku->getForeignId(),
                $sku->getPermalink()
            );

        if (!empty($data)) {
            $sku = $this
                ->factory
                ->create()
                ->unserialize($data);
        } else {
            $sku = null;
        }

        return $sku;
    }

    /**
     * @param string $sku
     *
     * @return bool
     */
    public function delete($sku)
    {
        return $this->provider->delete($sku);
    }

    /**
     * @param string $sku
     *
     * @return SkuInterface
     */
    public function disable($sku)
    {
        $data = $this->provider->disable($sku);

        if (!empty($data)) {
            $sku = $this
                ->factory
                ->create()
                ->unserialize($data);
        } else {
            $sku = null;
        }

        return $sku;
    }
}
