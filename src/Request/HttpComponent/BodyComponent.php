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
use Psr\Http\Message\StreamInterface;
use Beotie\PSR7\Request\HttpRequestServerAdapter;

/**
 * Body component
 *
 * This trait is used to implement the PSR7 over symfony Request instance and manange body part
 *
 * @category Request
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait BodyComponent
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
     * Gets the body of the message.
     *
     * @return StreamInterface Returns the body as a stream.
     */
    public function getBody()
    {
        throw new \LogicException(sprintf('%s method not implemented', __METHOD__));
    }

    /**
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param StreamInterface $body Body.
     *
     * @throws \InvalidArgumentException When the body is not valid.
     * @return static
     */
    public function withBody(StreamInterface $body)
    {
        $body->rewind();
        $streamContent = $body->getContents();

        $dataArray = [];
        mb_parse_str($streamContent, $dataArray);

        return $this->withParsedBody($dataArray);
    }

    /**
     * Retrieve any parameters provided in the request body.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, this method MUST
     * return the contents of $_POST.
     *
     * Otherwise, this method may return any results of deserializing
     * the request body content; as parsing returns structured content, the
     * potential types MUST be arrays or objects only. A null value indicates
     * the absence of body content.
     *
     * @return null|array|object The deserialized body parameters, if any.
     *     These will typically be an array or object.
     */
    public function getParsedBody()
    {
        if ($this->httpRequest->getMethod() === 'POST') {
            return $this->httpRequest->request->all();
        }

        return $this->httpRequest->getContent();
    }

    /**
     * Return an instance with the specified body parameters.
     *
     * These MAY be injected during instantiation.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, use this method
     * ONLY to inject the contents of $_POST.
     *
     * The data IS NOT REQUIRED to come from $_POST, but MUST be the results of
     * deserializing the request body content. Deserialization/parsing returns
     * structured data, and, as such, this method ONLY accepts arrays or objects,
     * or a null value if nothing was available to parse.
     *
     * As an example, if content negotiation determines that the request data
     * is a JSON payload, this method could be used to create a request
     * instance with the deserialized parameters.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param null|array|object $data The deserialized body data. This will
     *                                typically be in an array or object.
     *
     * @throws \InvalidArgumentException if an unsupported argument type is
     *     provided.
     * @return static
     */
    public function withParsedBody($data)
    {
        return $this->duplicate(['request' => $data], true);
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
