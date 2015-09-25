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
use Guzzle\Http\Message\Response;

/**
 * Interface KaliProviderInterface
 *
 * @author <florian@1001pharmacies.com>
 * @author Loïc Ambrosini <loic@1001pharmacies.com>
 */
interface KaliProviderInterface
{
    /**
     * Execute a GET request
     *
     * @param string  $uri
     * @param array   $headers
     * @param string  $body
     * @param array   $options
     *
     * @return Response
     */
    public function get($uri = null, $headers = null, $body = null, array $options = array());

    /**
     * Execute a POST request
     *
     * @param string  $uri
     * @param array   $headers
     * @param string  $body
     * @param array   $options
     *
     * @return Response
     */
    public function post($uri = null, $headers = null, $body = null, array $options = array());

    /**
     * Execute a PUT request
     *
     * @param string  $uri
     * @param array   $headers
     * @param string  $body
     * @param array   $options
     *
     * @return Response
     */
    public function put($uri = null, $headers = null, $body = null, array $options = array());

    /**
     * Execute a PATCH request
     *
     * @param string  $uri
     * @param array   $headers
     * @param string  $body
     * @param array   $options
     *
     * @return Response
     */
    public function patch($uri = null, $headers = null, $body = null, array $options = array());

    /**
     * Execute a DELETE request to
     *
     * @param string  $uri
     * @param array   $headers
     * @param string  $body
     * @param array   $options
     *
     * @return Response
     */
    public function delete($uri = null, $headers = null, $body = null, array $options = array());
}
