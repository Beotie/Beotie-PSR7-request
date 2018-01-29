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

/**
 * Method component
 *
 * This trait is used to implement the PSR7 over symfony Request instance and manange method part
 *
 * @category Request
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait MethodComponent
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
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod()
    {
        return $this->httpRequest->getMethod();
    }

    /**
     * Return an instance with the provided HTTP method.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request method.
     *
     * @param string $method Case-sensitive method.
     *
     * @throws                               \InvalidArgumentException for invalid HTTP methods.
     * @return                               static
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function withMethod($method)
    {
        $httpRequest = $this->httpRequest;
        $parameters = array_merge($httpRequest->query->all(), $httpRequest->request->all());

        return new static(
            Request::create(
                $httpRequest->getRequestUri(),
                $method,
                $parameters,
                $httpRequest->cookies->all(),
                $httpRequest->files->all(),
                $httpRequest->server->all(),
                $httpRequest->getContent()
            ),
            $this->fileFactory
        );
    }
}
