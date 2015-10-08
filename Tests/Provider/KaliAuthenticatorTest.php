<?php
/**
 * This file is part of the Meup Kali Client Bundle.
 *
 * (c) 1001pharmacies <http://github.com/1001pharmacies/kali-client>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meup\Bundle\KaliClientBundle\Tests\Manager;

use Guzzle\Http\ClientInterface;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Meup\Bundle\KaliClientBundle\Provider\KaliAuthenticator;
use PHPUnit_Framework_TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class KaliAuthenticatorTest
 *
 * @author Florian Ajir <florian@1001pharmacies.com>
 */
class KaliAuthenticatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $server;

    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var string
     */
    private $secretKey;

    /**
     * @var string|bool
     */
    private $certificate;

    /**
     *
     */
    public function testSetLoggerAfterConstruct()
    {
        $logger = $this->getLoggerMock();
        $client = $this->getClientMock();
        $authenticator = new KaliAuthenticator(
            $client,
            $this->server,
            $this->publicKey,
            $this->secretKey,
            $this->certificate
        );
        $authenticator->setLogger($logger);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
     */
    private function getLoggerMock()
    {
        return $this
            ->getMockBuilder('Psr\Log\LoggerInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ClientInterface
     */
    private function getClientMock()
    {
        return $this
            ->getMockBuilder('Guzzle\Http\ClientInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     *
     */
    public function testAuthenticateSucceed()
    {
        $token = '123';
        $response = $this->getResponseMock();
        $response
            ->expects($this->once())
            ->method('json')
            ->willReturn(array(
                'access_token' => $token
            ));
        $request = $this->getRequestMock();
        $request
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);
        $client = $this->getClientMock();
        $client
            ->expects($this->once())
            ->method('get')
            ->willReturn($request);
        $authenticator = new KaliAuthenticator(
            $client,
            $this->server,
            $this->publicKey,
            $this->secretKey,
            $this->certificate
        );
        $result = $authenticator->authenticate();
        $this->assertEquals($token, $result);
        $this->assertEquals($result, $authenticator->getToken());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Response
     */
    private function getResponseMock()
    {
        return $this
            ->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RequestInterface
     */
    private function getRequestMock()
    {
        return $this
            ->getMockBuilder('Guzzle\Http\Message\RequestInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     *
     */
    public function testAuthenticateError()
    {
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('error')
            ->willReturn(null);
        $response = $this->getResponseMock();
        $response
            ->expects($this->once())
            ->method('json')
            ->willReturn(array());
        $request = $this->getRequestMock();
        $request
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);
        $client = $this->getClientMock();
        $client
            ->expects($this->once())
            ->method('get')
            ->willReturn($request);
        $authenticator = new KaliAuthenticator(
            $client,
            $this->server,
            $this->publicKey,
            $this->secretKey,
            $this->certificate
        );
        $authenticator->setLogger($logger);
        $this->setExpectedException('Exception');
        $authenticator->authenticate();
    }

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->server = 'http://';
        $this->publicKey = '123';
        $this->secretKey = '123';
        $this->certificate = false;
    }
}
