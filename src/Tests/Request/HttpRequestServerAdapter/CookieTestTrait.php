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
use PHPUnit\Framework\TestCase;

/**
 * Cookie test trait
 *
 * This trait is used to validate the HttpRequestServerAdapter instance regarding the cookie logic
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait CookieTestTrait
{
    /**
     * Test getCookieParams
     *
     * This method validate the HttpRequestServerAdapter::getCookieParams method
     *
     * @return void
     */
    public function testGetCookieParams()
    {
        $cookies = ['key' => 'value'];
        $request = $this->getRequest([], ['cookies' => ['all' => $cookies]]);

        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);
        $instance = new HttpRequestServerAdapter($request, $fileFactory);

        $this->getTestCase()->assertEquals($cookies, $instance->getCookieParams());
    }

    /**
     * Test withCookieParams
     *
     * This method validate the HttpRequestServerAdapter::withCookieParams method
     *
     * @return void
     */
    public function testWithCookieParams()
    {
        $request = $this->getRequest(
            [],
            [
                'cookies' =>
                [
                    [
                        'expects' => $this->getTestCase()->exactly(2),
                        'method' => 'has',
                        'withConsecutive' => [
                            $this->getTestCase()->equalTo('alpha'),
                            $this->getTestCase()->equalTo('beta')
                        ],
                        'willReturnOnConsecutiveCalls' => [true, false]
                    ],
                    [
                        'expects' => $this->getTestCase()->once(),
                        'method' => 'set',
                        'with' => [
                            $this->getTestCase()->equalTo('beta'),
                            $this->getTestCase()->equalTo('betaVal')
                        ]
                    ]
                ]
            ]
        );

        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);
        $instance = new HttpRequestServerAdapter($request, $fileFactory);

        $result = $instance->withCookieParams(['alpha' => 'alphaVal', 'beta' => 'betaVal']);

        $reflex = new \ReflectionProperty(HttpRequestServerAdapter::class, 'httpRequest');
        $reflex->setAccessible(true);

        $this->getTestCase()->assertNotSame($reflex->getValue($instance), $reflex->getValue($result));
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
