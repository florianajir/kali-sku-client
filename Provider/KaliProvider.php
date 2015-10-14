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
use Guzzle\Http\ClientInterface;
use Guzzle\Http\Message\RequestInterface;
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
    const API_ENDPOINT = '/api';

    /**
     * @var ClientInterface
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
    protected $server;

    /**
     * @var string
     */
    protected $token;

    /**
     * @param ClientInterface $client Guzzle http client
     * @param KaliAuthenticatorInterface $authenticator kali authenticator service
     * @param string $server kali host
     * @param string|bool $certificateAuthority bool, file path, or directory path
     */
    public function __construct(
        ClientInterface $client,
        KaliAuthenticatorInterface $authenticator,
        $server,
        $certificateAuthority = false
    ) {
        $this->client = $client;
        $this->authenticator = $authenticator;
        $this->server = $server;
        $this->client->setBaseUrl($server);
        $this->client->setSslVerification($certificateAuthority);
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
            $this->logger->info("KaliProvider::get($sku)");
        }
        $request = $this->client->get(
            sprintf(
                '%s/%s',
                self::API_ENDPOINT,
                $sku
            )
        );
        $this->setAuthorizationHeader($request);
        $response = $request->send();
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
                            'method' => 'get',
                            'status' => $response->getStatusCode(),
                            'message' => $response->getMessage(),
                            'sku' => $sku
                        )
                    );
                }
                throw new Exception('Kali response status code not expected.');
        }

        return $response->json();
    }

    /**
     * @param RequestInterface $request
     *
     * @return RequestInterface
     * @throws Exception
     */
    private function setAuthorizationHeader(RequestInterface &$request)
    {
        $token = $this->authenticator->authenticate();
        $request->setHeader('Authorization', "Bearer $token");

        return $request;
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
            $this->logger->info("KaliProvider::allocate($project)");
        }
        if (empty($project)) {
            if ($this->logger) {
                $this->logger->critical('Invalid project parameter');
            }
            throw new InvalidArgumentException('project parameter must be set');
        }
        $request = $this->client->post(
            sprintf(
                '%s/%s',
                self::API_ENDPOINT,
                $project
            )
        );
        $this->setAuthorizationHeader($request);
        $response = $request->send();
        if ($response->getStatusCode() !== Codes::HTTP_CREATED) {
            if ($this->logger) {
                $this->logger->error(
                    'Error during sku allocation.',
                    array(
                        'method' => 'post',
                        'status' => $response->getStatusCode(),
                        'message' => $response->getMessage(),
                    )
                );
            }
            throw new Exception('Kali response status code not expected.');
        }

        return $response->json();
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
    public function create($project, $type, $id, $permalink)
    {
        if ($this->logger) {
            $this->logger->info("KaliProvider::create($project, $type, $id, $permalink)");
        }
        if (empty($project)) {
            if ($this->logger) {
                $this->logger->critical('project parameter must be set.');
            }
            throw new InvalidArgumentException('project parameter must be set');
        }
        if (empty($type)) {
            if ($this->logger) {
                $this->logger->critical('type parameter must be set.');
            }
            throw new InvalidArgumentException('type parameter must be set');
        }
        if (empty($id)) {
            if ($this->logger) {
                $this->logger->critical('id parameter must be set.');
            }
            throw new InvalidArgumentException('id parameter must be set');
        }
        $data = array(
            'sku' => array(
                'project' => $project,
                'type' => $type,
                'id' => $id,
                'permalink' => $permalink,
            )
        );
        $request = $this->client->post(
            self::API_ENDPOINT,
            array(),
            $data
        );
        $this->setAuthorizationHeader($request);
        $response = $request->send();
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
                throw new InvalidArgumentException('Sku creation failed due to form error.');
            default:
                if ($this->logger) {
                    $this->logger->error(
                        'Response status code not expected.',
                        array(
                            'method' => 'post',
                            'status' => $response->getStatusCode(),
                            'message' => $response->getMessage(),
                            'data' => $data,
                            'response' => $response->json()
                        )
                    );
                }
                throw new Exception('Kali response status code not expected.');
        }

        return $response->json();
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
            $this->logger->info("KaliProvider::delete($sku)");
        }
        if (empty($sku)) {
            if ($this->logger) {
                $this->logger->critical('sku parameter must be set.');
            }
            throw new InvalidArgumentException('sku parameter must be set');
        }
        $request = $this->client->delete(
            sprintf(
                '%s/%s',
                self::API_ENDPOINT,
                $sku
            )
        );
        $this->setAuthorizationHeader($request);
        $response = $request->send();
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
                            'method' => 'post',
                            'status' => $response->getStatusCode(),
                            'message' => $response->getMessage(),
                            'sku' => $sku,
                            'response' => $response->json()
                        )
                    );
                }
                throw new Exception('Kali response status code not expected.');
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
            $this->logger->info("KaliProvider::disable($sku)");
        }
        if (empty($sku)) {
            if ($this->logger) {
                $this->logger->critical('sku parameter must be set.');
            }
            throw new InvalidArgumentException('sku parameter must be set');
        }
        $request = $this->client->put(
            sprintf(
                '%s/disable/%s',
                self::API_ENDPOINT,
                $sku
            )
        );
        $this->setAuthorizationHeader($request);
        $response = $request->send();
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
                            'method' => 'post',
                            'status' => $response->getStatusCode(),
                            'message' => $response->getMessage(),
                            'sku' => $sku,
                            'response' => $response->json()
                        )
                    );
                }
                throw new Exception('Kali response status code not expected.');
        }

        return $response->json();
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
            $this->logger->info("KaliProvider::update($sku, $project, $type, $id, $permalink)");
        }
        if (empty($sku)) {
            if ($this->logger) {
                $this->logger->critical('sku parameter must be set.');
            }
            throw new InvalidArgumentException('sku parameter must be set');
        }
        if (empty($project)) {
            if ($this->logger) {
                $this->logger->critical('project parameter must be set.');
            }
            throw new InvalidArgumentException('project parameter must be set');
        }
        if (empty($type)) {
            if ($this->logger) {
                $this->logger->critical('type parameter must be set.');
            }
            throw new InvalidArgumentException('type parameter must be set');
        }
        if (empty($id)) {
            if ($this->logger) {
                $this->logger->critical('id parameter must be set.');
            }
            throw new InvalidArgumentException('id parameter must be set');
        }
        $data = array(
            'sku' => array(
                'project' => $project,
                'type' => $type,
                'id' => $id,
                'permalink' => $permalink,
            )
        );
        $request = $this->client->put(
            sprintf(
                '%s/%s',
                self::API_ENDPOINT,
                $sku
            ),
            array(),
            $data
        );
        $this->setAuthorizationHeader($request);
        $response = $request->send();
        switch ($response->getStatusCode()) {
            case Codes::HTTP_OK:
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
                throw new InvalidArgumentException('Sku update failed due to form error.');
            default:
                if ($this->logger) {
                    $this->logger->error(
                        'Response status code not expected.',
                        array(
                            'method' => 'put',
                            'status' => $response->getStatusCode(),
                            'message' => $response->getMessage(),
                            'data' => $data,
                            'response' => $response->json()
                        )
                    );
                }
                throw new Exception('Kali response status code not expected.');
        }

        return $response->json();
    }
}
