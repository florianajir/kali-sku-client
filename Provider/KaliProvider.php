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

use Exception;
use GuzzleHttp\Client;
use InvalidArgumentException;
use Meup\Bundle\KaliClientBundle\Util\Codes;
use Psr\Log\LoggerInterface;

/**
 * Kali manager for Kali API
 *
 * @author Florian Ajir <florian@1001pharmacies.com>
 */
class KaliProvider implements KaliProviderInterface
{
    const API_ENDPOINT = 'api';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var KaliAuthenticatorInterface
     */
    protected $authenticator;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Client $client Guzzle http client
     * @param KaliAuthenticatorInterface $authenticator kali authenticator service
     */
    public function __construct(Client $client, KaliAuthenticatorInterface $authenticator)
    {
        $this->client = $client;
        $this->authenticator = $authenticator;
    }

    /**
     * Set the logger
     *
     * @param LoggerInterface $logger
     *
     * @return self
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Get sku details from server
     *
     * @param string $sku
     *
     * @return string|false|null
     * @throws Exception
     */
    public function get($sku)
    {
        if ($this->logger) {
            $this->logger->debug("KaliProvider::get($sku)");
        }
        $uri = sprintf(
            '%s/%s',
            self::API_ENDPOINT,
            $sku
        );
        $response = $this->fetch('GET', $uri);
        switch ($response->getStatusCode()) {
            case Codes::HTTP_OK:
                break;
            case Codes::HTTP_GONE:
                if ($this->logger) {
                    $this->logger->notice('Sku is gone.');
                }

                return false;
            case Codes::HTTP_NOT_FOUND:
                return null;
            default:
                if ($this->logger) {
                    $this->logger->error(
                        'Response status code not expected.',
                        array(
                            'method' => 'get',
                            'status' => $response->getStatusCode(),
                            'reason' => $response->getReasonPhrase(),
                            'sku' => $sku
                        )
                    );
                }
                throw new Exception('Kali response status code not expected.', $response->getStatusCode());
        }

        return $response->getBody()->getContents();
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function fetch($method, $uri = null, array $options = [])
    {
        if (empty($options['headers']['Authorization'])) {
            $options['headers']['Authorization'] = $this->getAuthorizationHeader();
        }
        $response = $this->client->request($method, $uri, $options);
        switch ($response->getStatusCode()) {
            case Codes::HTTP_UNAUTHORIZED:
                if ($this->logger) {
                    $this->logger->notice('Token expired, requesting for a new one.');
                }
                $options['headers']['Authorization'] = $this->getAuthorizationHeader(true);
                $response = $this->client->request($method, $uri, $options);
                break;
            case Codes::HTTP_NOT_FOUND:
                if ($this->logger) {
                    $this->logger->warning('Sku not found.');
                }
                break;
        }

        return $response;
    }

    /**
     * Return Authorization header value (Bearer token)
     *
     * @param bool|false $forceReauth if set to true, it will request for a new token although it is already available
     *
     * @return string
     */
    private function getAuthorizationHeader($forceReauth = false)
    {
        if ($forceReauth || null === $this->authenticator->getToken()) {
            $this->authenticator->authenticate();
        }
        return "Bearer {$this->authenticator->getToken()}";
    }

    /**
     * Generate and allocate a new sku code in registry.
     * Note: used as first step of two-step sku creation process
     *
     * @param string $project
     *
     * @return string
     * @throws Exception
     */
    public function allocate($project)
    {
        if ($this->logger) {
            $this->logger->debug("KaliProvider::allocate($project)");
        }
        if (empty($project)) {
            if ($this->logger) {
                $this->logger->critical('Invalid project parameter');
            }
            throw new InvalidArgumentException('project parameter must be set');
        }
        $uri = sprintf(
            '%s/%s',
            self::API_ENDPOINT,
            $project
        );
        $response = $this->fetch('POST', $uri);
        if ($response->getStatusCode() !== Codes::HTTP_CREATED) {
            if ($this->logger) {
                $this->logger->error(
                    'Error during sku allocation.',
                    array(
                        'method' => 'post',
                        'status' => $response->getStatusCode(),
                        'reason' => $response->getReasonPhrase(),
                    )
                );
            }
            throw new Exception('Kali response status code not expected.', $response->getStatusCode());
        }

        return $response->getBody()->getContents();
    }

    /**
     * Creates a new sku from the submitted data.
     * Note: used for one-step sku creation process
     *
     * @param string $project
     * @param string $objectType
     * @param string $objectId
     * @param string $permalink
     *
     * @return string
     * @throws Exception
     */
    public function create($project, $objectType, $objectId, $permalink = null)
    {
        if ($this->logger) {
            $this->logger->debug("KaliProvider::create($project, $objectType, $objectId, $permalink)");
        }
        $data = $this->prepareSkuData($project, $objectType, $objectId, $permalink);
        $response = $this->fetch(
            'POST',
            self::API_ENDPOINT . '/',
            array(
                'json' => $data
            )
        );
        switch ($response->getStatusCode()) {
            case Codes::HTTP_CREATED:
                break;
            case Codes::HTTP_OK:
                if ($this->logger) {
                    $this->logger->warning('Existing sku in registry.');
                }
                break;
            case Codes::HTTP_BAD_REQUEST:
                if ($this->logger) {
                    $this->logger->error('Sku creation failed due to form error.', $data);
                }
                throw new InvalidArgumentException(
                    'Sku creation failed due to form error.',
                    $response->getStatusCode()
                );
            default:
                if ($this->logger) {
                    $this->logger->error(
                        'Response status code not expected.',
                        array(
                            'method' => 'post',
                            'status' => $response->getStatusCode(),
                            'message' => $response->getReasonPhrase(),
                            'data' => $data,
                            'response' => $response->getBody()->getContents()
                        )
                    );
                }
                throw new Exception('Kali response status code not expected.', $response->getStatusCode());
        }

        return $response->getBody()->getContents();
    }

    /**
     * Removes a sku
     *
     * @param string $sku
     *
     * @return bool
     * @throws Exception
     */
    public function delete($sku)
    {
        if ($this->logger) {
            $this->logger->debug("KaliProvider::delete($sku)");
        }
        $uri = sprintf(
            '%s/%s',
            self::API_ENDPOINT,
            $sku
        );
        $response = $this->fetch(
            'DELETE',
            $uri
        );
        switch ($response->getStatusCode()) {
            case Codes::HTTP_NO_CONTENT:
                break;
            case Codes::HTTP_NOT_FOUND:
                return false;
            default:
                if ($this->logger) {
                    $this->logger->error(
                        'Response status code not expected.',
                        array(
                            'method' => 'post',
                            'status' => $response->getStatusCode(),
                            'message' => $response->getReasonPhrase(),
                            'sku' => $sku,
                            'response' => $response->getBody()->getContents()
                        )
                    );
                }
                throw new Exception('Kali response status code not expected.', $response->getStatusCode());
        }

        return true;
    }

    /**
     * Disables a sku
     *
     * @param string $sku
     *
     * @return bool
     * @throws InvalidArgumentException no sku exception
     * @throws Exception invalid server response exception
     */
    public function disable($sku)
    {
        if ($this->logger) {
            $this->logger->debug("KaliProvider::disable($sku)");
        }
        $uri = sprintf(
            '%s/disable/%s',
            self::API_ENDPOINT,
            $sku
        );
        $response = $this->fetch('PUT', $uri);
        switch ($response->getStatusCode()) {
            case Codes::HTTP_OK:
                break;
            case Codes::HTTP_NOT_FOUND:
                return false;
            default:
                if ($this->logger) {
                    $this->logger->error(
                        'Response status code not expected.',
                        array(
                            'method' => 'post',
                            'status' => $response->getStatusCode(),
                            'message' => $response->getReasonPhrase(),
                            'sku' => $sku,
                            'response' => $response->getBody()->getContents()
                        )
                    );
                }
                throw new Exception('Kali response status code not expected.', $response->getStatusCode());
        }

        return $response->getBody()->getContents();
    }

    /**
     * Edit sku details on server
     * Note: used as second step of two-step sku creation process
     *
     * @param string $sku
     * @param string $project
     * @param string $objectType
     * @param string $objectId
     * @param string $permalink
     *
     * @return string
     * @throws Exception
     */
    public function update($sku, $project, $objectType, $objectId, $permalink)
    {
        if ($this->logger) {
            $this->logger->debug("KaliProvider::update($sku, $project, $objectType, $objectId, $permalink)");
        }
        $data = $this->prepareSkuData($project, $objectType, $objectId, $permalink);
        $response = $this->fetch(
            'PUT',
            sprintf(
                '%s/%s',
                self::API_ENDPOINT,
                $sku
            ),
            array(
                'json' => $data
            )
        );
        $content = $response->getBody()->getContents();
        switch ($response->getStatusCode()) {
            case Codes::HTTP_OK:
                break;
            case Codes::HTTP_CONFLICT:
                if ($this->logger) {
                    $this->logger->warning(
                        'Sku update failed due to existing resource on server.',
                        array(
                            'existing' => $content
                        )
                    );
                }
                break;
            case Codes::HTTP_NOT_FOUND:
                return false;
            case Codes::HTTP_BAD_REQUEST:
                if ($this->logger) {
                    $this->logger->error('Sku update failed due to form error.', $data);
                }
                throw new InvalidArgumentException('Sku update failed due to form error.', $response->getStatusCode());
            default:
                if ($this->logger) {
                    $this->logger->error(
                        'Response status code not expected.',
                        array(
                            'method' => 'put',
                            'status' => $response->getStatusCode(),
                            'message' => $response->getReasonPhrase(),
                            'data' => $data,
                            'response' => $content
                        )
                    );
                }
                throw new Exception('Kali response status code not expected.', $response->getStatusCode());
        }

        return $content;
    }

    /**
     * Prepare SKU array data from a list of parameter
     *
     * @param string $project
     * @param string $objectType
     * @param string $objectId
     * @param string $permalink
     *
     * @return array
     */
    private function prepareSkuData($project, $objectType, $objectId, $permalink)
    {
        return array(
            'sku' => array(
                'project' => $project,
                'type' => $objectType,
                'id' => $objectId,
                'permalink' => $permalink,
            )
        );
    }
}
