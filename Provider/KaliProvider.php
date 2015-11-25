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
     * @var string
     */
    protected $token;

    /**
     * @param Client                     $client        Guzzle http client
     * @param KaliAuthenticatorInterface $authenticator kali authenticator service
     */
    public function __construct(
        Client $client,
        KaliAuthenticatorInterface $authenticator
    ) {
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
        $response = $this->client->request('GET', $uri, ['headers' => $this->getDefaultHeaders()]);
        switch ($response->getStatusCode()) {
            case Codes::HTTP_OK:
                break;
            case Codes::HTTP_GONE:
                if ($this->logger) {
                    $this->logger->notice('Sku is gone.');
                }

                return false;
            case Codes::HTTP_NOT_FOUND:
                if ($this->logger) {
                    $this->logger->warning('Sku not found.');
                }

                return null;
            default:
                if ($this->logger) {
                    $this->logger->error(
                        'Response status code not expected.',
                        array(
                            'method'  => 'get',
                            'status'  => $response->getStatusCode(),
                            'reason' => $response->getReasonPhrase(),
                            'sku'     => $sku
                        )
                    );
                }
                throw new Exception('Kali response status code not expected.', $response->getStatusCode());
        }

        return $response->getBody()->getContents();
    }

    /**
     * @param bool|true $authenticate
     *
     * @return array
     */
    private function getDefaultHeaders($authenticate = true)
    {
        $headers = array();
        if (true === $authenticate) {
            $token = $this->authenticator->authenticate();
            $headers['Authorization'] = "Bearer $token";
        }

        return $headers;
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
        $response = $this->client->request('POST', $uri, array('headers' => $this->getDefaultHeaders()));
        if ($response->getStatusCode() !== Codes::HTTP_CREATED) {
            if ($this->logger) {
                $this->logger->error(
                    'Error during sku allocation.',
                    array(
                        'method'  => 'post',
                        'status'  => $response->getStatusCode(),
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
     * @param string $type
     * @param string $id
     * @param string $permalink
     *
     * @return string
     * @throws Exception
     */
    public function create($project, $type, $id, $permalink = null)
    {
        if ($this->logger) {
            $this->logger->debug("KaliProvider::create($project, $type, $id, $permalink)");
        }
        $data = array(
            'sku' => array(
                'project'   => $project,
                'type'      => $type,
                'id'        => $id,
                'permalink' => $permalink,
            )
        );
        $response = $this->client->request(
            'POST',
            self::API_ENDPOINT . '/',
            array(
                'headers' => $this->getDefaultHeaders(),
                'json'    => $data
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
                            'method'   => 'post',
                            'status'   => $response->getStatusCode(),
                            'message'  => $response->getReasonPhrase(),
                            'data'     => $data,
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
        $response = $this->client->request(
            'DELETE',
            $uri,
            [
                'headers' => $this->getDefaultHeaders()
            ]
        );
        switch ($response->getStatusCode()) {
            case Codes::HTTP_NO_CONTENT:
                break;
            case Codes::HTTP_NOT_FOUND:
                if ($this->logger) {
                    $this->logger->warning('Sku not found.');
                }

                return false;
            default:
                if ($this->logger) {
                    $this->logger->error(
                        'Response status code not expected.',
                        array(
                            'method'   => 'post',
                            'status'   => $response->getStatusCode(),
                            'message'  => $response->getReasonPhrase(),
                            'sku'      => $sku,
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
        $response = $this->client->request(
            'PUT',
            $uri,
            array(
                'headers' => $this->getDefaultHeaders()
            )
        );
        switch ($response->getStatusCode()) {
            case Codes::HTTP_OK:
                break;
            case Codes::HTTP_NOT_FOUND:
                if ($this->logger) {
                    $this->logger->warning('Sku not found.');
                }

                return false;
            default:
                if ($this->logger) {
                    $this->logger->error(
                        'Response status code not expected.',
                        array(
                            'method'   => 'post',
                            'status'   => $response->getStatusCode(),
                            'message'  => $response->getReasonPhrase(),
                            'sku'      => $sku,
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
     * @param string $type
     * @param string $id
     * @param string $permalink
     *
     * @return string
     * @throws Exception
     */
    public function update($sku, $project, $type, $id, $permalink)
    {
        if ($this->logger) {
            $this->logger->debug("KaliProvider::update($sku, $project, $type, $id, $permalink)");
        }
        $data = array(
            'sku' => array(
                'project'   => $project,
                'type'      => $type,
                'id'        => $id,
                'permalink' => $permalink,
            )
        );
        $response = $this->client->request(
            'PUT',
            sprintf(
                '%s/%s',
                self::API_ENDPOINT,
                $sku
            ),
            array(
                'headers' => $this->getDefaultHeaders(),
                'json'    => $data
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
                if ($this->logger) {
                    $this->logger->warning('Sku not found.');
                }

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
                            'method'   => 'put',
                            'status'   => $response->getStatusCode(),
                            'message'  => $response->getReasonPhrase(),
                            'data'     => $data,
                            'response' => $content
                        )
                    );
                }
                throw new Exception('Kali response status code not expected.', $response->getStatusCode());
        }

        return $content;
    }
}
