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

use Guzzle\Http\Client;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Meup\Bundle\KaliClientBundle\Provider\KaliAuthenticatorInterface;
use Meup\Bundle\KaliClientBundle\Provider\KaliProvider;
use Meup\Bundle\KaliClientBundle\Util\Codes;
use PHPUnit_Framework_TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class KaliProviderTest
 *
 * @author Florian Ajir <florian@1001pharmacies.com>
 */
class KaliProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $server;

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
        $authenticator = $this->getAuthenticatorMock();
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
        );
        $provider->setLogger($logger);
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
     * @return \PHPUnit_Framework_MockObject_MockObject|Client
     */
    private function getClientMock()
    {
        return $this
            ->getMockBuilder('Guzzle\Http\Client')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|KaliAuthenticatorInterface
     */
    private function getAuthenticatorMock()
    {
        return $this
            ->getMockBuilder('Meup\Bundle\KaliClientBundle\Provider\KaliAuthenticatorInterface')
            ->disableOriginalConstructor()
            ->getMock();
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
    public function testGetOk()
    {
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('info')
            ->willReturn(null);
        $response = $this->getResponseMock();
        $response
            ->expects($this->once())
            ->method('json')
            ->willReturn(array());
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(Codes::HTTP_OK);
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
        $authenticator = $this->getAuthenticatorMock();
        $authenticator
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn('access_token');
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
        );
        $provider->setLogger($logger);
        $response = $provider->get('sku');
        $this->assertNotNull($response);
        $this->assertNotFalse($response);
    }

    /**
     *
     */
    public function testGetGone()
    {
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('notice')
            ->willReturn(null);
        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(Codes::HTTP_GONE);
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
        $authenticator = $this->getAuthenticatorMock();
        $authenticator
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn('access_token');
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
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
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('warning')
            ->willReturn(null);
        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(Codes::HTTP_NOT_FOUND);
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
        $authenticator = $this->getAuthenticatorMock();
        $authenticator
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn('access_token');
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
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
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('error')
            ->willReturn(null);
        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(Codes::HTTP_INTERNAL_SERVER_ERROR);
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
        $authenticator = $this->getAuthenticatorMock();
        $authenticator
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn('access_token');
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
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
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('critical')
            ->willReturn(null);
        $client = $this->getClientMock();
        $authenticator = $this->getAuthenticatorMock();
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
        );
        $provider->setLogger($logger);
        $provider->allocate(null);
    }

    /**
     * Allocate ok
     */
    public function testAllocateOk()
    {
        $expected = array();
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('info')
            ->willReturn(null);
        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(Codes::HTTP_CREATED);
        $response
            ->expects($this->once())
            ->method('json')
            ->willReturn($expected);
        $request = $this->getRequestMock();
        $request
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);
        $client = $this->getClientMock();
        $client
            ->expects($this->once())
            ->method('post')
            ->willReturn($request);
        $authenticator = $this->getAuthenticatorMock();
        $authenticator
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn('access_token');
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
        );
        $provider->setLogger($logger);
        $result = $provider->allocate('app_name');
        $this->assertEquals($expected, $result);
    }

    /**
     * allocate unexpected response
     */
    public function testAllocateUnexpectedResponseStatusCode()
    {
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('error')
            ->willReturn(null);
        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(Codes::HTTP_INTERNAL_SERVER_ERROR);
        $request = $this->getRequestMock();
        $request
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);
        $client = $this->getClientMock();
        $client
            ->expects($this->once())
            ->method('post')
            ->willReturn($request);
        $authenticator = $this->getAuthenticatorMock();
        $authenticator
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn('access_token');
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
        );
        $provider->setLogger($logger);
        $this->setExpectedException('Exception');
        $provider->allocate('sku');
    }

    /**
     * Create with empty project parameter
     */
    public function testCreateWithEmptyParams()
    {
        $this->setExpectedException('InvalidArgumentException');
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('critical')
            ->willReturn(null);
        $client = $this->getClientMock();
        $authenticator = $this->getAuthenticatorMock();
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
        );
        $provider->setLogger($logger);
        $provider->create(null, null, null, null);
    }

    /**
     *
     */
    public function testCreateOk()
    {
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('info')
            ->willReturn(null);
        $response = $this->getResponseMock();
        $response
            ->expects($this->once())
            ->method('json')
            ->willReturn(array());
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(Codes::HTTP_CREATED);
        $request = $this->getRequestMock();
        $request
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);
        $client = $this->getClientMock();
        $client
            ->expects($this->once())
            ->method('post')
            ->willReturn($request);
        $authenticator = $this->getAuthenticatorMock();
        $authenticator
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn('access_token');
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
        );
        $provider->setLogger($logger);
        $response = $provider->create('project', 'type', 'id', 'permalink');
        $this->assertNotNull($response);
        $this->assertNotFalse($response);
    }

    /**
     *
     */
    public function testCreateButExists()
    {
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('info')
            ->willReturn(null);
        $logger
            ->expects($this->once())
            ->method('warning')
            ->willReturn(null);
        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(Codes::HTTP_OK);
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
            ->method('post')
            ->willReturn($request);
        $authenticator = $this->getAuthenticatorMock();
        $authenticator
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn('access_token');
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
        );
        $provider->setLogger($logger);
        $result = $provider->create('project', 'type', 'id', 'permalink');
        $this->assertNotNull($result);
        $this->assertNotFalse($result);
    }

    /**
     *
     */
    public function testCreateBadRequest()
    {
        $this->setExpectedException('InvalidArgumentException');
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('error')
            ->willReturn(null);
        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(Codes::HTTP_BAD_REQUEST);
        $request = $this->getRequestMock();
        $request
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);
        $client = $this->getClientMock();
        $client
            ->expects($this->once())
            ->method('post')
            ->willReturn($request);
        $authenticator = $this->getAuthenticatorMock();
        $authenticator
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn('access_token');
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
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
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('error')
            ->willReturn(null);
        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(Codes::HTTP_INTERNAL_SERVER_ERROR);
        $request = $this->getRequestMock();
        $request
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);
        $client = $this->getClientMock();
        $client
            ->expects($this->once())
            ->method('post')
            ->willReturn($request);
        $authenticator = $this->getAuthenticatorMock();
        $authenticator
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn('access_token');
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
        );
        $provider->setLogger($logger);
        $provider->create('project', 'type', 'id', 'permalink');
    }

    /**
     * Create with empty sku parameter
     */
    public function testDeleteWithoutSku()
    {
        $this->setExpectedException('InvalidArgumentException');
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('critical')
            ->willReturn(null);
        $client = $this->getClientMock();
        $authenticator = $this->getAuthenticatorMock();
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
        );
        $provider->setLogger($logger);
        $provider->delete(null);
    }

    /**
     *
     */
    public function testDeleteOk()
    {
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('info')
            ->willReturn(null);
        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(Codes::HTTP_NO_CONTENT);
        $request = $this->getRequestMock();
        $request
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);
        $client = $this->getClientMock();
        $client
            ->expects($this->once())
            ->method('delete')
            ->willReturn($request);
        $authenticator = $this->getAuthenticatorMock();
        $authenticator
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn('access_token');
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
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
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('info')
            ->willReturn(null);
        $logger
            ->expects($this->once())
            ->method('warning')
            ->willReturn(null);
        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(Codes::HTTP_NOT_FOUND);
        $request = $this->getRequestMock();
        $request
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);
        $client = $this->getClientMock();
        $client
            ->expects($this->once())
            ->method('delete')
            ->willReturn($request);
        $authenticator = $this->getAuthenticatorMock();
        $authenticator
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn('access_token');
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
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
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('error')
            ->willReturn(null);
        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(Codes::HTTP_INTERNAL_SERVER_ERROR);
        $request = $this->getRequestMock();
        $request
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);
        $client = $this->getClientMock();
        $client
            ->expects($this->once())
            ->method('delete')
            ->willReturn($request);
        $authenticator = $this->getAuthenticatorMock();
        $authenticator
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn('access_token');
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
        );
        $provider->setLogger($logger);
        $provider->delete('sku');
    }

    /**
     * Disable with empty sku parameter
     */
    public function testDisableWithoutSku()
    {
        $this->setExpectedException('InvalidArgumentException');
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('critical')
            ->willReturn(null);
        $client = $this->getClientMock();
        $authenticator = $this->getAuthenticatorMock();
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
        );
        $provider->setLogger($logger);
        $provider->disable(null);
    }

    /**
     *
     */
    public function testDisableOk()
    {
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('info')
            ->willReturn(null);
        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(Codes::HTTP_OK);
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
            ->method('put')
            ->willReturn($request);
        $authenticator = $this->getAuthenticatorMock();
        $authenticator
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn('access_token');
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
        );
        $provider->setLogger($logger);
        $result = $provider->disable('sku');
        $this->assertNotNull($result);
    }

    /**
     *
     */
    public function testDisableNotFound()
    {
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('info')
            ->willReturn(null);
        $logger
            ->expects($this->once())
            ->method('warning')
            ->willReturn(null);
        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(Codes::HTTP_NOT_FOUND);
        $request = $this->getRequestMock();
        $request
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);
        $client = $this->getClientMock();
        $client
            ->expects($this->once())
            ->method('put')
            ->willReturn($request);
        $authenticator = $this->getAuthenticatorMock();
        $authenticator
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn('access_token');
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
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
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('error')
            ->willReturn(null);
        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(Codes::HTTP_INTERNAL_SERVER_ERROR);
        $request = $this->getRequestMock();
        $request
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);
        $client = $this->getClientMock();
        $client
            ->expects($this->once())
            ->method('put')
            ->willReturn($request);
        $authenticator = $this->getAuthenticatorMock();
        $authenticator
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn('access_token');
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
        );
        $provider->setLogger($logger);
        $provider->disable('sku');
    }

    /**
     * Update with empty project parameter
     */
    public function testUpdateWithEmptyParams()
    {
        $this->setExpectedException('InvalidArgumentException');
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('critical')
            ->willReturn(null);
        $client = $this->getClientMock();
        $authenticator = $this->getAuthenticatorMock();
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
        );
        $provider->setLogger($logger);
        $provider->update(null, null, null, null, null);
    }

    /**
     *
     */
    public function testUpdateOk()
    {
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('info')
            ->willReturn(null);
        $response = $this->getResponseMock();
        $response
            ->expects($this->once())
            ->method('json')
            ->willReturn(array());
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(Codes::HTTP_OK);
        $request = $this->getRequestMock();
        $request
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);
        $client = $this->getClientMock();
        $client
            ->expects($this->once())
            ->method('put')
            ->willReturn($request);
        $authenticator = $this->getAuthenticatorMock();
        $authenticator
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn('access_token');
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
        );
        $provider->setLogger($logger);
        $response = $provider->update('sku', 'project', 'type', 'id', 'permalink');
        $this->assertNotNull($response);
        $this->assertNotFalse($response);
    }

    /**
     *
     */
    public function testUpdateBadRequest()
    {
        $this->setExpectedException('InvalidArgumentException');
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('error')
            ->willReturn(null);
        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(Codes::HTTP_BAD_REQUEST);
        $request = $this->getRequestMock();
        $request
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);
        $client = $this->getClientMock();
        $client
            ->expects($this->once())
            ->method('put')
            ->willReturn($request);
        $authenticator = $this->getAuthenticatorMock();
        $authenticator
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn('access_token');
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
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
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('error')
            ->willReturn(null);
        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(Codes::HTTP_INTERNAL_SERVER_ERROR);
        $request = $this->getRequestMock();
        $request
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);
        $client = $this->getClientMock();
        $client
            ->expects($this->once())
            ->method('put')
            ->willReturn($request);
        $authenticator = $this->getAuthenticatorMock();
        $authenticator
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn('access_token');
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
        );
        $provider->setLogger($logger);
        $provider->update('sku', 'project', 'type', 'id', 'permalink');
    }

    /**
     *
     */
    public function testUpdateNotFound()
    {
        $logger = $this->getLoggerMock();
        $logger
            ->expects($this->once())
            ->method('info')
            ->willReturn(null);
        $logger
            ->expects($this->once())
            ->method('warning')
            ->willReturn(null);
        $response = $this->getResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(Codes::HTTP_NOT_FOUND);
        $request = $this->getRequestMock();
        $request
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);
        $client = $this->getClientMock();
        $client
            ->expects($this->once())
            ->method('put')
            ->willReturn($request);
        $authenticator = $this->getAuthenticatorMock();
        $authenticator
            ->expects($this->once())
            ->method('authenticate')
            ->willReturn('access_token');
        $provider = new KaliProvider(
            $client,
            $authenticator,
            $this->server,
            $this->certificate
        );
        $provider->setLogger($logger);
        $result = $provider->update('sku', 'project', 'type', 'id', 'permalink');
        $this->assertFalse($result);
    }

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->server = 'http://';
        $this->certificate = false;
    }
}
