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
 * @category Request
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
namespace Beotie\PSR7\Request\File;

use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Psr\Http\Message\StreamInterface;
use Beotie\PSR7\Stream\Factory\AbstractEmbeddedStreamFactory;

/**
 * Request uploaded file adapter
 *
 * This class is used as uploaded file adapter to implement PSR7\UploadedFileInterface
 *
 * @category Request
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
class RequestUploadedFileAdapter implements UploadedFileInterface
{
    /**
     * Error message
     *
     * This constant define the method error message
     *
     * @var string
     */
    public const ERROR_MESSAGE = 'No available file or already moved';

    /**
     * Uploaded file
     *
     * This property store the inner uploaded file
     *
     * @var UploadedFile
     */
    private $uploadedFile;

    /**
     * Stream factory
     *
     * This property store the Stream factory in charge of the Stream building
     *
     * @var AbstractEmbeddedStreamFactory
     */
    private $streamFactory;

    /**
     * Is moved
     *
     * This property store the moved state of the file
     *
     * @var bool
     */
    private $isMoved = false;

    /**
     * Construct
     *
     * The default RequestUploadedFileAdapter constructor
     *
     * @param UploadedFile                  $uploadedFile  The inner uploaded file
     * @param AbstractEmbeddedStreamFactory $streamFactory The stream instance factory
     *
     * @return void
     */
    public function __construct(UploadedFile $uploadedFile, AbstractEmbeddedStreamFactory $streamFactory)
    {
        $this->uploadedFile = $uploadedFile;
        $this->streamFactory = $streamFactory;
    }

    /**
     * Retrieve the error associated with the uploaded file.
     *
     * The return value MUST be one of PHP's UPLOAD_ERR constants, such as :
     * <ul>
     *     <li>UPLOAD_ERR_OK</li>
     *     <li>UPLOAD_ERR_INI_SIZE'</li>
     *     <li>UPLOAD_ERR_FORM_SIZE'</li>
     *     <li>UPLOAD_ERR_PARTIAL'</li>
     *     <li>UPLOAD_ERR_NO_FILE'</li>
     *     <li>UPLOAD_ERR_NO_TMP_DIR'</li>
     *     <li>UPLOAD_ERR_CANT_WRITE'</li>
     *     <li>UPLOAD_ERR_EXTENSION'</li>
     * </ul>
     *
     * If the file was uploaded successfully, this method MUST return
     * UPLOAD_ERR_OK.
     *
     * Implementations SHOULD return the value stored in the "error" key of
     * the file in the $_FILES array.
     *
     * @see    http://php.net/manual/en/features.file-upload.errors.php
     * @return int One of PHP's UPLOAD_ERR constants.
     */
    public function getError()
    {
        return $this->uploadedFile->getError();
    }

    /**
     * Retrieve the file size.
     *
     * Implementations SHOULD return the value stored in the "size" key of
     * the file in the $_FILES array if available, as PHP calculates this based
     * on the actual size transmitted.
     *
     * @return int|null The file size in bytes or null if unknown.
     */
    public function getSize()
    {
        return $this->uploadedFile->getSize();
    }

    /**
     * Retrieve the filename sent by the client.
     *
     * Do not trust the value returned by this method. A client could send
     * a malicious filename with the intention to corrupt or hack your
     * application.
     *
     * Implementations SHOULD return the value stored in the "name" key of
     * the file in the $_FILES array.
     *
     * @return string|null The filename sent by the client or null if none
     *     was provided.
     */
    public function getClientFilename()
    {
        return $this->uploadedFile->getClientOriginalName();
    }

    /**
     * Retrieve a stream representing the uploaded file.
     *
     * This method MUST return a StreamInterface instance, representing the
     * uploaded file. The purpose of this method is to allow utilizing native PHP
     * stream functionality to manipulate the file upload, such as
     * stream_copy_to_stream() (though the result will need to be decorated in a
     * native PHP stream wrapper to work with such functions).
     *
     * If the moveTo() method has been called previously, this method MUST raise
     * an exception.
     *
     * @return                               StreamInterface Stream representation of the uploaded file.
     * @throws                               \RuntimeException in cases when no stream is available or can be
     *     created.
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function getStream()
    {
        if (!$this->uploadedFile || $this->isMoved) {
            throw new \RuntimeException(self::ERROR_MESSAGE);
        }

        return $this->streamFactory->getStream(
            [
                AbstractEmbeddedStreamFactory::getArrayKey() => $this->uploadedFile->openFile()
            ]
        );
    }

    /**
     * Retrieve the media type sent by the client.
     *
     * Do not trust the value returned by this method. A client could send
     * a malicious media type with the intention to corrupt or hack your
     * application.
     *
     * Implementations SHOULD return the value stored in the "type" key of
     * the file in the $_FILES array.
     *
     * @return string|null The media type sent by the client or null if none
     *     was provided.
     */
    public function getClientMediaType()
    {
        if (!$this->uploadedFile) {
            throw new \RuntimeException(self::ERROR_MESSAGE);
        }

        return $this->uploadedFile->getType();
    }

    /**
     * Move the uploaded file to a new location.
     *
     * Use this method as an alternative to move_uploaded_file(). This method is
     * guaranteed to work in both SAPI and non-SAPI environments.
     * Implementations must determine which environment they are in, and use the
     * appropriate method (move_uploaded_file(), rename(), or a stream
     * operation) to perform the operation.
     *
     * $targetPath may be an absolute path, or a relative path. If it is a
     * relative path, resolution should be the same as used by PHP's rename()
     * function.
     *
     * The original file or stream MUST be removed on completion.
     *
     * If this method is called more than once, any subsequent calls MUST raise
     * an exception.
     *
     * When used in an SAPI environment where $_FILES is populated, when writing
     * files via moveTo(), is_uploaded_file() and move_uploaded_file() SHOULD be
     * used to ensure permissions and upload status are verified correctly.
     *
     * If you wish to move to a stream, use getStream(), as SAPI operations
     * cannot guarantee writing to stream destinations.
     *
     * @param string $targetPath Path to which to move the uploaded file.
     *
     * @see    http://php.net/is_uploaded_file
     * @see    http://php.net/move_uploaded_file
     * @throws \InvalidArgumentException if the $targetPath specified is invalid.
     * @throws \RuntimeException on any error during the move operation, or on
     *     the second or subsequent call to the method.
     * @return void
     */
    public function moveTo($targetPath)
    {
        if (!$this->uploadedFile || $this->isMoved) {
            throw new \RuntimeException(self::ERROR_MESSAGE);
        }

        $this->isMoved = true;
        if (!is_dir($targetPath)) {
            $dirname = dirname($targetPath);
            $this->uploadedFile->move($dirname, basename($targetPath));
            $this->uploadedFile = null;

            return;
        }

        $this->uploadedFile->move($targetPath);
        $this->uploadedFile = null;

        return;
    }
}
