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
 * @author <florian@1001pharmacies.com>
 */
interface KaliProviderInterface
{
    /**
     * Execute a GET request to get sku details
     *
     * @param string $sku
     *
     * @return array
     */
    public function get($uri = null, $headers = null, $postBody = null, array $options = array());

    /**
     * Execute a POST request to create sku and return sku code
     *
     * @param string $project
     * @param string $type
     * @param int    $id
     *
     * @return array
     */
    public function post($uri = null, $headers = null, $postBody = null, array $options = array());

    /**
     * Execute a POST request to create sku and return sku code
     *
     * @param string $project
     * @param string $type
     * @param int    $id
     *
     * @return array
     */
    public function put($uri = null, $headers = null, $postBody = null, array $options = array());

    /**
     * Execute a POST request to create sku and return sku code
     *
     * @param string $project
     * @param string $type
     * @param int    $id
     *
     * @return array
     */
    public function patch($uri = null, $headers = null, $postBody = null, array $options = array());

    /**
     * Execute a DELETE request to
     *
     * @param string $sku
     *
     * @return bool
     */
    public function delete($uri = null, $headers = null, $postBody = null, array $options = array());
}
