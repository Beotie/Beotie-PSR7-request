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
 * StringUri path part
 *
 * This trait is used to validate the StringUri instance relative to path management
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait StringUriPathPart
{
    /**
     * Path provider
     *
     * This method provide a set of path to validate the StringUri methods
     *
     * @return [[string]]
     */
    public function pathProvider()
    {
        return [
            ['/absolute-path'],
            ['/long/absolute-path'],
            ['relative-path']
        ];
    }

    /**
     * Test withPath
     *
     * This method validate the StringUri::withPath method
     *
     * @param string $path The path to use as new path
     *
     * @dataProvider pathProvider
     * @return       void
     * @covers       Beotie\PSR7\Request\Uri\StringUri::withPath
     * @covers       Beotie\PSR7\Request\Uri\StringUri::duplicateWith
     */
    public function testWithPath(string $path) : void
    {
        $currentPath = 'path';

        $reflex = new \ReflectionClass(StringUri::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $propertyReflex = $reflex->getProperty('path');
        $propertyReflex->setAccessible(true);

        $propertyReflex->setValue($instance, $currentPath);

        $newInstance = $instance->withPath($path);

        $this->getTestCase()->assertEquals($currentPath, $propertyReflex->getValue($instance));
        $this->getTestCase()->assertEquals($path, $propertyReflex->getValue($newInstance));

        return;
    }

    /**
     * Test getPath
     *
     * This method validate the StringUri::getPath method
     *
     * @param string $path The path to use as path
     *
     * @dataProvider pathProvider
     * @return       void
     * @covers       Beotie\PSR7\Request\Uri\StringUri::getPath
     */
    public function testGetPath(string $path) : void
    {
        $reflex = new \ReflectionClass(StringUri::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $propertyReflex = $reflex->getProperty('path');
        $propertyReflex->setAccessible(true);
        $propertyReflex->setValue($instance, $path);

        $this->getTestCase()->assertEquals($path, $instance->getPath());

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
