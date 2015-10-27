<?php
/**
 * This file is part of the Kali-client Project
 *
 * (c) 1001pharmacies <http://github.com/1001pharmacies/kali-client>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Meup\Bundle\KaliClientBundle\Provider;

use Exception;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

/**
 * Class KaliAuthenticator
 *
 * @author Florian Ajir <florian@1001pharmacies.com>
 */
class KaliAuthenticator implements KaliAuthenticatorInterface
{
    const GRANT_TYPE = 'client_credentials';
    const OAUTH_ENDPOINT = 'oauth/v2/token';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var LoggerInterface
     */
    protected $logger;

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
     * @param Client $client
     * @param string          $publicKey            public key, provided by Kali API
     * @param string          $secretKey            secret key, provided by Kali API
     */
    public function __construct(
        Client $client,
        $publicKey,
        $secretKey
    ) {
        $this->client = $client;
        $this->publicKey = $publicKey;
        $this->secretKey = $secretKey;
    }

    /**
     * Setter the logger
     *
     * @param LoggerInterface $logger
     *
     * @return self
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Authentification token getter
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Authentication
     *
     * Strict Oauth2 'client_credentials' authentication.
     * Use the public and secret key to request a valid token to fetch the API.
     * This token expires after 1 hour by default, so you'll have to request another one.
     *
     * @throws Exception if failed to retrieve access token
     */
    public function authenticate()
    {
        if ($this->logger) {
            $this->logger->info("KaliAuthenticator::authenticate()");
        }
        $response = $this->request();
        $json = $response->getBody()->getContents();
        $data = json_decode($json, true);
        if (!empty($data['access_token'])) {
            return $this->token = $data['access_token'];
        } else {
            if ($this->logger) {
                $this->logger->error(
                    'KaliClient::authenticate',
                    array(
                        'status' => $response->getStatusCode(),
                        'reason' => $response->getReasonPhrase(),
                        'response' => $data
                    )
                );
            }
            throw new Exception(
                'Authentication failed: ' . implode('. ', $data),
                $response->getStatusCode()
            );
        }
    }

    /**
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function request()
    {
        return $this->client->request(
            'GET',
            sprintf(
                '%s?client_id=%s&client_secret=%s&grant_type=%s',
                self::OAUTH_ENDPOINT,
                $this->publicKey,
                $this->secretKey,
                self::GRANT_TYPE
            ),
            array(
                'headers' => array(
                    'Content-Type' => 'application/x-www-form-urlencoded',
                )
            )
        );
    }
}
