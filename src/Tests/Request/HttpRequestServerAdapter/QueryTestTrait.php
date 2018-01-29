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
use Symfony\Component\HttpFoundation\Request;

/**
 * Query test trait
 *
 * This trait is used to validate the HttpRequestServerAdapter instance regarding the query logic
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait QueryTestTrait
{
    /**
     * Test withQueryParams
     *
     * This method validate the HttpRequestServerAdapter::withQueryParams method
     *
     * @return                               void
     * @covers                               Beotie\PSR7\Request\HttpRequestServerAdapter::withQueryParams
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testWithQueryParams() : void
    {
        $baseQuery = ['query' => 'param'];
        $request = Request::create('', 'GET', $baseQuery, [], [], [], null);

        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);
        $instance = new HttpRequestServerAdapter($request, $fileFactory);

        $requestProperty = new \ReflectionProperty(HttpRequestServerAdapter::class, 'httpRequest');
        $requestProperty->setAccessible(true);

        $newQuery = ['queries' => 'params'];
        $newInstance = $instance->withQueryParams($newQuery);

        $this->getTestCase()->assertEquals($requestProperty->getValue($instance)->query->all(), $baseQuery);
        $this->getTestCase()->assertEquals($requestProperty->getValue($newInstance)->query->all(), $newQuery);

        return;
    }

    /**
     * Test getQueryParams
     *
     * This method validate the HttpRequestServerAdapter::getQueryParams method
     *
     * @return void
     * @covers Beotie\PSR7\Request\HttpRequestServerAdapter::getQueryParams
     */
    public function testGetQueryParams() : void
    {
        $baseQuery = ['query' => 'param'];

        $httpRequest = $this->getRequest(
            [],
            [
                'query' => ['all' => $baseQuery]
            ]
        );

        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);
        $instance = new HttpRequestServerAdapter($httpRequest, $fileFactory);

        $this->getTestCase()->assertEquals($baseQuery, $instance->getQueryParams());

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
