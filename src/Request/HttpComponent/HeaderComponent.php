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
 * Header component
 *
 * This trait is used to implement the PSR7 over symfony Request instance and manange header part
 *
 * @category Request
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait HeaderComponent
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
     * Return an instance without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the named header.
     *
     * @param string $name Case-insensitive header field name to remove.
     *
     * @return static
     */
    public function withoutHeader($name)
    {
        $httpRequest = $this->requestDuplicate();

        if ($httpRequest->headers->has($name)) {
            $httpRequest->headers->remove($name);
        }

        return new static($httpRequest, $this->fileFactory);
    }

    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use getHeader() instead
     * and supply your own delimiter when concatenating.
     *
     * If the header does not appear in the message, this method MUST return
     * an empty string.
     *
     * @param string $name Case-insensitive header field name.
     *
     * @return string A string of values as provided for the given header
     *    concatenated together using a comma. If the header does not appear in
     *    the message, this method MUST return an empty string.
     */
    public function getHeaderLine($name)
    {
        $result = '';

        $name = strtoupper($name);

        $headers = $this->httpRequest->headers->all();
        $headers = array_change_key_case($headers, CASE_UPPER);

        if (in_array($name, array_keys($headers))) {
            $result = $headers[$name];

            if (is_array($result)) {
                $result = implode(',', $result);
            }
        }

        return $result;
    }

    /**
     * Retrieves a message header value by the given case-insensitive name.
     *
     * This method returns an array of all the header values of the given
     * case-insensitive header name.
     *
     * If the header does not appear in the message, this method MUST return an
     * empty array.
     *
     * @param string $name Case-insensitive header field name.
     *
     * @return string[] An array of string values as provided for the given
     *    header. If the header does not appear in the message, this method MUST
     *    return an empty array.
     */
    public function getHeader($name)
    {
        $result = [];

        $name = strtoupper($name);

        $headers = $this->httpRequest->headers->all();
        $headers = array_change_key_case($headers, CASE_UPPER);

        if (array_key_exists($name, $headers)) {
            $result = $headers[$name];

            if (!is_array($result)) {
                $result = [$result];
            }
        }

        return $result;
    }

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new header and/or value.
     *
     * @param string          $name  Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     *
     * @throws \InvalidArgumentException for invalid header names or values.
     * @return static
     */
    public function withAddedHeader($name, $value)
    {
        $request = $this->requestDuplicate();
        $request->headers->set($name, $value, false);

        return new static($request, $this->fileFactory);
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     *
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader($name)
    {
        $headers = $this->httpRequest->headers->all();
        $headers = array_change_key_case($headers, CASE_UPPER);

        return array_key_exists(strtoupper($name), $headers);
    }

    /**
     * Retrieves all message header values.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ": " . implode(", ", $values);
     *     }
     *
     *     // Emit headers iteratively:
     *     foreach ($message->getHeaders() as $name => $values) {
     *         foreach ($values as $value) {
     *             header(sprintf('%s: %s', $name, $value), false);
     *         }
     *     }
     *
     * While header names are not case-sensitive, getHeaders() will preserve the
     * exact case in which headers were originally specified.
     *
     * @return string[][] Returns an associative array of the message's headers. Each
     *     key MUST be a header name, and each value MUST be an array of strings
     *     for that header.
     */
    public function getHeaders()
    {
        return $this->httpRequest->headers->all();
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from getHeaders().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header and value.
     *
     * @param string          $name  Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     *
     * @throws \InvalidArgumentException for invalid header names or values.
     * @return static
     */
    public function withHeader($name, $value)
    {
        $request = $this->requestDuplicate();
        $request->headers->set($name, $value);

        return new static($request, $this->fileFactory);
    }

    /**
     * Request duplicate
     *
     * This method duplicate the current inner request and override the specified parameters
     *
     * @param array $param The parameters to override
     * @param bool  $force Hard replace the parameter, act as replace completely
     *
     * @return Request
     */
    protected abstract function requestDuplicate(array $param = [], bool $force = false) : Request;
}
