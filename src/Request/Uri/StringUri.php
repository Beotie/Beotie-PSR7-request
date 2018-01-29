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
namespace Beotie\PSR7\Request\Uri;

use Psr\Http\Message\UriInterface;
use Beotie\PSR7\Request\Uri\StringUriElement\AuthorityElementTrait;
use Beotie\PSR7\Request\Uri\StringUriElement\SchemeElementTrait;
use Beotie\PSR7\Request\Uri\StringUriElement\PathElementTrait;
use Beotie\PSR7\Request\Uri\StringUriElement\QueryElementTrait;
use Beotie\PSR7\Request\Uri\StringUriElement\FragmentElementTrait;
use Beotie\PSR7\Request\Uri\StringUriElement\HostElementTrait;
use Beotie\PSR7\Request\Uri\StringUriElement\PortElementTrait;
use Beotie\PSR7\Request\Uri\StringUriElement\UserInfoElementTrait;

/**
 * String uri
 *
 * This class is used as UriInterface implemantation, based on a string url.
 *
 * @category Request
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
class StringUri implements UriInterface, PortMappingInterface
{
    use AuthorityElementTrait,
        SchemeElementTrait,
        PathElementTrait,
        QueryElementTrait,
        FragmentElementTrait,
        HostElementTrait,
        PortElementTrait,
        UserInfoElementTrait;

    /**
     * Construct
     *
     * The default StringUri constructor
     *
     * @param string $uri The string representation of the URI
     *
     * @return void
     */
    public function __construct(string $uri)
    {
        $components = parse_url($uri);

        $componentKeys = [
            'scheme',
            'path',
            'query',
            'fragment',
            'host',
            'port',
            'user',
            'pass'
        ];

        foreach ($componentKeys as $component) {
            if (!empty($components[$component])) {
                $this->{$component} = $components[$component];
            }
        }
    }

    /**
     * Return the string representation as a URI reference.
     *
     * Depending on which components of the URI are present, the resulting
     * string is either a full URI or relative reference according to RFC 3986,
     * Section 4.1. The method concatenates the various components of the URI,
     * using the appropriate delimiters:
     *
     * - If a scheme is present, it MUST be suffixed by ":".
     * - If an authority is present, it MUST be prefixed by "//".
     * - The path can be concatenated without delimiters. But there are two
     *   cases where the path has to be adjusted to make the URI reference
     *   valid as PHP does not allow to throw an exception in __toString():
     *     - If the path is rootless and an authority is present, the path MUST
     *       be prefixed by "/".
     *     - If the path is starting with more than one "/" and no authority is
     *       present, the starting slashes MUST be reduced to one.
     * - If a query is present, it MUST be prefixed by "?".
     * - If a fragment is present, it MUST be prefixed by "#".
     *
     * @see    http://tools.ietf.org/html/rfc3986#section-4.1
     * @return string
     */
    public function __toString()
    {
        $query = $this->getQuery();
        if ($query) {
            $query = sprintf('?%s', $query);
        }

        $fragment = $this->getFragment();
        if ($fragment) {
            $fragment = sprintf('#%s', $fragment);
        }

        $base = sprintf('%s%s%s', $this->getSchemeElement(), $this->getAuthorityPart(), $this->getPath());
        $complement = sprintf('%s%s', $query, $fragment);

        return sprintf('%s%s', $base, $complement);
    }

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
    protected function duplicateWith(string $property, $value) : StringUri
    {
        $instance = clone $this;
        $instance->{$property} = $value;
        return $instance;
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
    protected function getStandardSchemePort(?string $scheme) : ?int
    {
        if (!empty($scheme) && isset(self::MAPPING[strtolower($scheme)])) {
            return self::MAPPING[strtolower($scheme)];
        }

        return null;
    }

    /**
     * Get scheme element
     *
     * This method return the scheme element of the url
     *
     * @return string
     */
    private function getSchemeElement() : string
    {
        $scheme = $this->scheme;
        if (!empty($scheme)) {
            $scheme .= ':';
        }

        return $scheme;
    }
}
