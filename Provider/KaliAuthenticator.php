<?php
/**
 * This file is part of the Kali-client Project
 *
 * (c) 1001pharmacies <http://github.com/1001pharmacies/Kali-client>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Meup\Bundle\KaliClientBundle\Provider;

use Exception;
use Guzzle\Http\ClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Class KaliAuthenticator
 *
 * @author Florian Ajir <florian@1001pharmacies.com>
 */
class KaliAuthenticator implements KaliAuthenticatorInterface
{
    const GRANT_TYPE = 'client_credentials';
    const OAUTH_ENDPOINT = '/oauth/v2/token';

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
     * @param string          $server               Kali server
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
        $this->server = $server;
        $this->publicKey = $publicKey;
        $this->secretKey = $secretKey;
        $this->client->setBaseUrl($server);
        $this->client->setSslVerification($certificateAuthority);
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
        $request = $this->client->get(
            sprintf(
                '%s?client_id=%s&client_secret=%s&grant_type=%s',
                self::OAUTH_ENDPOINT,
                $this->publicKey,
                $this->secretKey,
                self::GRANT_TYPE
            ),
            array(
                'Content-Type' => 'application/x-www-form-urlencoded',
            )
        );
        $response = $request->send();
        $data = $response->json();
        if (!empty($data['access_token'])) {
            $this->token = $data['access_token'];

            return $this->token;
        } else {
            if ($this->logger) {
                $this->logger->error(
                    'KaliClient::authenticate',
                    $data
                );
            }
            throw new Exception('No access token returned on authenticate');
        }
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
}
