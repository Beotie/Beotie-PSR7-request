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
use Psr\Http\Message\UploadedFileInterface;

/**
 * File test trait
 *
 * This trait is used to validate the HttpRequestServerAdapter instance regarding the file logic
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait FileTestTrait
{
    /**
     * Test withUploadedFiles
     *
     * This method validate the HttpRequestServerAdapter::withUploadedFiles method
     *
     * @return void
     */
    public function testWithUploadedFiles()
    {
        $uploadedFile = $this->createMock(UploadedFileInterface::class);
        $newUploadedFile = $this->createMock(UploadedFileInterface::class);

        $request = $this->getRequest(
            [
                'duplicate'=> $this->getRequest(
                    [],
                    [
                        'files' => [
                            'all' => [
                                $newUploadedFile
                            ]
                        ]
                    ]
                )
            ],
            [
                'files' => [
                    'all' => [
                        $uploadedFile
                    ]
                ]
            ]
        );

        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);
        $instance = new HttpRequestServerAdapter($request, $fileFactory);

        $newInstance = $instance->withUploadedFiles([$newUploadedFile]);

        $reflex = new \ReflectionProperty(HttpRequestServerAdapter::class, 'httpRequest');
        $reflex->setAccessible(true);

        $originalHttpRequest = $reflex->getValue($instance);
        $newHttpRequest = $reflex->getValue($newInstance);
        $this->getTestCase()->assertNotSame($originalHttpRequest, $newHttpRequest);
        $this->getTestCase()->assertSame([$uploadedFile], $originalHttpRequest->files->all());
        $this->getTestCase()->assertSame([$newUploadedFile], $newHttpRequest->files->all());
    }

    /**
     * Test getUploadedFiles
     *
     * This method validate the HttpRequestServerAdapter::getUploadedFiles method
     *
     * @return void
     */
    public function testGetUploadedFiles()
    {
        $uploadedFile = $this->getTestCase()->getMockBuilder(UploadedFileInterface::class)
            ->getMock();

        $request = $this->getRequest([], ['files' => ['all' => [$uploadedFile]]]);

        $fileFactory = $this->createMock(EmbeddedFileFactoryInterface::class);
        $fileFactory->expects($this->getTestCase()->once())
            ->method('getUploadFile')
            ->willReturn($uploadedFile);
        $instance = new HttpRequestServerAdapter($request, $fileFactory);

        $this->getTestCase()->assertEquals([$uploadedFile], $instance->getUploadedFiles());
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
