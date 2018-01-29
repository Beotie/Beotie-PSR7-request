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
 * StringUri authority part
 *
 * This trait is used to validate the StringUri instance relative to authority management
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait StringUriAuthorityPart
{
    /**
     * Authority provider
     *
     * This method provide a set of values to validate the StringUri methods. This values cover
     * user, password, host, scheme, port and expected result, in this order.
     *
     * @return [[string|int|null]]
     */
    public function authorityProvider()
    {
        return [
            ['user', 'password', 'host', 'http', 80, 'user:password@host'],
            ['user', 'password', 'host', 'http', 3306, 'user:password@host:3306'],
            ['user', '', 'host', 'http', 80, 'user@host'],
            ['user', '', 'host', 'http', 3306, 'user@host:3306'],
            ['', '', 'host', 'http', 80, 'host'],
            ['', '', 'host', 'http', 3306, 'host:3306'],
            ['', '', '', 'http', 80, ''],
            ['', '', '', 'http', 3306, ':3306'],
            ['', '', '', '', null, '']
        ];
    }

    /**
     * Test getAuthority
     *
     * This method validate the StringUri::getAuthority method
     *
     * @param string  $user     The user name to be used in the uri
     * @param string  $password The user password to be used in the uri
     * @param string  $host     The uri host used
     * @param string  $scheme   The protocol scheme
     * @param integer $port     The connection port
     * @param string  $expected The expected result of the getAuthority function
     *
     * @dataProvider authorityProvider
     * @return       void
     * @covers       Beotie\PSR7\Request\Uri\StringUri::getAuthority
     */
    public function testGetAuthority(
        string $user,
        string $password,
        string $host,
        string $scheme,
        ?int $port,
        string $expected
    ) : void {
        $reflex = new \ReflectionClass(StringUri::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $this->setAuthorityProperty($reflex, $instance, 'user', $user)
            ->setAuthorityProperty($reflex, $instance, 'pass', $password)
            ->setAuthorityProperty($reflex, $instance, 'host', $host)
            ->setAuthorityProperty($reflex, $instance, 'scheme', $scheme)
            ->setAuthorityProperty($reflex, $instance, 'port', $port);

        $this->getTestCase()->assertEquals($expected, $instance->getAuthority());

        return;
    }

    /**
     * Test getAuthorityPart
     *
     * This method validate the StringUri::getAuthorityPart method
     *
     * @param string  $user     The user name to be used in the uri
     * @param string  $password The user password to be used in the uri
     * @param string  $host     The uri host used
     * @param string  $scheme   The protocol scheme
     * @param integer $port     The connection port
     * @param string  $expected The expected result of the getAuthority function
     *
     * @dataProvider authorityProvider
     * @return       void
     * @covers       Beotie\PSR7\Request\Uri\StringUri::getAuthorityPart
     */
    public function testGetAuthorityPart(
        string $user,
        string $password,
        string $host,
        string $scheme,
        ?int $port,
        string $expected
    ) : void {
        if (!empty($scheme)) {
            $expected = sprintf('//%s', $expected);
        }

        $reflex = new \ReflectionClass(StringUri::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $this->setAuthorityProperty($reflex, $instance, 'user', $user)
            ->setAuthorityProperty($reflex, $instance, 'pass', $password)
            ->setAuthorityProperty($reflex, $instance, 'host', $host)
            ->setAuthorityProperty($reflex, $instance, 'scheme', $scheme)
            ->setAuthorityProperty($reflex, $instance, 'port', $port);

        $method = $reflex->getMethod('getAuthorityPart');
        $method->setAccessible(true);

        $this->getTestCase()->assertEquals($expected, $method->invoke($instance));

        return;
    }

    /**
     * Set property
     *
     * This method is used to hydrate a property relative of the authority
     *
     * @param \ReflectionClass $reflex   The main reflection class of StringUri
     * @param StringUri        $object   The main tested instance
     * @param string           $property The property to set
     * @param string|int       $value    The value to inject
     *
     * @return StringUriAuthorityPart
     */
    protected function setAuthorityProperty(\ReflectionClass $reflex, StringUri $object, string $property, $value)
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
