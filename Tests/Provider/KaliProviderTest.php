<?php
/**
 * This file is part of the Meup Kali Client Bundle.
 *
 * (c) 1001pharmacies <http://github.com/1001pharmacies/kali-client>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meup\Bundle\KaliClientBundle\Tests\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Meup\Bundle\KaliClientBundle\Provider\KaliAuthenticatorInterface;
use Meup\Bundle\KaliClientBundle\Provider\KaliProvider;
use Meup\Bundle\KaliClientBundle\Util\Codes;
use PHPUnit_Framework_TestCase;

/**
 * Class KaliProviderTest
 *
 * @author Florian Ajir <florian@1001pharmacies.com>
 */
class KaliProviderTest extends PHPUnit_Framework_TestCase
{
    public function testSetLoggerAfterConstruct()
    {
        $logger = $this->mockLogger(null, false);
        $client = $this->mockClient();
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
    }

    /**
     * @param bool|true $infoTrace
     * @param string|null $expectedLogLevel
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface
     */
    private function mockLogger($expectedLogLevel = null, $infoTrace = true)
    {
        $logger = $this
            ->getMockBuilder('\Psr\Log\LoggerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        if (true === $infoTrace) {
            $logger
                ->expects($this->once())
                ->method('debug')
            ;
        }

        if (false === is_null($expectedLogLevel)) {
            $logger
                ->expects($this->once())
                ->method($expectedLogLevel);
        }

        return $logger;
    }

    /**
     * @param array<Response|RequestException> $responses
     *
     * @return Client
     */
    private function mockClient(array $responses = array())
    {
        // Create a mock and queue responses.
        $mock = new MockHandler($responses);
        $handler = HandlerStack::create($mock);
        $client = new Client(array(
            'handler' => $handler,
            'http_errors' => false
        ));

        return $client;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|KaliAuthenticatorInterface
     */
    private function mockAuthenticator()
    {
        return $this
            ->getMockBuilder('Meup\Bundle\KaliClientBundle\Provider\KaliAuthenticatorInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     *
     */
    public function testGetOk()
    {
        $body = '{}';
        $responses = array(
            new Response(Codes::HTTP_OK, array(), $body)
        );
        $logger = $this->mockLogger();
        $client = $this->mockClient($responses);
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $response = $provider->get('sku');
        $this->assertEquals($body, $response);
    }

    /**
     *
     */
    public function testGetGone()
    {
        $responses = array(
            new Response(Codes::HTTP_GONE)
        );
        $logger = $this->mockLogger('notice');
        $client = $this->mockClient($responses);
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $response = $provider->get('sku');
        $this->assertFalse($response);
    }

    /**
     *
     */
    public function testGetNotFound()
    {
        $responses = array(
            new Response(Codes::HTTP_NOT_FOUND)
        );
        $logger = $this->mockLogger('warning');
        $client = $this->mockClient($responses);
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $response = $provider->get('sku');
        $this->assertNull($response);
    }

    /**
     *
     */
    public function testGetUnexpectedResponseStatusCode()
    {
        $responses = array(
            new Response(Codes::HTTP_INTERNAL_SERVER_ERROR)
        );
        $logger = $this->mockLogger('error');
        $client = $this->mockClient($responses);
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $this->setExpectedException('Exception');
        $provider->get('sku');
    }

    /**
     * Allocate with empty project param
     */
    public function testAllocateWithEmptyProjectName()
    {
        $this->setExpectedException('InvalidArgumentException');
        $logger = $this->mockLogger('critical');
        $client = $this->mockClient();
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $provider->allocate(null);
    }

    /**
     * Allocate ok
     */
    public function testAllocateOk()
    {
        $body = '{"code": "1234567"}';
        $responses = array(
            new Response(Codes::HTTP_CREATED, array(), $body)
        );
        $logger = $this->mockLogger();
        $client = $this->mockClient($responses);
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $result = $provider->allocate('app_name');
        $this->assertEquals($body, $result);
    }

    /**
     * allocate unexpected response
     */
    public function testAllocateUnexpectedResponseStatusCode()
    {
        $responses = array(
            new Response(Codes::HTTP_INTERNAL_SERVER_ERROR)
        );
        $logger = $this->mockLogger('error');
        $client = $this->mockClient($responses);
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $this->setExpectedException('Exception');
        $provider->allocate('sku');
    }

    /**
     *
     */
    public function testCreateOk()
    {
        $body = '{}';
        $responses = array(
            new Response(Codes::HTTP_CREATED, array(), $body)
        );
        $logger = $this->mockLogger();
        $client = $this->mockClient($responses);
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $response = $provider->create('project', 'type', 'id', 'permalink');
        $this->assertEquals($body, $response);
    }

    /**
     *
     */
    public function testCreateButExists()
    {
        $body = '{}';
        $responses = array(
            new Response(Codes::HTTP_OK, array(), $body)
        );
        $logger = $this->mockLogger('warning');
        $client = $this->mockClient($responses);
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $result = $provider->create('project', 'type', 'id', 'permalink');
        $this->assertEquals($body, $result);
    }

    /**
     *
     */
    public function testCreateBadRequest()
    {
        $this->setExpectedException('InvalidArgumentException');
        $responses = array(
            new Response(Codes::HTTP_BAD_REQUEST)
        );
        $logger = $this->mockLogger('error');
        $client = $this->mockClient($responses);
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $provider->create('project', 'type', 'id', 'permalink');
    }

    /**
     *
     */
    public function testCreateUnexpectedResponseStatusCode()
    {
        $this->setExpectedException('Exception');
        $responses = array(
            new Response(Codes::HTTP_INTERNAL_SERVER_ERROR)
        );
        $logger = $this->mockLogger('error');
        $client = $this->mockClient($responses);
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $provider->create('project', 'type', 'id', 'permalink');
    }

    /**
     *
     */
    public function testDeleteOk()
    {
        $responses = array(
            new Response(Codes::HTTP_NO_CONTENT)
        );
        $logger = $this->mockLogger();
        $client = $this->mockClient($responses);
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $result = $provider->delete('sku');
        $this->assertTrue($result);
    }

    /**
     *
     */
    public function testDeleteNotFound()
    {
        $responses = array(
            new Response(Codes::HTTP_NOT_FOUND)
        );
        $logger = $this->mockLogger('warning');
        $client = $this->mockClient($responses);
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $result = $provider->delete('sku');
        $this->assertFalse($result);
    }

    /**
     *
     */
    public function testDeleteUnexpectedResponseStatusCode()
    {
        $this->setExpectedException('Exception');
        $responses = array(
            new Response(Codes::HTTP_INTERNAL_SERVER_ERROR)
        );
        $logger = $this->mockLogger('error');
        $client = $this->mockClient($responses);
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $provider->delete('sku');
    }

    /**
     *
     */
    public function testDisableOk()
    {
        $body = '{}';
        $responses = array(
            new Response(Codes::HTTP_OK, array(), $body)
        );
        $logger = $this->mockLogger();
        $client = $this->mockClient($responses);
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $result = $provider->disable('sku');
        $this->assertEquals($body, $result);
    }

    /**
     *
     */
    public function testDisableNotFound()
    {
        $responses = array(
            new Response(Codes::HTTP_NOT_FOUND)
        );
        $logger = $this->mockLogger('warning');
        $client = $this->mockClient($responses);
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $result = $provider->disable('sku');
        $this->assertFalse($result);
    }

    /**
     *
     */
    public function testDisableUnexpectedResponseStatusCode()
    {
        $this->setExpectedException('Exception');
        $responses = array(
            new Response(Codes::HTTP_INTERNAL_SERVER_ERROR)
        );
        $logger = $this->mockLogger('error');
        $client = $this->mockClient($responses);
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $provider->disable('sku');
    }


    /**
     *
     */
    public function testUpdateOk()
    {
        $body = '{}';
        $responses = array(
            new Response(Codes::HTTP_OK, array(), $body)
        );
        $logger = $this->mockLogger();
        $client = $this->mockClient($responses);
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $response = $provider->update('sku', 'project', 'type', 'id', 'permalink');
        $this->assertEquals($body, $response);
    }

    /**
     *
     */
    public function testUpdateConflict()
    {
        $body = '{ "code" : "1234567" }';
        $responses = array(
            new Response(Codes::HTTP_CONFLICT, array(), $body)
        );
        $logger = $this->mockLogger('warning');
        $client = $this->mockClient($responses);
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $response = $provider->update('sku', 'project', 'type', 'id', 'permalink');
        $this->assertJsonStringEqualsJsonString($body, $response);
    }

    /**
     *
     */
    public function testUpdateBadRequest()
    {
        $this->setExpectedException('InvalidArgumentException');
        $responses = array(
            new Response(Codes::HTTP_BAD_REQUEST)
        );
        $logger = $this->mockLogger('error');
        $client = $this->mockClient($responses);
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $provider->update('sku', 'project', 'type', 'id', 'permalink');
    }

    /**
     *
     */
    public function testUpdateUnexpectedResponseStatusCode()
    {
        $this->setExpectedException('Exception');
        $responses = array(
            new Response(Codes::HTTP_INTERNAL_SERVER_ERROR)
        );
        $logger = $this->mockLogger('error');
        $client = $this->mockClient($responses);
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $provider->update('sku', 'project', 'type', 'id', 'permalink');
    }

    /**
     *
     */
    public function testUpdateNotFound()
    {
        $responses = array(
            new Response(Codes::HTTP_NOT_FOUND)
        );
        $logger = $this->mockLogger('warning');
        $client = $this->mockClient($responses);
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $result = $provider->update('sku', 'project', 'type', 'id', 'permalink');
        $this->assertFalse($result);
    }

    public function testDeleteUnauthorized()
    {
        $responses = array(
            new Response(Codes::HTTP_UNAUTHORIZED),
            new Response(Codes::HTTP_NO_CONTENT)
        );
        $logger = $this->mockLogger('notice');
        $client = $this->mockClient($responses);
        $authenticator = $this->mockAuthenticator();
        $provider = new KaliProvider(
            $client,
            $authenticator
        );
        $provider->setLogger($logger);
        $result = $provider->delete('sku');
        $this->assertTrue($result);
    }
}
