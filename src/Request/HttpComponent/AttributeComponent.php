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
use Beotie\PSR7\Request\HttpRequestServerAdapter;

/**
 * Attribute component
 *
 * This trait is used to implement the PSR7 over symfony Request instance and manange attributes part
 *
 * @category Request
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait AttributeComponent
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
     * Return an instance with the specified derived request attribute.
     *
     * This method allows setting a single derived request attribute as
     * described in getAttributes().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated attribute.
     *
     * @param string $name  The attribute name.
     * @param mixed  $value The value of the attribute.
     *
     * @see    getAttributes()
     * @return static
     */
    public function withAttribute($name, $value)
    {
        return $this->duplicate(['attributes' => [$name => $value]]);
    }

    /**
     * Retrieve a single derived request attribute.
     *
     * Retrieves a single derived request attribute as described in
     * getAttributes(). If the attribute has not been previously set, returns
     * the default value as provided.
     *
     * This method obviates the need for a hasAttribute() method, as it allows
     * specifying a default value to return if the attribute is not found.
     *
     * @param string $name    The attribute name.
     * @param mixed  $default Default value to return if the attribute does not exist.
     *
     * @see    getAttributes()
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        return $this->httpRequest->get($name, $default);
    }

    /**
     * Return an instance that removes the specified derived request attribute.
     *
     * This method allows removing a single derived request attribute as
     * described in getAttributes().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the attribute.
     *
     * @param string $name The attribute name.
     *
     * @see    getAttributes()
     * @return static
     */
    public function withoutAttribute($name)
    {
        $httpRequest = clone $this->httpRequest;

        $httpRequest->attributes->remove($name);

        return new static($httpRequest);
    }

    /**
     * Retrieve attributes derived from the request.
     *
     * The request "attributes" may be used to allow injection of any
     * parameters derived from the request: e.g., the results of path
     * match operations; the results of decrypting cookies; the results of
     * deserializing non-form-encoded message bodies; etc. Attributes
     * will be application and request specific, and CAN be mutable.
     *
     * @return array Attributes derived from the request.
     */
    public function getAttributes()
    {
        return $this->httpRequest->attributes->all();
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
