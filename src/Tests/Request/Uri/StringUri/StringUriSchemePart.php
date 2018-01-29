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

use Beotie\PSR7\Request\Uri\PortMappingInterface;
use Beotie\PSR7\Request\Uri\StringUri;
use PHPUnit\Framework\TestCase;

/**
 * StringUri scheme part
 *
 * This trait is used to validate the StringUri instance relative to scheme management
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait StringUriSchemePart
{
    /**
     * Scheme provider
     *
     * This method provide a set of scheme to validate the StringUri methods
     *
     * @return [[string]]
     */
    public function schemeProvider()
    {
        $schemes = array_keys(PortMappingInterface::MAPPING);

        return array_map([$this, 'toArray'], $schemes);
    }

    /**
     * Test withScheme
     *
     * This method validate the StringUri::withScheme method
     *
     * @param string $scheme The scheme to use as new scheme
     *
     * @dataProvider schemeProvider
     * @return       void
     * @covers       Beotie\PSR7\Request\Uri\StringUri::withScheme
     * @covers       Beotie\PSR7\Request\Uri\StringUri::duplicateWith
     */
    public function testWithScheme(string $scheme) : void
    {
        $currentScheme = 'http';

        $reflex = new \ReflectionClass(StringUri::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $propertyReflex = $reflex->getProperty('scheme');
        $propertyReflex->setAccessible(true);

        $propertyReflex->setValue($instance, $currentScheme);

        $newInstance = $instance->withScheme($scheme);

        $this->getTestCase()->assertEquals($currentScheme, $propertyReflex->getValue($instance));
        $this->getTestCase()->assertEquals($scheme, $propertyReflex->getValue($newInstance));

        return;
    }

    /**
     * Test getScheme
     *
     * This method validate the StringUri::getScheme method
     *
     * @param string $scheme The scheme to use as new scheme
     *
     * @dataProvider schemeProvider
     * @return       void
     * @covers       Beotie\PSR7\Request\Uri\StringUri::getScheme
     */
    public function testGetScheme(string $scheme) : void
    {
        $reflex = new \ReflectionClass(StringUri::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $propertyReflex = $reflex->getProperty('scheme');
        $propertyReflex->setAccessible(true);
        $propertyReflex->setValue($instance, $scheme);

        $this->getTestCase()->assertEquals($scheme, $instance->getScheme());

        return;
    }

    /**
     * Test getSchemeElement
     *
     * This method validate the StringUri::getSchemeElement method
     *
     * @param string $scheme The scheme to use as scheme
     *
     * @dataProvider schemeProvider
     * @return       void
     * @covers       Beotie\PSR7\Request\Uri\StringUri::getSchemeElement
     */
    public function testGetSchemeElement(string $scheme) : void
    {
        $reflex = new \ReflectionClass(StringUri::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $reflexMethod = $reflex->getMethod('getSchemeElement');
        $reflexMethod->setAccessible(true);

        $this->getTestCase()->assertEmpty($reflexMethod->invoke($instance));

        $propertyReflex = $reflex->getProperty('scheme');
        $propertyReflex->setAccessible(true);
        $propertyReflex->setValue($instance, $scheme);

        $this->getTestCase()->assertEquals(sprintf('%s:', $scheme), $reflexMethod->invoke($instance));

        return;
    }

    /**
     * Test getStandardSchemePort
     *
     * This method validate the StringUri::getStandardSchemePort method
     *
     * @param string $scheme The scheme to use as scheme
     *
     * @dataProvider schemeProvider
     * @return       void
     * @covers       Beotie\PSR7\Request\Uri\StringUri::getStandardSchemePort
     */
    public function testGetStandardSchemePort(string $scheme) : void
    {
        $expectedPort = PortMappingInterface::MAPPING[$scheme];

        $reflex = new \ReflectionClass(StringUri::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $reflexMethod = $reflex->getMethod('getStandardSchemePort');
        $reflexMethod->setAccessible(true);

        $this->getTestCase()->assertEquals($expectedPort, $reflexMethod->invoke($instance, $scheme));
    }

    /**
     * To array
     *
     * This method insert a value into an empty array
     *
     * @param mixed $value The value to inject
     *
     * @return array
     */
    protected function toArray($value) : array
    {
        return [$value];
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
