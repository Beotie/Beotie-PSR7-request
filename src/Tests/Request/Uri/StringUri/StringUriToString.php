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
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
namespace Beotie\PSR7\Tests\Request\Uri\StringUri;

use Beotie\PSR7\Request\Uri\StringUri;
use PHPUnit\Framework\TestCase;

/**
 * StringUri toString part
 *
 * This trait is used to validate the StringUri instance relative to toString method
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait StringUriToString
{
    /**
     * To string provider
     *
     * This method provide a set of fixture data to validate the toString method of the StringUri
     *
     * @return [[string, array]]
     */
    public function toStringProvider() : array
    {
        $host = 'example.org';
        return [
            ['example.org', ['host' => $host]],
            ['user@example.org', ['host' => $host, 'user' => 'user']],
            ['user:pwd@example.org', ['host' => $host, 'user' => 'user', 'pass' => 'pwd']],
            ['http://example.org', ['host' => $host, 'scheme' => 'http']],
            ['http://example.org', ['host' => $host, 'scheme' => 'http', 'port' => 80]],
            ['http://example.org:336', ['host' => $host, 'scheme' => 'http', 'port' => 336]],
            ['example.org?query=string', ['host' => $host, 'query' => 'query=string']],
            ['http://?query=string', ['scheme' => 'http', 'query' => 'query=string']],
            ['example.org#hello/world', ['host' => $host, 'fragment' => 'hello/world']],
            ['http://#hello/world', ['scheme' => 'http', 'fragment' => 'hello/world']],
            ['http://user:pwd@example.org', ['host' => $host, 'user' => 'user', 'pass' => 'pwd', 'scheme' => 'http']]
        ];
    }

    /**
     * Test toString
     *
     * This method validate the StringUri::__toString method
     *
     * @param string $expected The expected result of the __toString method
     * @param array  $params   The StringUri parameters to hydrate the properties, as associative array of property
     *                         name and value
     *
     * @dataProvider toStringProvider
     * @covers       Beotie\PSR7\Request\Uri\StringUri::__toString
     * @return       void
     */
    public function testToString(string $expected, array $params) : void
    {
        $reflex = new \ReflectionClass(StringUri::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        foreach ($params as $propertyName => $value) {
            $property = $reflex->getProperty($propertyName);
            $property->setAccessible(true);

            $property->setValue($instance, $value);
        }

        $this->getTestCase()->assertEquals($expected, (string)$instance);

        return;
    }

    /**
     * Get test case
     *
     * This method return the current test case to be used as assertion provider
     *
     * @return TestCase
     */
    protected abstract function getTestCase() : TestCase;
}
