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

use Beotie\PSR7\Request\HttpRequestServerAdapter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Beotie\PSR7\Request\File\Factory\EmbeddedFileFactoryInterface;

/**
 * Request target test trait
 *
 * This trait is used to validate the HttpRequestServerAdapter instance regarding the request target logic
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait RequestTargetTestTrait
{
    /**
     * Request target provider
     *
     * Return an array of 2 dimensions of string value to be used by the testGetRequestTarget as testing data fixtures.
     *
     * @see    self::testGetRequestTarget
     * @return [[string]]
     */
    public function requestTargetProvider() : array
    {
        return [
            ['/where?q=now'],
            ['http://www.example.org/pub/WWW/TheProject.html'],
            ['www.example.com:80']
        ];
    }

    /**
     * Test withRequestTarget
     *
     * This method validate the HttpRequestServerAdapter::withRequestTarget method
     *
     * @param string $requestTarget The request target to be used as new request target
     *
     * @return       void
     * @covers       Beotie\PSR7\Request\HttpRequestServerAdapter::withRequestTarget
     * @dataProvider requestTargetProvider
     */
    public function testWithRequestTarget(string $requestTarget) : void
    {
        $request = $this->getRequest();
        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);

        $reflex = new \ReflectionClass(HttpRequestServerAdapter::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $requestProperty = $reflex->getProperty('httpRequest');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($instance, $request);

        $fileFactoryProperty = $reflex->getProperty('fileFactory');
        $fileFactoryProperty->setAccessible(true);
        $fileFactoryProperty->setValue($instance, $fileFactory);

        $newRequest = $instance->withRequestTarget($requestTarget);
        $innerRequest = $requestProperty->getValue($newRequest);

        $this->getTestCase()->assertNotSame($innerRequest, $request);
        $this->getTestCase()->assertEquals($this->gessRequestTarget($requestTarget), $innerRequest->getUri());

        return;
    }

    /**
     * Guess request target
     *
     * Return the requestTarget as server understandable request string. By this way, add localhost as host if not
     * defined, add http scheme if not defined and remove port.
     *
     * @param string $baseRequest The base request to guess
     *
     * @return string
     */
    protected function gessRequestTarget(string $baseRequest) : string
    {
        $result = $baseRequest;
        $parsingResult = parse_url($baseRequest);
        $parsingResult['host'] = ($parsingResult['host'] ?? null);
        $parsingResult['scheme'] = ($parsingResult['scheme'] ?? null);
        $parsingResult['path'] = ($parsingResult['path'] ?? null);

        if ($parsingResult['host'] === null) {
            $result = sprintf('localhost%s', $result);
        }
        if ($parsingResult['scheme'] === null) {
            $result = sprintf('http://%s', $result);
        }
        if (!$parsingResult['path']) {
            $result = sprintf('%s/', $result);
        }

        return preg_replace('/:[0-9]+/', '', $result);
    }

    /**
     * Test getRequestTarget
     *
     * Validate that the HttpRequestServerAdapter::getRequestTarget return the result of the Request's getRequestUri()
     * method.
     *
     * @param string $requestTarget The request target expected to be returned by the
     *                              HttpRequestServerAdapter::getRequestTarget and injected into the Request instance
     *
     * @return       void
     * @covers       Beotie\PSR7\Request\HttpRequestServerAdapter::getRequestTarget
     * @dataProvider requestTargetProvider
     */
    public function testGetRequestTarget(string $requestTarget) : void
    {
        $request = $this->getRequest(
            [
                [
                    'expects' => $this->once(),
                    'method' => 'getRequestUri',
                    'willReturn' => $requestTarget
                ]
            ]
        );

        $reflex = new \ReflectionClass(HttpRequestServerAdapter::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $requestProperty = $reflex->getProperty('httpRequest');
        $requestProperty->setAccessible(true);

        $requestProperty->setValue($instance, $request);

        $this->getTestCase()->assertEquals($requestTarget, $instance->getRequestTarget());

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
