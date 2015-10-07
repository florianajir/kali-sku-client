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
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\ClientInterface;
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
     * @var KaliAuthenticator
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
     * @param KaliAuthenticator $authenticator kali authenticator service
     * @param string $server kali host
     * @param string|bool $certificateAuthority bool, file path, or directory path
     */
    public function __construct(
        ClientInterface $client,
        KaliAuthenticator $authenticator,
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
     * Get sku details from server
     *
     * @param string $sku
     *
     * @return string|false|null
     */
    public function get($sku)
    {
        $request = $this->client->get(
            sprintf(
                '%s/%s',
                self::API_ENDPOINT,
                $sku
            )
        );
        $this->setAuthorizationHeader($request);
        $response = $request->send();
        $this->logger->info("KaliProvider::get($sku)");
        switch ($response->getStatusCode()) {
            case Codes::HTTP_OK:
                break;
            case Codes::HTTP_GONE:
                $this->logger->notice('Sku is gone.');
                return false;
            case Codes::HTTP_NOT_FOUND:
                $this->logger->warning('Sku not found.');
                return null;
            default:
                $this->logger->error(
                    'Response status code not expected.',
                    array(
                        'method' => 'get',
                        'status' => $response->getStatusCode(),
                        'message' => $response->getMessage(),
                        'sku' => $sku
                    )
                );
                return null;
        }

        return $response->json();
    }

    /**
     * Generate and allocate a new sku code in registry.
     * Note: used as first step of two-step sku creation process
     *
     * @param string $project
     *
     * @return string
     */
    public function allocate($project)
    {
        if (empty($project) || !is_string($project)) {
            $this->logger->critical('Invalid project parameter');
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
        $this->logger->info("KaliProvider::allocate($project)");
        if ($response->getStatusCode() !== Codes::HTTP_CREATED) {
            $this->logger->error(
                'Error during sku allocation.',
                array(
                    'method' => 'post',
                    'status' => $response->getStatusCode(),
                    'message' => $response->getMessage(),
                )
            );
            return null;
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
     */
    public function create($project, $type, $id, $permalink)
    {
        if (empty($project) || !is_string($project)) {
            $this->logger->critical('project parameter must be set.');
            throw new InvalidArgumentException('project parameter must be set');
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
        $this->logger->info("KaliProvider::create($project, $type, $id, $permalink)");
        switch ($response->getStatusCode()) {
            case Codes::HTTP_CREATED:
                break;
            case Codes::HTTP_OK:
                $this->logger->notice('Existing sku in registry.');
                break;
            case Codes::HTTP_BAD_REQUEST:
                $this->logger->warning('Sku creation failed due to form error.', $data);
                return null;
            default:
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
                return null;
        }

        return $response->json();
    }

    /**
     * Removes a sku
     *
     * @param string $sku
     *
     * @return bool
     */
    public function delete($sku)
    {
        if (empty($sku) || !is_string($sku)) {
            $this->logger->critical('sku parameter must be set.');
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
        $this->logger->info("KaliProvider::delete($sku)");
        switch ($response->getStatusCode()) {
            case Codes::HTTP_OK:
                break;
            case Codes::HTTP_NOT_FOUND:
                $this->logger->warning('Sku not found.');
                return false;
            default:
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
                return false;
        }

        return true;
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
     */
    public function update($sku, $project, $type, $id, $permalink)
    {
        if (empty($sku) || !is_string($sku)) {
            $this->logger->critical('sku parameter must be set.');
            throw new InvalidArgumentException('sku parameter must be set');
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
        $this->logger->info("KaliProvider::update($sku, $project, $type, $id, $permalink)");
        switch ($response->getStatusCode()) {
            case Codes::HTTP_OK:
                break;
            case Codes::HTTP_NOT_FOUND:
                $this->logger->warning('Sku not found.');
                return null;
            case Codes::HTTP_BAD_REQUEST:
                $this->logger->warning('Sku creation failed due to form error.', $data);
                return null;
            default:
                $this->logger->error(
                    'Response status code not expected.',
                    array(
                        'method' => 'update',
                        'status' => $response->getStatusCode(),
                        'message' => $response->getMessage(),
                        'data' => $data,
                        'response' => $response->json()
                    )
                );
                return null;
        }

        return $response->json();
    }
}
