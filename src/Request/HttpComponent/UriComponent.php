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

use Psr\Http\Message\UriInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Uri component
 *
 * This trait is used to implement the PSR7 over symfony Request instance and manange uri part
 *
 * @category Request
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait UriComponent
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
     * Returns an instance with the provided URI.
     *
     * This method MUST update the Host header of the returned request by
     * default if the URI contains a host component. If the URI does not
     * contain a host component, any pre-existing Host header MUST be carried
     * over to the returned request.
     *
     * You can opt-in to preserving the original state of the Host header by
     * setting `$preserveHost` to `true`. When `$preserveHost` is set to
     * `true`, this method interacts with the Host header in the following ways:
     *
     * - If the Host header is missing or empty, and the new URI contains
     *   a host component, this method MUST update the Host header in the returned
     *   request.
     * - If the Host header is missing or empty, and the new URI does not contain a
     *   host component, this method MUST NOT update the Host header in the returned
     *   request.
     * - If a Host header is present and non-empty, this method MUST NOT update
     *   the Host header in the returned request.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new UriInterface instance.
     *
     * @param UriInterface $uri          New request URI to use.
     * @param bool         $preserveHost Preserve the original state of the Host header.
     *
     * @link                                 http://tools.ietf.org/html/rfc3986#section-4.3
     * @return                               static
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $parameters = array_merge($this->httpRequest->query->all(), $this->httpRequest->request->all());
        $httpRequest = Request::create(
            (string)$uri,
            $this->httpRequest->getMethod(),
            $parameters,
            $this->httpRequest->cookies->all(),
            $this->httpRequest->files->all(),
            $this->httpRequest->server->all(),
            $this->httpRequest->getContent()
        );

        $this->updateHostFromUri($httpRequest, $uri, !$preserveHost);

        return new static($httpRequest, $this->fileFactory);
    }

    /**
     * Retrieves the URI instance.
     *
     * This method MUST return a UriInterface instance.
     *
     * @link   http://tools.ietf.org/html/rfc3986#section-4.3
     * @return UriInterface Returns a UriInterface instance
     *     representing the URI of the request.
     */
    public function getUri()
    {
        return $this->httpRequest->getUri();
    }

    /**
     * Update host from URI
     *
     * This method update the request, accordingly with the given uri host if exist. If the request already
     * have defined a host, this method does not update the request with the uri host as long as the force
     * parameter is set to false.
     *
     * @param Request      $request The request to update
     * @param UriInterface $uri     The uri whence extract the host
     * @param bool         $force   Indicate the request header MUST be override if the uri contain host
     *
     * @return void
     */
    private function updateHostFromUri(Request $request, UriInterface $uri, bool $force) : void
    {
        if (!$force) {
            $request->headers->set('HOST', $this->httpRequest->getHost());
            return;
        }

        $uriHostComponent = $uri->getHost();
        if (empty($uriHostComponent)) {
            return;
        }

        $host = $this->httpRequest->getHost();

        if (empty($host) && !empty($uriHostComponent)) {
            $request->headers->set('HOST', $uriHostComponent);
            return;
        }

        if (!empty($uriHostComponent)) {
            $request->headers->set('HOST', $uriHostComponent);
        }

        return;
    }
}
