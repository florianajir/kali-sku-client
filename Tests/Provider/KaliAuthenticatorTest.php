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

use Meup\Bundle\KaliClientBundle\Provider\KaliAuthenticator;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

/**
 * Class KaliAuthenticatorTest
 *
 * @author Florian Ajir <florian@1001pharmacies.com>
 */
class KaliAuthenticatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testAuthenticateRequest()
    {
        $token = "1234567890";
        $body = <<<JSON
{
  "access_token": {$token}
}
JSON;
        $responses = array(
            new Response(200, array(), $body)
        );
        $client = $this->mockClient($responses);
        $logger = $this->mockLogger();
        $authenticator = new KaliAuthenticator($client, '1234567890', '1234567890');
        $authenticator->setLogger($logger);
        $this->assertNotFalse($authenticator->authenticate());
        $this->assertEquals($token, $authenticator->getToken());
    }

    /**
     *
     */
    public function testAuthenticationError()
    {
        $responses = array(
            new Response(404, array(), "Server not found")
        );
        $client = $this->mockClient($responses);
        $authenticator = new KaliAuthenticator($client, '1234567890', '1234567890');
        $authenticator->setLogger($this->mockLogger(true));
        $this->setExpectedException('\Exception');
        $this->assertNull($authenticator->authenticate());
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
     * @param bool|false $expectError
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface
     */
    private function mockLogger($expectError = false)
    {
        $logger = $this
            ->getMockBuilder('\Psr\Log\LoggerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $logger
            ->expects($this->any())
            ->method('debug')
            ->with('KaliAuthenticator::authenticate()');

        if (true === $expectError) {
            $logger
                ->expects($this->once())
                ->method('error');
        }

        return $logger;
    }
}
