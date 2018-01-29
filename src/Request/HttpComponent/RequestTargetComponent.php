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
 * Request target component
 *
 * This trait is used to implement the PSR7 over symfony Request instance and manange request target part
 *
 * @category Request
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait RequestTargetComponent
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
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     *
     * @return string
     */
    public function getRequestTarget()
    {
        return $this->httpRequest->getRequestUri();
    }

    /**
     * Return an instance with the specific request-target.
     *
     * If the request needs a non-origin-form request-target — e.g., for
     * specifying an absolute-form, authority-form, or asterisk-form —
     * this method may be used to create an instance with the specified
     * request-target, verbatim.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request target.
     *
     * @param mixed $requestTarget the request target
     *
     * @link                                 http://tools.ietf.org/html/rfc7230#section-5.3 (for the various
     *     request-target forms allowed in request messages)
     * @return                               static
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function withRequestTarget($requestTarget)
    {
        $parameters = array_merge($this->httpRequest->query->all(), $this->httpRequest->request->all());
        $httpRequest = Request::create(
            $requestTarget,
            $this->httpRequest->getMethod(),
            $parameters,
            $this->httpRequest->cookies->all(),
            $this->httpRequest->files->all(),
            $this->httpRequest->server->all(),
            $this->httpRequest->getContent()
        );

        return new static($httpRequest, $this->fileFactory);
    }
}
