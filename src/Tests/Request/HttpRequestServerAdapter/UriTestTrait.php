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

use Beotie\PSR7\Request\File\Factory\EmbeddedFileFactoryInterface;
use Beotie\PSR7\Request\HttpRequestServerAdapter;
use PHPUnit\Framework\MockObject\MockObject;
use Beotie\PSR7\Request\Uri\StringUri;
use PHPUnit\Framework\TestCase;

/**
 * Uri test trait
 *
 * This trait is used to validate the HttpRequestServerAdapter instance regarding the uri logic
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait UriTestTrait
{
    /**
     * Uri provider
     *
     * Return an array containing a set of array representing per appearance order, the string uri, the uri host, the
     * base request host and the host preservation state, in order to test the HttpRequestServerAdapter::withUri
     * method.
     *
     * @return [[string, string, string|null, bool]]
     */
    public function uriProvider()
    {
        return [
            ['/uri', 'localhost', null, true],
            ['/uri', 'localhost', null, false],
            ['http://www.example.org/uri', 'www.example.org', null, true],
            ['http://www.example.org/uri', 'www.example.org', null, false],
            ['http://www.example.org/uri', 'www.example.org', 'www.exaple.org', true],
            ['http://www.example.org/uri', 'www.example.org', 'www.exaple.org', false],
            ['/uri', 'localhost', 'www.exaple.org', true],
            ['/uri', 'localhost', 'www.exaple.org', false],
        ];
    }

    /**
     * Test getUri
     *
     * This method validate the HttpRequestServerAdapter::getUri method
     *
     * @param string $uri The expected uri result
     *
     * @return       void
     * @covers       Beotie\PSR7\Request\HttpRequestServerAdapter::getUri
     * @dataProvider uriProvider
     */
    public function testGetUri(string $uri) : void
    {
        $httpRequest = $this->getRequest(
            [
                'getUri' => $uri
            ]
        );

        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);
        $instance = new HttpRequestServerAdapter($httpRequest, $fileFactory);

        $this->getTestCase()->assertEquals($uri, $instance->getUri());

        return;
    }

    /**
     * Test with uri
     *
     * This method validate the HttpRequestServerAdapter::withUri method
     *
     * @param string $uri          The string uri representation
     * @param string $uriHost      The uri host
     * @param string $requestHost  The base request host
     * @param bool   $preserveHost The host preservation state
     *
     * @return       void
     * @covers       Beotie\PSR7\Request\HttpRequestServerAdapter::withUri
     * @covers       Beotie\PSR7\Request\HttpRequestServerAdapter::updateHostFromUri
     * @dataProvider uriProvider
     */
    public function testWithUri(string $uri, string $uriHost, string $requestHost = null, bool $preserveHost) : void
    {
        $httpRequest = $this->getRequest(
            [
                'getHost' => $requestHost
            ]
        );

        $uriObject = new StringUri($uri);
        $this->getTestCase()->assertEquals($uri, (string)$uriObject);

        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);

        $instance = new HttpRequestServerAdapter($httpRequest, $fileFactory);

        $newRequest = $instance->withUri($uriObject, $preserveHost);
        $this->assertInstanceOf(HttpRequestServerAdapter::class, $newRequest);

        $requestProperty = new \ReflectionProperty(HttpRequestServerAdapter::class, 'httpRequest');
        $requestProperty->setAccessible(true);
        $innerRequest = $requestProperty->getValue($newRequest);

        $this->getTestCase()->assertEquals(parse_url($uri, PHP_URL_PATH), $innerRequest->getPathInfo());
        $this->getTestCase()->assertNotSame($httpRequest, $innerRequest);

        if ($preserveHost) {
            $this->getTestCase()->assertEquals($requestHost, $innerRequest->headers->get('HOST'));
            return;
        }

        $this->getTestCase()->assertEquals($uriHost, $innerRequest->headers->get('HOST'));
        return;
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
