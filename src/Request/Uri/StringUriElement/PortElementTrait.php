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
namespace Beotie\PSR7\Request\Uri\StringUriElement;

use Beotie\PSR7\Request\Uri\StringUri;

/**
 * Port element trait
 *
 * This trait is use for decoupling the port part of the StringUri from the main class
 *
 * @category Request
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait PortElementTrait
{
    /**
     * Port
     *
     * This property store the port part of the url
     *
     * @var integer|null
     */
    protected $port = null;

    /**
     * Return an instance with the specified port.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified port.
     *
     * Implementations MUST raise an exception for ports outside the
     * established TCP and UDP port ranges.
     *
     * A null value provided for the port is equivalent to removing the port
     * information.
     *
     * @param null|int $port The port to use with the new instance; a null value
     *                       removes the port information.
     *
     * @return static A new instance with the specified port.
     * @throws \InvalidArgumentException for invalid ports.
     */
    public function withPort($port)
    {
        if ($port > 65535 || $port < 0 || !is_int($port)) {
            throw new \RuntimeException('Out of TCP/UDP allowed range');
        }

        return $this->duplicateWith('port', $port);
    }

    /**
     * Retrieve the port component of the URI.
     *
     * If a port is present, and it is non-standard for the current scheme,
     * this method MUST return it as an integer. If the port is the standard port
     * used with the current scheme, this method SHOULD return null.
     *
     * If no port is present, and no scheme is present, this method MUST return
     * a null value.
     *
     * If no port is present, but a scheme is present, this method MAY return
     * the standard port for that scheme, but SHOULD return null.
     *
     * @return null|int The URI port.
     */
    public function getPort()
    {
        if ($this->port !== $this->getStandardSchemePort($this->scheme)) {
            return intval($this->port);
        }

        return null;
    }

    /**
     * Get standard scheme port
     *
     * This method return the standard scheme port for a given scheme
     *
     * @param mixed $scheme The scheme to retreive
     *
     * @return string|NULL
     */
    protected abstract function getStandardSchemePort(?string $scheme) : ?int;

    /**
     * Duplicate with
     *
     * This method duplicate the current object and update a property with a given value before returning it
     *
     * @param string $property The property name to update
     * @param mixed  $value    The new property value
     *
     * @return StringUri
     */
    protected abstract function duplicateWith(string $property, $value) : StringUri;
}
