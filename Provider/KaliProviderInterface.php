<?php
/**
 * This file is part of the Meup Kali Client Bundle.
 *
 * (c) 1001pharmacies <http://github.com/1001pharmacies/kali-client>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meup\Bundle\KaliClientBundle\Provider;

/**
 * Interface KaliProviderInterface
 *
 * @author Florian Ajir <florian@1001pharmacies.com>
 */
interface KaliProviderInterface
{
    /**
     * Generate and allocate a new sku code in registry.
     * Note: used as first step of two-step sku creation process
     *
     * @param string $project
     *
     * @return string
     */
    public function allocate($project);

    /**
     * Creates a new sku from the submitted data.
     * Note: used for one-step sku creation process
     *
     * @param string $project
     * @param string $type
     * @param string $id
     * @param string $permalink
     *
     * @return string
     */
    public function create($project, $type, $id, $permalink);

    /**
     * Removes a sku
     *
     * @param string $sku
     *
     * @return bool
     */
    public function delete($sku);

    /**
     * Get sku details from server
     *
     * @param string $sku
     *
     * @return string
     */
    public function get($sku);

    /**
     * Edit sku details on server
     * Note: used as second step of two-step sku creation process
     *
     * @param string $code
     * @param string $project
     * @param string $type
     * @param string $id
     * @param string $permalink
     *
     * @return string
     */
    public function update($code, $project, $type, $id, $permalink);
}
