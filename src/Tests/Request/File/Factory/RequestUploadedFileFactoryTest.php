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
namespace Beotie\PSR7\Tests\Request\File\Factory;

use PHPUnit\Framework\TestCase;
use Beotie\PSR7\Request\File\Factory\RequestUploadedFileFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Beotie\PSR7\Request\File\RequestUploadedFileAdapter;
use Beotie\PSR7\Stream\Factory\AbstractEmbeddedStreamFactory;

/**
 * RequestUploadedFileFactory test
 *
 * This class is used to validate the RequestUploadedFileFactory class
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
class RequestUploadedFileFactoryTest extends TestCase
{
    /**
     * Test getArrayKey
     *
     * This method is used to validate the FileInfoStreamFactory::getArrayKey method
     *
     * @return void
     */
    public function testGetArrayKey() : void
    {
        $streamFactory = $this->createMock(AbstractEmbeddedStreamFactory::class);
        $instance = new RequestUploadedFileFactory($streamFactory);

        $this->assertEquals(
            'inner_content',
            $instance->getArrayKey()
        );

        return;
    }

    /**
     * Test getUploadFile
     *
     * This method is used to validate the FileInfoStreamFactory::getUploadFile method
     *
     * @return void
     */
    public function testGetUploadFile() : void
    {
        $uploadedFile = $this->createMock(UploadedFile::class);
        $streamFactory = $this->createMock(AbstractEmbeddedStreamFactory::class);
        $instance = new RequestUploadedFileFactory($streamFactory);

        $this->assertEquals(
            new RequestUploadedFileAdapter($uploadedFile, $streamFactory),
            $instance->getUploadFile([$instance->getArrayKey() => $uploadedFile])
        );
    }

    /**
     * Test getUploadFile type error
     *
     * This method is used to validate the FileInfoStreamFactory::getUploadFile method in case of wrong type usage
     *
     * @return void
     */
    public function testGetUploadedFileTypeError() : void
    {
        $this->expectException(\RuntimeException::class);
        $message = sprintf(
            'The "%s::%s" expect the file array key to be an instance of "%s". "%s" given',
            RequestUploadedFileFactory::class,
            'getUploadFile',
            UploadedFile::class,
            \stdClass::class
        );
        $this->expectExceptionMessage($message);

        $uploadedFile = new \stdClass();
        $streamFactory = $this->createMock(AbstractEmbeddedStreamFactory::class);
        $instance = new RequestUploadedFileFactory($streamFactory);

        $instance->getUploadFile([$instance->getArrayKey() => $uploadedFile]);

        return;
    }

    /**
     * Test getUploadFile key error
     *
     * This method is used to validate the FileInfoStreamFactory::getUploadFile method in case of wrong key usage
     *
     * @return void
     */
    public function testGetUploadedFileKeyError() : void
    {
        $this->expectException(\RuntimeException::class);
        $message = sprintf(
            'The "%s::%s" expect a file array key to build an instance of RequestUploadedFileAdapter',
            RequestUploadedFileFactory::class,
            'getUploadFile'
        );
        $this->expectExceptionMessage($message);

        $uploadedFile = $this->createMock(UploadedFile::class);
        $streamFactory = $this->createMock(AbstractEmbeddedStreamFactory::class);
        $instance = new RequestUploadedFileFactory($streamFactory);

        $instance->getUploadFile(['__wrong_key__' => $uploadedFile]);

        return;
    }
}
