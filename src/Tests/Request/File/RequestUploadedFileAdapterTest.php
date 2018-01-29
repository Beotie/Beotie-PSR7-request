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
namespace Beotie\PSR7\Tests\Request\File;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Beotie\PSR7\Stream\Factory\EmbeddedStreamFactoryInterface;
use Beotie\PSR7\Request\File\RequestUploadedFileAdapter;
use Psr\Http\Message\StreamInterface;
use Beotie\PSR7\Stream\Factory\FileInfoStreamFactory;
use Beotie\PSR7\Stream\Factory\AbstractEmbeddedStreamFactory;

/**
 * RequestUploadedFileAdapter test
 *
 * This class is used to validate the RequestUploadedFileAdapter class
 *
 * @category                                     Test
 * @package                                      Beotie_Core_Bundle
 * @author                                       matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license                                      MIT <https://opensource.org/licenses/MIT>
 * @link                                         http://cscfa.fr
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class RequestUploadedFileAdapterTest extends TestCase
{
    /**
     * File size
     *
     * This constant define the test file size
     *
     * @var integer
     */
    private const FILE_SIZE = 1024;

    /**
     * File path
     *
     * This property store the test file path
     *
     * @var string
     */
    private $filePath;

    /**
     * Test construct
     *
     * This method validate the GenericRequestBag::__construct method
     *
     * @return void
     */
    public function testConstruct() : void
    {
        $uploadedFile = new UploadedFile($this->filePath, basename($this->filePath));
        $streamFactory = $this->createMock(AbstractEmbeddedStreamFactory::class);

        $instance = new RequestUploadedFileAdapter($uploadedFile, $streamFactory);

        $this->assertSame(
            $uploadedFile,
            $this->getUploadedFileProperty()
                ->getValue($instance),
            sprintf(
                'The "%s::__construct" method is expected to set the "uploadedFile" property',
                RequestUploadedFileAdapter::class
            )
        );

        $this->assertSame(
            $streamFactory,
            $this->getStreamFactoryProperty()
                ->getValue($instance),
            sprintf(
                'The "%s::__construct" method is expected to set the "streamFactory" property',
                RequestUploadedFileAdapter::class
            )
        );

        return;
    }

    /**
     * Test getSize
     *
     * This method validate the GenericRequestBag::getSize method
     *
     * @return void
     */
    public function testGetSize() : void
    {
        $uploadedFile = new UploadedFile($this->filePath, basename($this->filePath));
        $streamFactory = $this->createMock(AbstractEmbeddedStreamFactory::class);

        $instance = new RequestUploadedFileAdapter($uploadedFile, $streamFactory);

        $this->assertEquals(
            self::FILE_SIZE,
            $instance->getSize(),
            sprintf(
                'The "%s::getSize" is expected to return the UploadedFile file size',
                RequestUploadedFileAdapter::class
            )
        );

        return;
    }

    /**
     * Test getClientFilename
     *
     * This method validate the GenericRequestBag::getClientFilename method
     *
     * @return void
     */
    public function testGetClientFilename() : void
    {
        $uploadedFile = new UploadedFile($this->filePath, basename($this->filePath));
        $streamFactory = $this->createMock(AbstractEmbeddedStreamFactory::class);

        $instance = new RequestUploadedFileAdapter($uploadedFile, $streamFactory);

        $this->assertEquals(
            basename($this->filePath),
            $instance->getClientFilename(),
            sprintf(
                'The "%s::getSize" is expected to return the UploadedFile original file name',
                RequestUploadedFileAdapter::class
            )
        );

        return;
    }

    /**
     * Test getError
     *
     * This method validate the GenericRequestBag::getError method
     *
     * @return void
     */
    public function testGetError() : void
    {
        $uploadedFile = new UploadedFile($this->filePath, basename($this->filePath));
        $streamFactory = $this->createMock(AbstractEmbeddedStreamFactory::class);

        $instance = new RequestUploadedFileAdapter($uploadedFile, $streamFactory);

        $this->assertEquals(
            UPLOAD_ERR_OK,
            $instance->getError(),
            sprintf(
                'The "%s::getError" is expected to return the UploadedFile errors',
                RequestUploadedFileAdapter::class
            )
        );

        return;
    }

    /**
     * Test getStream
     *
     * This method validate the GenericRequestBag::getStream method
     *
     * @return                               void
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testGetStream() : void
    {
        $uploadedFile = new UploadedFile($this->filePath, basename($this->filePath));
        $streamFactory = $this->createMock(AbstractEmbeddedStreamFactory::class);
        $stream = $this->createMock(StreamInterface::class);

        $callbackClosure = \Closure::fromCallable([$this, 'streamFactoryArgumentTest']);
        $streamFactory->expects($this->once())
            ->method('getStream')
            ->with($this->callback($callbackClosure))
            ->willReturn($stream);

        $instance = new RequestUploadedFileAdapter($uploadedFile, $streamFactory);

        $this->assertSame(
            $stream,
            $instance->getStream(),
            sprintf(
                'The "%s::getStream" method is expected to return the result of the "%s::getStream" method',
                RequestUploadedFileAdapter::class,
                EmbeddedStreamFactoryInterface::class
            )
        );

        return;
    }

    /**
     * Test getStream failure
     *
     * This method validate the GenericRequestBag::getStream method in case of unresolved fileUpload
     *
     * @return void
     */
    public function testGetStreamFailure() : void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No available file or already moved');

        $reflex = new \ReflectionClass(RequestUploadedFileAdapter::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $instance->getStream();

        return;
    }

    /**
     * Test getStream moved
     *
     * This method validate the GenericRequestBag::getStream method in case of moved fileUpload
     *
     * @return void
     */
    public function testGetStreamMoved() : void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No available file or already moved');

        $uploadedFile = new UploadedFile($this->filePath, basename($this->filePath));
        $streamFactory = $this->createMock(AbstractEmbeddedStreamFactory::class);
        $instance = new RequestUploadedFileAdapter($uploadedFile, $streamFactory);

        $propertyReflex = new \ReflectionProperty(RequestUploadedFileAdapter::class, 'isMoved');
        $propertyReflex->setAccessible(true);
        $propertyReflex->setValue($instance, true);

        $instance->getStream();

        return;
    }

    /**
     * Test stream factory argument
     *
     * This method is used to validate the getStream method argument
     *
     * @param array $value The argument value
     *
     * @return                               bool
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function streamFactoryArgumentTest(array $value) : bool
    {
        $this->assertTrue(is_array($value));
        $this->assertArrayHasKey(AbstractEmbeddedStreamFactory::getArrayKey(), $value);
        $this->assertInstanceOf(\SplFileObject::class, $value[FileInfoStreamFactory::getArrayKey()]);

        return true;
    }

    /**
     * Test getClientMediaType moved
     *
     * This method validate the GenericRequestBag::getClientMediaType method
     *
     * @return void
     */
    public function testGetClientMediaType() : void
    {
        $uploadedFile = $this->createMock(UploadedFile::class);
        $uploadedFile->expects($this->once())
            ->method('getType')
            ->willReturn('text/html');

        $streamFactory = $this->createMock(AbstractEmbeddedStreamFactory::class);

        $instance = new RequestUploadedFileAdapter($uploadedFile, $streamFactory);

        $this->assertEquals('text/html', $instance->getClientMediaType());

        return;
    }

    /**
     * Test getClientMediaType moved
     *
     * This method validate the GenericRequestBag::getClientMediaType method
     *
     * @return void
     */
    public function testGetClientMediaTypeFailure() : void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No available file or already moved');

        $reflex = new \ReflectionClass(RequestUploadedFileAdapter::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $instance->getClientMediaType();
    }

    /**
     * Test moveTo directory
     *
     * This method validate the GenericRequestBag::moveTo method in case of directory target
     *
     * @return void
     */
    public function testMoveToDir() : void
    {
        $uploadedFile = $this->createMock(UploadedFile::class);
        $uploadedFile->expects($this->once())
            ->method('move')
            ->with($this->equalTo(sys_get_temp_dir()));

        $streamFactory = $this->createMock(AbstractEmbeddedStreamFactory::class);

        $instance = new RequestUploadedFileAdapter($uploadedFile, $streamFactory);
        $instance->moveTo(sys_get_temp_dir());

        return;
    }

    /**
     * Test moveTo file
     *
     * This method validate the GenericRequestBag::moveTo method in case of file target
     *
     * @return void
     */
    public function testMoveToFile() : void
    {
        $directory = sys_get_temp_dir();
        $file = 'test.xml';
        $target = $directory.DIRECTORY_SEPARATOR.$file;

        $uploadedFile = $this->createMock(UploadedFile::class);
        $uploadedFile->expects($this->once())
            ->method('move')
            ->with($this->equalTo($directory), $this->equalTo($file));

        $streamFactory = $this->createMock(AbstractEmbeddedStreamFactory::class);

        $instance = new RequestUploadedFileAdapter($uploadedFile, $streamFactory);
        $instance->moveTo($target);

        return;
    }

    /**
     * Test moveTo undefined file
     *
     * This method validate the GenericRequestBag::moveTo method in case of unexisting UploadedFile
     *
     * @return void
     */
    public function testMoveToUndefined() : void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No available file or already moved');

        $reflex = new \ReflectionClass(RequestUploadedFileAdapter::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $instance->moveTo(sys_get_temp_dir());

        return;
    }

    /**
     * Test moveTo moved
     *
     * This method validate the GenericRequestBag::moveTo method in case of UploadedFile already moved
     *
     * @return void
     */
    public function testMoveToMoved() : void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No available file or already moved');

        $uploadedFile = new UploadedFile($this->filePath, basename($this->filePath));
        $streamFactory = $this->createMock(AbstractEmbeddedStreamFactory::class);
        $instance = new RequestUploadedFileAdapter($uploadedFile, $streamFactory);

        $propertyReflex = new \ReflectionProperty(RequestUploadedFileAdapter::class, 'isMoved');
        $propertyReflex->setAccessible(true);
        $propertyReflex->setValue($instance, true);

        $instance->moveTo(sys_get_temp_dir());

        return;
    }

    /**
     * Set up
     *
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->filePath = tempnam(sys_get_temp_dir(), 'TEST');

        $fileStream = fopen($this->filePath, 'r+');
        ftruncate($fileStream, self::FILE_SIZE);
        fclose($fileStream);
    }

    /**
     * Tear down
     *
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    protected function tearDown()
    {
        unlink($this->filePath);
    }

    /**
     * Get stream factory property
     *
     * This method return the streamFactory property to be used by the tests
     *
     * @return \ReflectionProperty
     */
    private function getStreamFactoryProperty() : \ReflectionProperty
    {
        $property = new \ReflectionProperty(RequestUploadedFileAdapter::class, 'streamFactory');
        $property->setAccessible(true);

        return $property;
    }

    /**
     * Get uploaded file property
     *
     * This method return the uploadedFile property to be used by the tests
     *
     * @return \ReflectionProperty
     */
    private function getUploadedFileProperty() : \ReflectionProperty
    {
        $property = new \ReflectionProperty(RequestUploadedFileAdapter::class, 'uploadedFile');
        $property->setAccessible(true);

        return $property;
    }
}
