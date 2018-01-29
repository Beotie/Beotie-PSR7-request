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
 * StringUri host part
 *
 * This trait is used to validate the StringUri instance relative to host management
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait StringUriHostPart
{
    /**
     * Test withHost
     *
     * This method validate the StringUri::withHost method
     *
     * @return void
     * @covers Beotie\PSR7\Request\Uri\StringUri::withHost
     * @covers Beotie\PSR7\Request\Uri\StringUri::duplicateWith
     */
    public function testWithHost() : void
    {
        $host = 'example.org';
        $currentHost = 'host';

        $reflex = new \ReflectionClass(StringUri::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $propertyReflex = $reflex->getProperty('host');
        $propertyReflex->setAccessible(true);

        $propertyReflex->setValue($instance, $currentHost);

        $newInstance = $instance->withHost($host);

        $this->getTestCase()->assertEquals($currentHost, $propertyReflex->getValue($instance));
        $this->getTestCase()->assertEquals($host, $propertyReflex->getValue($newInstance));

        return;
    }

    /**
     * Test getHost
     *
     * This method validate the StringUri::getHost method
     *
     * @return void
     * @covers Beotie\PSR7\Request\Uri\StringUri::getHost
     */
    public function testGetHost() : void
    {
        $host = 'example.org';
        $reflex = new \ReflectionClass(StringUri::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $propertyReflex = $reflex->getProperty('host');
        $propertyReflex->setAccessible(true);
        $propertyReflex->setValue($instance, $host);

        $this->getTestCase()->assertEquals($host, $instance->getHost());

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
