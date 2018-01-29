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
namespace Beotie\PSR7\Request;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Beotie\PSR7\Request\File\Factory\EmbeddedFileFactoryInterface;
use Beotie\PSR7\Request\HttpComponent as Component;

/**
 * Http request server adapter
 *
 * This class is used to implement the PSR7 over symfony Request instance
 *
 * @category Request
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
class HttpRequestServerAdapter implements ServerRequestInterface
{
    use Component\CookieComponent,
        Component\AttributeComponent,
        Component\DuplicationComponent,
        Component\HeaderComponent,
        Component\QueryComponent,
        Component\FileComponent,
        Component\BodyComponent,
        Component\UriComponent,
        Component\MethodComponent,
        Component\RequestTargetComponent,
        Component\ProtocolComponent;

    /**
     * Http request
     *
     * The base symfony http request
     *
     * @var Request
     */
    private $httpRequest;

    /**
     * Construct
     *
     * The base HttpRequestServerAdapter constructor
     *
     * @param Request                      $httpRequest The base symfony request instance
     * @param EmbeddedFileFactoryInterface $fileFactory The EbeddedUploadFile factory
     *
     * @return void
     */
    public function __construct(Request $httpRequest, EmbeddedFileFactoryInterface $fileFactory)
    {
        $this->httpRequest = $httpRequest;
        $this->fileFactory = $fileFactory;
    }

    /**
     * Retrieve server parameters.
     *
     * Retrieves data related to the incoming request environment,
     * typically derived from PHP's $_SERVER superglobal. The data IS NOT
     * REQUIRED to originate from $_SERVER.
     *
     * @return array
     */
    public function getServerParams()
    {
        return $this->httpRequest->server->all();
    }
}
