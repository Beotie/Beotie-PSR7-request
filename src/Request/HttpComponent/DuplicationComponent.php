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

use Beotie\PSR7\Request\HttpRequestServerAdapter;
use Symfony\Component\HttpFoundation\Request;

/**
 * Duplication component
 *
 * This trait is used to duplicate symfony Request instance
 *
 * @category Request
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait DuplicationComponent
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
     * Duplicate
     *
     * This method duplicate the current request and override the specified parameters
     *
     * @param array $param The parameters to override
     * @param bool  $force Hard replace the parameter, act as replace completely
     *
     * @return HttpRequestServerAdapter
     */
    protected function duplicate(array $param = [], bool $force = false) : HttpRequestServerAdapter
    {
        return new static($this->requestDuplicate($param, $force), $this->fileFactory);
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
    protected function requestDuplicate(array $param = [], bool $force = false) : Request
    {
        $query = null;
        $request = null;
        $attributes = null;
        $cookies = null;
        $files = null;
        $server = null;

        $parameters = ['query', 'request', 'attributes', 'cookies', 'files', 'server'];

        foreach ($parameters as $parameter) {
            $$parameter = $this->httpRequest->{$parameter}->all();

            if (isset($param[$parameter])) {
                $$parameter = $this->mergeParam($$parameter, $param[$parameter], $force);
            }
        }

        return $this->httpRequest->duplicate($query, $request, $attributes, $cookies, $files, $server);
    }

    /**
     * Merge param
     *
     * Return a merge between the original parameter and the newValue parameter if the force parameter is set to false.
     * Otherwise, return newValue parameter.
     *
     * @param array $original  The original value to merge
     * @param array $newValues The new values to merge
     * @param bool  $force     The merging strategy, as merge or replace
     *
     * @return array
     */
    private function mergeParam(array $original, array $newValues, bool $force) : array
    {
        if (!$force) {
            return array_replace($original, $newValues);
        }

        return $newValues;
    }
}
