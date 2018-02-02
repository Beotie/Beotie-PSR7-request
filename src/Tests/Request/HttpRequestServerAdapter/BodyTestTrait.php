<?php
declare(strict_types=1);
/**
 * This file is part of beotie/core_bundle
 *
 * As each files provides by the CSCFA, this file is licensed
 * under the MIT license.
 *
 * PHP version 7.1
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
namespace Beotie\PSR7\Tests\Request\HttpRequestServerAdapter;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Beotie\PSR7\Request\File\Factory\EmbeddedFileFactoryInterface;
use Beotie\PSR7\Request\HttpRequestServerAdapter;
use Psr\Http\Message\StreamInterface;

/**
 * Body test trait
 *
 * This trait is used to validate the HttpRequestServerAdapter instance regarding the body logic
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait BodyTestTrait
{
    /**
     * Test getBody
     *
     * This method validate the HttpRequestServerAdapter::getBody method
     *
     * @return void
     */
    public function testGetBody()
    {
        $this->getTestCase()->expectException(\LogicException::class);

        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);
        $instance = new HttpRequestServerAdapter($this->getRequest(), $fileFactory);

        $instance->getBody();
    }

    /**
     * Test withBody
     *
     * This method validate the HttpRequestServerAdapter::withBody method
     *
     * @return void
     */
    public function testWithBody()
    {
        $resultRequest = $this->getRequest();
        $request = $this->getRequest(
            [
                [
                    'expects' => $this->getTestCase()->once(),
                    'method' => 'duplicate',
                    'with' => [
                        $this->getTestCase()->anything(),
                        $this->equalTo(['data'=>'dataValue', 'test'=>1])
                    ],
                    'willReturn' => $resultRequest
                ]
            ]
        );

        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);
        $instance = new HttpRequestServerAdapter($request, $fileFactory);

        $body = $this->createMock(StreamInterface::class);
        $body->expects($this->once())
            ->method('rewind');
        $body->expects($this->once())
            ->method('getContents')
            ->willReturn('data=dataValue&test=1');

        $result = $instance->withBody($body);
        $reflex = new \ReflectionProperty(HttpRequestServerAdapter::class, 'httpRequest');
        $reflex->setAccessible(true);

        $this->getTestCase()->assertSame($resultRequest, $reflex->getValue($result));
    }

    /**
     * Test get url encoded ParsedBody
     *
     * This method validate the HttpRequestServerAdapter::getParsedBody method
     *
     * @return void
     */
    public function testGetUrlEncodedParsedBody()
    {
        $content = ['key' => 'value'];
        $request = $this->getRequest(
            [
                'getContent' => $content,
                'getMethod' => 'POST'
            ],
            [
                'request' => [
                    'all' => $content
                ],
                'headers' => [
                    [
                        'expects' => $this->once(),
                        'method' => 'get',
                        'with' => [$this->equalTo('Content-Type')],
                        'willReturn' => 'application/x-www-form-urlencoded'
                    ]
                ]
            ]
        );

        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);
        $instance = new HttpRequestServerAdapter($request, $fileFactory);

        $this->getTestCase()->assertEquals($content, $instance->getParsedBody());
    }

    /**
     * Test getParsedBody
     *
     * This method validate the HttpRequestServerAdapter::getParsedBody method
     *
     * @return void
     */
    public function testGetParsedBody()
    {
        $content = 'here is my content';
        $request = $this->getRequest(['getContent' => $content]);

        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);
        $instance = new HttpRequestServerAdapter($request, $fileFactory);

        $this->getTestCase()->assertEquals($content, $instance->getParsedBody());
    }

    /**
     * Test withParsedBody
     *
     * This method validate the HttpRequestServerAdapter::withParsedBody method
     *
     * @return void
     */
    public function testWithParsedBody()
    {
        $data = ['data'=>'dataValue', 'test'=>1];
        $resultRequest = $this->getRequest();
        $request = $this->getRequest(
            [
                [
                    'expects' => $this->getTestCase()->once(),
                    'method' => 'duplicate',
                    'with' => [
                        $this->getTestCase()->anything(),
                        $this->equalTo($data)
                    ],
                    'willReturn' => $resultRequest
                ]
            ]
        );

        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);
        $instance = new HttpRequestServerAdapter($request, $fileFactory);


        $result = $instance->withParsedBody($data);
        $reflex = new \ReflectionProperty(HttpRequestServerAdapter::class, 'httpRequest');
        $reflex->setAccessible(true);

        $this->getTestCase()->assertSame($resultRequest, $reflex->getValue($result));
    }

    /**
     * Get request
     *
     * Return an instance of Request mock that contain a ParameterBag mock in cookies, query and request properties
     * and contain a FileBag mock in file property and contain a ServerBag mock in server property.
     * For each properties, the instance will be configured with the according key given bag options. If the
     * configuration is not specified, the instance will return an empty array for each call of all() method.
     *
     * @param array $parameters An array of parameters to create invokation mocker into the returned instance
     * @param array $bagOptions An array containing the bag invocation configuration
     *
     * @return  MockObject
     * @example <pre>
     *  // Simple use
     *  $this->getRequest();
     *
     *  // Usage with invokation mocker
     *  $this->getRequest(
     *      [
     *          [
     *              'expects'    => $this->any(),
     *              'method'     => 'methodName',
     *              'willReturn' => null
     *          ]
     *      ]
     *  );
     *
     *  // Usage with bag invokation configuration
     *  $this->getRequest(
     *      [],
     *      [
     *          'server' => ['all' => []],
     *          'query' => ['any all' => []],
     *          'request' => [
     *              [
     *                  'expects'    => $this->any(),
     *                  'method'     => 'all',
     *                  'willReturn' => []
     *              ]
     *          ]
     *      ]
     *  );
     * </pre>
     */
    protected abstract function getRequest(array $parameters = [], array $bagOptions = []) : MockObject;

    /**
     * Get test case
     *
     * Return an instance of TestCase
     *
     * @return TestCase
     */
    protected abstract function getTestCase() : TestCase;
}
