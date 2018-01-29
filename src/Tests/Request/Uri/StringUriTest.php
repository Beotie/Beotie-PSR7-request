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
namespace Beotie\PSR7\Tests\Request\Uri;

use PHPUnit\Framework\TestCase;
use Beotie\PSR7\Request\Uri\StringUri;
use function False\true;
use Beotie\PSR7\Tests\Request\Uri\StringUri\StringUriSchemePart;
use Beotie\PSR7\Tests\Request\Uri\StringUri\StringUriPathPart;
use Beotie\PSR7\Tests\Request\Uri\StringUri\StringUriQueryPart;
use Beotie\PSR7\Tests\Request\Uri\StringUri\StringUriFragmentPart;
use Beotie\PSR7\Tests\Request\Uri\StringUri\StringUriHostPart;
use Beotie\PSR7\Tests\Request\Uri\StringUri\StringUriAuthorityPart;
use Beotie\PSR7\Tests\Request\Uri\StringUri\StringUriPortPart;
use Beotie\PSR7\Tests\Request\Uri\StringUri\StringUriUserInfoPart;
use Beotie\PSR7\Tests\Request\Uri\StringUri\StringUriToString;

/**
 * String uri test
 *
 * This class is used to validate the StringUri instance
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
class StringUriTest extends TestCase
{
    use StringUriSchemePart,
        StringUriPathPart,
        StringUriQueryPart,
        StringUriFragmentPart,
        StringUriHostPart,
        StringUriAuthorityPart,
        StringUriPortPart,
        StringUriUserInfoPart,
        StringUriToString;

    /**
     * Uri provider
     *
     * This method provide a set of data to validate the StringUri methods
     *
     * @return [[string, [string|int]]
     */
    public function uriProvider()
    {
        return [
            [
                'http://user:userPass@example.org:3300/path?query=true#frag/ment',
                [
                    'http',
                    '/path',
                    'query=true',
                    'frag/ment',
                    'example.org',
                    3300,
                    'user',
                    'userPass'
                ]
            ]
        ];
    }

    /**
     * Test constructor
     *
     * This method validate the StringUri::_construct method
     *
     * @param string $uri        The constructor URI
     * @param array  $components The uri components
     *
     * @dataProvider uriProvider
     * @return       void
     * @covers       Beotie\PSR7\Request\Uri\StringUri::__construct
     */
    public function testConstructor(string $uri, array $components) : void
    {
        $instance = new StringUri($uri);

        $componentKeys = [
            'scheme',
            'path',
            'query',
            'fragment',
            'host',
            'port',
            'user',
            'pass'
        ];
        $components = array_combine($componentKeys, $components);

        $reflectionClass = new \ReflectionClass(StringUri::class);
        foreach ($componentKeys as $component) {
            $property = $reflectionClass->getProperty($component);
            $property->setAccessible(true);
            $this->assertEquals($components[$component], $property->getValue($instance));
        }

        return;
    }

    /**
     * Get test case
     *
     * This method return the current test case to be used as assertion provider
     *
     * @return TestCase
     */
    protected function getTestCase(): TestCase
    {
        return $this;
    }
}
