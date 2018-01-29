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
 * Protocol test trait
 *
 * This trait is used to validate the HttpRequestServerAdapter instance regarding the protocol logic
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait ProtocolTestTrait
{
    /**
     * Protocol version provider
     *
     * Return an array containing a set of array representing per appearance order, the protocol version and the
     * exception expecting state, in order to test the HttpRequestServerAdapter::withProtocolVersion
     * method.
     *
     * @return string[][]|boolean[][]
     */
    public function protocolVersionProvider()
    {
        return [
            ['1.0', false],
            ['1.1', false],
            ['2', false],
            ['.1', true],
            ['azerty', true],
            ['1.0.0', false]
        ];
    }

    /**
     * Test withProtocolVersion
     *
     * This method validate the HttpRequestServerAdapter::withProtocolVersion method
     *
     * @param string $protocol       The new request protocol version number
     * @param bool   $throwException The expecting exception state of the method call
     *
     * @return       void
     * @dataProvider protocolVersionProvider
     */
    public function testWithProtocolVersion(string $protocol, bool $throwException) : void
    {
        $testCase = $this->getTestCase();

        if ($throwException) {
            $testCase->expectException(\RuntimeException::class);
            $testCase->expectExceptionMessage(
                'The version string MUST contain only the HTTP version number (e.g., "1.1", "1.0").'
            );
        }

        $newRequest = $this->getRequest();
        $request = $this->getRequest($this->guessRequestArgByProtocol($throwException, $newRequest, $protocol));

        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);
        $instance = new HttpRequestServerAdapter($request, $fileFactory);

        $requestProperty = new \ReflectionProperty(HttpRequestServerAdapter::class, 'httpRequest');
        $requestProperty->setAccessible(true);

        $updatedInstance = $instance->withProtocolVersion($protocol);
        $testCase->assertInstanceOf(HttpRequestServerAdapter::class, $updatedInstance);
        $testCase->assertNotSame($instance, $updatedInstance);

        $testCase->assertSame($newRequest, $requestProperty->getValue($updatedInstance));
    }

    /**
     * Test getProtocolVersion
     *
     * This method validate the HttpRequestServerAdapter::getProtocolVersion method
     *
     * @return void
     */
    public function testGetProtocolVersion() : void
    {
        $request = $this->getRequest(
            [],
            [
                'server' => [
                    [
                        'expects' => $this->getTestCase()->once(),
                        'method' => 'get',
                        'with' => $this->getTestCase()->equalTo('SERVER_PROTOCOL'),
                        'willReturn' => '1.1',
                    ]
                ]
            ]
        );

        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);
        $instance = new HttpRequestServerAdapter($request, $fileFactory);

        $this->getTestCase()->assertEquals('1.1', $instance->getProtocolVersion());

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

    /**
     * Guess request argument by protocol
     *
     * Return an invokation mocker configuration that contain a never expectation as 'expects' key in case of true
     * throwException parameter, and complete duplication configuration with server protocol update in case of false
     * throwException parameter.
     *
     * @param bool       $throwException The expected exception state
     * @param MockObject $request        The request object returned by the duplicate method in case of no exception
     *                                   expected
     * @param string     $protocol       The protocol number expected by the duplication of the server parameter
     *
     * @return array
     */
    private function guessRequestArgByProtocol(bool $throwException, MockObject $request, string $protocol) : array
    {
        $testCase = $this->getTestCase();

        if ($throwException) {
            return [
                [
                    'expects' => $testCase->never(),
                    'method' => 'duplicate'
                ]
            ];
        }

        $request->headers->expects($this->getTestCase()->once())
            ->method('set')
            ->with(
                $this->getTestCase()->equalTo('SERVER_PROTOCOL'),
                $this->getTestCase()->equalTo($protocol)
            );

        return [
            [
                'expects' => $testCase->once(),
                'method' => 'duplicate',
                'with' => [
                    $testCase->equalTo([]),
                    $testCase->equalTo([]),
                    $testCase->equalTo([]),
                    $testCase->equalTo([]),
                    $testCase->equalTo([]),
                    $testCase->equalTo([])
                ],
                'willReturn' => $request
            ]
        ];
    }
}
