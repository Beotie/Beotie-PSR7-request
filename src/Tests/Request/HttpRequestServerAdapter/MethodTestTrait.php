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
 * Method test trait
 *
 * This trait is used to validate the HttpRequestServerAdapter instance regarding the method logic
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait MethodTestTrait
{
    /**
     * Method provider
     *
     * Return an array of 2 dimensions of string value to be used by the testGetMethod as testing data fixtures.
     *
     * @see    self::testGetMethod
     * @return [[string]]
     */
    public function methodProvider() : array
    {
        return [
            ['POST'],
            ['GET'],
            ['PUT'],
            ['PATCH'],
            ['PING'],
            ['CONNECT']
        ];
    }

    /**
     * Test withMethod
     *
     * This method validate the HttpRequestServerAdapter::withMethod method
     *
     * @param string $method The method to use as method argument
     *
     * @return       void
     * @dataProvider methodProvider
     */
    public function testWithMethod(string $method) : void
    {
        $request = $this->getRequest();
        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);
        $instance = new HttpRequestServerAdapter($request, $fileFactory);

        $newInstance = $instance->withMethod($method);

        $this->getTestCase()->assertNotSame($instance, $newInstance);

        $requestProperty = new \ReflectionProperty(HttpRequestServerAdapter::class, 'httpRequest');
        $requestProperty->setAccessible(true);
        $innerRequest = $requestProperty->getValue($newInstance);

        $this->getTestCase()->assertNotSame($request, $innerRequest);
        $this->getTestCase()->assertEquals($method, $innerRequest->getMethod());
        return;
    }

    /**
     * Test getMethod
     *
     * Validate that the HttpRequestServerAdapter::getMethod return the result of the Request's getMethod() method.
     *
     * @param string $method The method expected to be returned by the HttpRequestServerAdapter::getMethod and injected
     *                       into the Request instance
     *
     * @return       void
     * @dataProvider methodProvider
     */
    public function testGetMethod(string $method) : void
    {
        $request = $this->getRequest(
            [
                [
                    'expects' => $this->once(),
                    'method' => 'getMethod',
                    'willReturn' => $method
                ]
            ]
        );

        $reflex = new \ReflectionClass(HttpRequestServerAdapter::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $requestProperty = $reflex->getProperty('httpRequest');
        $requestProperty->setAccessible(true);

        $requestProperty->setValue($instance, $request);

        $this->getTestCase()->assertEquals($method, $instance->getMethod());

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
