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

/**
 * Header test trait
 *
 * This trait is used to validate the HttpRequestServerAdapter instance regarding the header logic
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait HeaderTestTrait
{
    /**
     * Header provider
     *
     * Return a set of array containing by appearance order a header key as string, a header value as string or array
     * and a value as string representing the result of the HttpRequestServerAdapter::getHeaderLine method.
     *
     * @return [[string, string|array, string]]
     */
    public function headerProvider() : array
    {
        return [
            ['HTTP_HEADER', 'value', 'value'],
            ['HTTP_HEADER', ['value1', 'value2'], 'value1,value2']
        ];
    }

    /**
     * Test withHeader
     *
     * This method validate the HttpRequestServerAdapter::withHeader method
     *
     * @return void
     */
    public function testWithHeader()
    {
        $newRequest = $this->getRequest(
            [],
            [
                'headers' => [
                    [
                        'expects' => $this->getTestCase()->once(),
                        'method' => 'set',
                        'with' => [
                            $this->getTestCase()->equalTo('HTTP_HEADER'),
                            $this->getTestCase()->equalTo('value')
                        ]
                    ]
                ]
            ]
        );

        $request = $this->getRequest(
            [
                'duplicate' => $newRequest
            ]
        );

        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);
        $instance = new HttpRequestServerAdapter($request, $fileFactory);

        $newInstance = $instance->withHeader('HTTP_HEADER', 'value');

        $requestProperty = new \ReflectionProperty(HttpRequestServerAdapter::class, 'httpRequest');
        $requestProperty->setAccessible(true);

        $this->getTestCase()->assertSame($request, $requestProperty->getValue($instance));
        $this->getTestCase()->assertSame($newRequest, $requestProperty->getValue($newInstance));
        $this->getTestCase()->assertNotSame($request, $requestProperty->getValue($newInstance));
        $this->getTestCase()->assertNotSame($newRequest, $requestProperty->getValue($instance));
    }

    /**
     * Test getHeaders
     *
     * This method validate the HttpRequestServerAdapter::getHeaders method
     *
     * @return void
     */
    public function testGetHeaders()
    {
        $headers = ['PROTOCOL_VERSION' => '1.0', 'HTTP_HEADER' => 'Header'];
        $request = $this->getRequest([], ['headers' => ['all' => $headers]]);

        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);
        $instance = new HttpRequestServerAdapter($request, $fileFactory);

        $this->getTestCase()->assertEquals($headers, $instance->getHeaders());
    }

    /**
     * Test hasHeader
     *
     * This method validate the HttpRequestServerAdapter::hasHeader method
     *
     * @param string $header The header key to be used at method call
     *
     * @return       void
     * @dataProvider headerProvider
     */
    public function testHasHeader(string $header)
    {
        $testCase = $this->getTestCase();
        $request = $this->getRequest(
            [],
            [
                'headers' => [
                    [
                        'expects' => $testCase->exactly(6),
                        'method' => 'all',
                        'willReturnOnConsecutiveCalls' => [
                            [$header => ''],
                            [$header => ''],
                            [strtolower($header) => ''],
                            [strtolower($header) => ''],
                            [substr($header, 0, (strlen($header) - 1)) => ''],
                            [substr($header, 0, (strlen($header) - 1)) => '']
                        ]
                    ]
                ]
            ]
        );

        $map = [
            [true, $header, 'Failed to retreive normal header'],
            [true, strtolower($header), 'Failed to retreive lower case given header header'],
            [true, $header, 'Failed to retreive lower case stored header'],
            [true, strtolower($header), 'Failed to retreive both lower case header'],
            [false, $header, 'Failed to return false'],
            [false, strtolower($header), 'Failed to return false with lower case given header']
        ];

        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);
        $instance = new HttpRequestServerAdapter($request, $fileFactory);
        foreach ($map as $testStep) {
            list($expectation, $argument, $message) = $testStep;

            if ($expectation) {
                $testCase->assertTrue($instance->hasHeader($argument), $message);
                continue;
            }
            $testCase->assertFalse($instance->hasHeader($argument), $message);
        }

        return;
    }

    /**
     * Test withAddedHeader
     *
     * This method validate the HttpRequestServerAdapter::withAddedHeader method
     *
     * @return void
     */
    public function testWithAddedHeader()
    {
        $newRequest = $this->getRequest(
            [],
            [
                'headers' => [
                    [
                        'expects' => $this->getTestCase()->once(),
                        'method' => 'set',
                        'with' => [
                            $this->getTestCase()->equalTo('HTTP_HEADER'),
                            $this->getTestCase()->equalTo('value'),
                            $this->getTestCase()->isFalse()
                        ]
                    ]
                ]
            ]
        );

        $request = $this->getRequest(
            [
                'duplicate' => $newRequest
            ]
        );

        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);
        $instance = new HttpRequestServerAdapter($request, $fileFactory);

        $newInstance = $instance->withAddedHeader('HTTP_HEADER', 'value');

        $requestProperty = new \ReflectionProperty(HttpRequestServerAdapter::class, 'httpRequest');
        $requestProperty->setAccessible(true);

        $this->getTestCase()->assertSame($request, $requestProperty->getValue($instance));
        $this->getTestCase()->assertSame($newRequest, $requestProperty->getValue($newInstance));
        $this->getTestCase()->assertNotSame($request, $requestProperty->getValue($newInstance));
        $this->getTestCase()->assertNotSame($newRequest, $requestProperty->getValue($instance));
    }

    /**
     * Test getHeader
     *
     * This method validate the HttpRequestServerAdapter::getHeader method
     *
     * @param string       $header The header key to be used at method call
     * @param string|array $value  The header value to be injected into the header bag
     *
     * @return       void
     * @dataProvider headerProvider
     */
    public function testGetHeader(string $header, $value) : void
    {
        $testCase = $this->getTestCase();
        $request = $this->getRequest(
            [],
            [
                'headers' => [
                    [
                        'expects' => $testCase->exactly(6),
                        'method' => 'all',
                        'willReturnOnConsecutiveCalls' => [
                            [$header => $value],
                            [$header => $value],
                            [strtolower($header) => $value],
                            [strtolower($header) => $value],
                            [substr($header, 0, (strlen($header) - 1)) => $value],
                            [substr($header, 0, (strlen($header) - 1)) => $value]
                        ]
                    ]
                ]
            ]
        );

        if (!is_array($value)) {
            $value = [$value];
        }

        $map = [
            [$value, $header, 'Failed to retreive normal header'],
            [$value, strtolower($header), 'Failed to retreive lower case given header header'],
            [$value, $header, 'Failed to retreive lower case stored header'],
            [$value, strtolower($header), 'Failed to retreive both lower case header'],
            [[], $header, 'Failed to return empty array'],
            [[], strtolower($header), 'Failed to return empty array with lower case given header']
        ];

        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);
        $instance = new HttpRequestServerAdapter($request, $fileFactory);
        foreach ($map as $testStep) {
            list($expectation, $argument, $message) = $testStep;
            $result = $instance->getHeader($argument);

            $testCase->assertTrue(is_array($result));
            $testCase->assertEquals($expectation, $result, $message);
        }

        return;
    }

    /**
     * Test getHeaderLine
     *
     * This method validate the HttpRequestServerAdapter::getHeaderLine method
     *
     * @param string       $header         The header key to be used at method call
     * @param string|array $value          The header value to be injected into the header bag
     * @param string       $expectedResult The expected result of the method call
     *
     * @return       void
     * @dataProvider headerProvider
     */
    public function testGetHeaderLine(string $header, $value, string $expectedResult) : void
    {
        $testCase = $this->getTestCase();
        $request = $this->getRequest(
            [],
            [
                'headers' => [
                    [
                        'expects' => $testCase->exactly(6),
                        'method' => 'all',
                        'willReturnOnConsecutiveCalls' => [
                            [$header => $value],
                            [$header => $value],
                            [strtolower($header) => $value],
                            [strtolower($header) => $value],
                            [substr($header, 0, (strlen($header) - 1)) => $value],
                            [substr($header, 0, (strlen($header) - 1)) => $value]
                        ]
                    ]
                ]
            ]
        );

        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);
        $instance = new HttpRequestServerAdapter($request, $fileFactory);

        $map = [
            [$expectedResult, $header, 'Failed to retreive normal header'],
            [$expectedResult, strtolower($header), 'Failed to retreive lower case given header header'],
            [$expectedResult, $header, 'Failed to retreive lower case stored header'],
            [$expectedResult, strtolower($header), 'Failed to retreive both lower case header'],
            ['', $header, 'Failed to return empty string'],
            ['', strtolower($header), 'Failed to return empty string with lower case given header']
        ];

        foreach ($map as $testStep) {
            list($expectation, $argument, $message) = $testStep;
            $testCase->assertEquals($expectation, $instance->getHeaderLine($argument), $message);
        }

        return;
    }

    /**
     * Test withoutHeader
     *
     * This method validate the HttpRequestServerAdapter::withoutHeader method
     *
     * @return void
     */
    public function testWithoutHeader() : void
    {
        $testCase = $this->getTestCase();
        $request = $this->getRequest(
            [
                [
                    'expects' => $testCase->once(),
                    'method' => 'duplicate',
                    'with' => [
                        $testCase->equalTo([]),
                        $testCase->equalTo([]),
                        $testCase->equalTo([]),
                        $testCase->equalTo([]),
                        $testCase->equalTo([]),
                        $testCase->equalTo(
                            [
                                'HTTP_HEADER' => true
                            ]
                        )
                    ],
                    'willReturn' => $this->getRequest(
                        [],
                        [
                            'headers' => [
                                [
                                    'expects' => $testCase->once(),
                                    'method' => 'has',
                                    'with' => $testCase->equalTo('HTTP_HEADER'),
                                    'willReturn' => true
                                ],
                                [
                                    'expects' => $testCase->once(),
                                    'method' => 'remove',
                                    'with' => $testCase->equalTo('HTTP_HEADER')
                                ]
                            ]
                        ]
                    )
                ]
            ],
            [
                'server' => [
                    'all' => [
                        'HTTP_HEADER' => true
                    ]
                ]
            ]
        );

        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);
        $instance = new HttpRequestServerAdapter($request, $fileFactory);

        $instance->withoutHeader('HTTP_HEADER');
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
