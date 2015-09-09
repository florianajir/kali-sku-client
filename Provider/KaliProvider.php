<?php
namespace Meup\Bundle\KaliClientBundle\Provider;

use InvalidArgumentException;
use Guzzle\Http\ClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Kali manager for Kali API v1.0
 *
 * @author Florian Ajir <florian@1001pharmacies.com>
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
        LoggerInterface $logger,
        $server,
        $publicKey,
        $secretKey,
        $certificateAuthority = false
    ) {
        $this->client = $client;
        $this->logger = $logger;
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
            $this->logger->error('KaliClient::authenticate');
            throw new InvalidArgumentException('Failed to authenticate');
        } else {
            $this->token = $data['access_token'];
        }
    }

    /**
     * @return array
     */
    private function getAuthorizationHeader()
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
    public function get($sku)
    {
        $request = $this->client->get(
            self::API_ENDPOINT.$sku,
            $this->getAuthorizationHeader()
        );

        $response = $request->send();

        $this->logger->debug(
            'KaliClient::get',
            array(
                'token'    => $this->token,
                'url'      => $request->getUrl(),
                'status'   => $response->getStatusCode(),
                'response' => $response->json()
            )
        );

        if ($response->getStatusCode() === 400) {
            $this->logger->error(
                'KaliClient::get failed. Sku not found',
                array(
                    'sku' => $sku,
                )
            );
            throw new InvalidArgumentException('sku not found');
        }

        return $response->json();
    }

    /**
     * {@inheritdoc}
     */
    public function post($uri = null, $headers = null, $postBody = null, array $options = array())
    {
    }

    /**
     * {@inheritdoc}
     */
    public function delete($uri = null, $headers = null, $body = null, array $options = array())
    {
    }
}
