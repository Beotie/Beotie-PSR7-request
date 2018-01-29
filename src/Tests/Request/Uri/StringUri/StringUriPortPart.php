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
 * StringUri port part
 *
 * This trait is used to validate the StringUri instance relative to port management
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait StringUriPortPart
{
    /**
     * Port error provider
     *
     * This method provide a set of values to validate the StringUri methods. This values cover invalid ports
     *
     * @return [[string|int|null]]
     */
    public function portErrorProvider()
    {
        return [
            ['scp'],
            [65536],
            [-1],
            [null]
        ];
    }

    /**
     * Port provider
     *
     * This method provide a set of values to validate the StringUri methods. This values cover ports
     *
     * @return [[int]]
     */
    public function portProvider()
    {
        return [
            [80],
            [53],
            [631],
            [110]
        ];
    }

    /**
     * Port provider
     *
     * This method provide a set of values to validate the StringUri methods. This values cover
     * scheme, port and expected result, in this order.
     *
     * @return [[string|int|null]]
     */
    public function portSchemeProvider()
    {
        return [
            ['http', 80, null],
            ['dns', 53, null],
            ['ipps', 631, null],
            ['pop', 110, null],
            ['wais', 210, null],
            ['hTTp', 80, null],
            ['dnS', 53, null],
            ['iPps', 631, null],
            ['Pop', 110, null],
            ['waIs', 210, null],
            ['http', 210, 210],
            ['dns', 631, 631],
            ['ipps', 53, 53],
            ['pop', 80, 80],
            ['wais', 110, 110]
        ];
    }

    /**
     * Test getPort
     *
     * This method validate the StringUri::getPort method
     *
     * @param string  $scheme   The protocol scheme
     * @param integer $port     The connection port
     * @param string  $expected The expected result of the getPort function
     *
     * @dataProvider portSchemeProvider
     * @return       void
     * @covers       Beotie\PSR7\Request\Uri\StringUri::getPort
     */
    public function testGetPort(string $scheme, int $port, ?int $expected) : void
    {
        $reflex = new \ReflectionClass(StringUri::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $this->setPortProperty($reflex, $instance, 'scheme', $scheme)
            ->setPortProperty($reflex, $instance, 'port', $port);

        $this->getTestCase()->assertEquals($expected, $instance->getPort());

        return;
    }

    /**
     * Test withPort
     *
     * This method validate the StringUri::withPort method
     *
     * @param string $port The port to use as new port
     *
     * @dataProvider portProvider
     * @return       void
     * @covers       Beotie\PSR7\Request\Uri\StringUri::withPort
     * @covers       Beotie\PSR7\Request\Uri\StringUri::duplicateWith
     */
    public function testWithPort(int $port) : void
    {
        $currentPort = '1';

        $reflex = new \ReflectionClass(StringUri::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $propertyReflex = $reflex->getProperty('port');
        $propertyReflex->setAccessible(true);

        $propertyReflex->setValue($instance, $currentPort);

        $newInstance = $instance->withPort($port);

        $this->getTestCase()->assertEquals($currentPort, $propertyReflex->getValue($instance));
        $this->getTestCase()->assertEquals($port, $propertyReflex->getValue($newInstance));

        return;
    }

    /**
     * Test withPort in error case
     *
     * This method validate the StringUri::withPort method
     *
     * @param string $port The port to use as new port
     *
     * @dataProvider portErrorProvider
     * @return       void
     * @covers       Beotie\PSR7\Request\Uri\StringUri::withPort
     */
    public function testWithPortError($port) : void
    {
        $this->getTestCase()->expectException(\RuntimeException::class);
        $this->getTestCase()->expectExceptionMessage('Out of TCP/UDP allowed range');

        $reflex = new \ReflectionClass(StringUri::class);
        $instance = $reflex->newInstanceWithoutConstructor();
        $instance->withPort($port);
    }

    /**
     * Set property
     *
     * This method is used to hydrate a property relative to the port
     *
     * @param \ReflectionClass $reflex   The main reflection class of StringUri
     * @param StringUri        $object   The main tested instance
     * @param string           $property The property to set
     * @param string|int       $value    The value to inject
     *
     * @return StringUriAuthorityPart
     */
    protected function setPortProperty(\ReflectionClass $reflex, StringUri $object, string $property, $value)
    {
        $propertyReflex = $reflex->getProperty($property);

        if ($propertyReflex->isPrivate() || $propertyReflex->isProtected()) {
            $propertyReflex->setAccessible(true);
        }

        $propertyReflex->setValue($object, $value);

        return $this;
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
