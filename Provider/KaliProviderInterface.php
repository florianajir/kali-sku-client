<?php

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
    public function get($sku);

    /**
     * Execute a POST request to create sku and return sku code
     *
     * @param string $project
     * @param string $type
     * @param int    $id
     *
     * @return array
     */
    public function post($project, $type, $id);

    /**
     * Execute a DELETE request to
     *
     * @param string $sku
     *
     * @return bool
     */
    public function delete($sku);
}
