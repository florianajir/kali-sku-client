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

use InvalidArgumentException;
use Guzzle\Http\ClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Kali manager for Kali API v1.0
 *
 * @author Florian Ajir <florian@1001pharmacies.com>
 * @author Loïc Ambrosini <loic@1001pharmacies.com>
 */
class KaliProvider implements KaliProviderInterface
{
    const GRANT_TYPE = 'client_credentials';
    const API_ENDPOINT = '/api/';

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var string
     */
    protected $server;

    /**
     * @var string
     */
    protected $publicKey;

    /**
     * @var string
     */
    protected $secretKey;

    /**
     * @var string
     */
    protected $token;

    /**
     * @param ClientInterface $client
     * @param LoggerInterface $logger
     * @param string          $server               kali host
     * @param string          $publicKey            public key, provided by Kali API
     * @param string          $secretKey            secret key, provided by Kali API
     * @param string|bool     $certificateAuthority bool, file path, or directory path
     */
    public function __construct(
        ClientInterface $client,
        $server,
        $publicKey,
        $secretKey,
        $certificateAuthority = false
    ) {
        $this->client = $client;
        $this->publicKey = $publicKey;
        $this->secretKey = $secretKey;
        $this->server = $server;
        $this->client->setBaseUrl($server);
        $this->client->setSslVerification($certificateAuthority);
    }

    /**
     * Authentication
     *
     * Strict Oauth2 'client_credentials' authentication.
     * Use the public and secret key to request a valid token to fetch the API.
     * This token expires after 1 hour by default, so you'll have to request another one.
     */
    private function authenticate()
    {
        $request = $this->client->get(
            sprintf(
                '/oauth/v2/token?client_id=%s&client_secret=%s&grant_type=%s',
                $this->publicKey,
                $this->secretKey,
                self::GRANT_TYPE
            )
        );
        $data = $request->send()->json();
        if (empty($data['access_token'])) {
            if ($this->logger) {
                $this->logger->error('KaliClient::authenticate');
            }
            throw new InvalidArgumentException('Failed to authenticate');
        } else {
            $this->token = $data['access_token'];
        }
    }

    /**
     * @return array
     */
    public function getAuthorizationHeader()
    {
        if (empty($this->token)) {
            $this->authenticate();
        }

        return array(
            'Authorization' => "Bearer {$this->token}",
        );
    }

    /**
     * {@inheritdoc}
     */
    public function get($uri = null, $headers = null, $postBody = null, array $options = array())
    {
        $defaultHeaders = $this->getAuthorizationHeader();

        $headers = !is_null($headers) ? array_merge($defaultHeaders, $headers) : $defaultHeaders;

        $request = $this->client->get(
            $uri,
            $headers,
            $options
        );

        return $request->send();
    }

    /**
     * {@inheritdoc}
     */
    public function post($uri = null, $headers = null, $postBody = null, array $options = array())
    {
        $defaultHeaders = $this->getAuthorizationHeader();

        $headers = !is_null($headers) ? array_merge($defaultHeaders, $headers) : $defaultHeaders;

        $request = $this->client->post(
            $uri,
            $headers,
            $postBody,
            $options
        );

        return $request->send();
    }

    /**
     * {@inheritdoc}
     */
    public function put($uri = null, $headers = null, $postBody = null, array $options = array())
    {
        $defaultHeaders = $this->getAuthorizationHeader();

        $headers = !is_null($headers) ? array_merge($defaultHeaders, $headers) : $defaultHeaders;

        $request = $this->client->put(
            $uri,
            $headers,
            $postBody,
            $options
        );

        return $request->send();
    }

    /**
     * Execute a POST request to create sku and return sku code
     *
     * @param string $project
     * @param string $type
     * @param int $id
     *
     * @return array
     */
    public function patch($uri = null, $headers = null, $postBody = null, array $options = array())
    {
        $defaultHeaders = $this->getAuthorizationHeader();

        $headers = !is_null($headers) ? array_merge($defaultHeaders, $headers) : $defaultHeaders;

        $request = $this->client->patch(
            $uri,
            $headers,
            $postBody,
            $options
        );

        return $request->send();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($uri = null, $headers = null, $postBody = null, array $options = array())
    {
        $defaultHeaders = $this->getAuthorizationHeader();

        $headers = !is_null($headers) ? array_merge($defaultHeaders, $headers) : $defaultHeaders;

        $request = $this->client->delete(
            $uri,
            $headers,
            $postBody,
            $options
        );

        return $request->send();
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
