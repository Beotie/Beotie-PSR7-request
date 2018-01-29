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
namespace Beotie\PSR7\Request\HttpComponent;

use Symfony\Component\HttpFoundation\Request;
use Beotie\PSR7\Request\File\Factory\EmbeddedFileFactoryInterface;
use Beotie\PSR7\Request\File\RequestUploadedFileAdapter;
use Beotie\PSR7\Request\HttpRequestServerAdapter;

/**
 * File component
 *
 * This trait is used to implement the PSR7 over symfony Request instance and manange file part
 *
 * @category Request
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait FileComponent
{
    /**
     * Http request
     *
     * The base symfony http request
     *
     * @var Request
     */
    private $httpRequest;

    /**
     * File factory
     *
     * This method store the factory used to convert uploaded files.
     *
     * @var EmbeddedFileFactoryInterface
     */
    private $fileFactory;

    /**
     * Create a new instance with the specified uploaded files.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param array $uploadedFiles An array tree of UploadedFileInterface instances.
     *
     * @throws \InvalidArgumentException if an invalid structure is provided.
     * @return static
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        return $this->duplicate(['files' => $uploadedFiles], true);
    }

    /**
     * Retrieve normalized file upload data.
     *
     * This method returns upload metadata in a normalized tree, with each leaf
     * an instance of Psr\Http\Message\UploadedFileInterface.
     *
     * These values MAY be prepared from $_FILES or the message body during
     * instantiation, or MAY be injected via withUploadedFiles().
     *
     * @return array An array tree of UploadedFileInterface instances; an empty
     *     array MUST be returned if no data is present.
     */
    public function getUploadedFiles()
    {
        return $this->filesToUploadedStream($this->httpRequest->files->all());
    }

    /**
     * Files to uploaded stream
     *
     * Convert an uploaded file array to a request uploaded file adapter array
     *
     * @param array $files The array of UploadedFile
     *
     * @return RequestUploadedFileAdapter[]
     */
    protected function filesToUploadedStream(array $files) : array
    {
        $resultFiles = [];

        foreach ($files as $key => $file) {
            $resultFiles[$key] = $this->fileFactory->getUploadFile(
                [
                    $this->fileFactory->getArrayKey() => $file
                ]
            );
        }

        return $resultFiles;
    }

    /**
     * Duplicate
     *
     * This method duplicate the current request and override the specified parameters
     *
     * @param array $param The parameters to override
     * @param bool  $force Hard replace the parameter, act as replace completely
     *
     * @return HttpRequestServerAdapter
     */
    protected abstract function duplicate(array $param = [], bool $force = false) : HttpRequestServerAdapter;
}
