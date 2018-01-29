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
 * StringUri query part
 *
 * This trait is used to validate the StringUri instance relative to query management
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait StringUriQueryPart
{
    /**
     * Query provider
     *
     * This method provide a set of query to validate the StringUri methods
     *
     * @return [[string]]
     */
    public function queryProvider()
    {
        return [
            ['query=string'],
            ['query=string&other=value']
        ];
    }

    /**
     * Test withQuery
     *
     * This method validate the StringUri::withQuery method
     *
     * @param string $query The query to use as new query
     *
     * @dataProvider queryProvider
     * @return       void
     * @covers       Beotie\PSR7\Request\Uri\StringUri::withQuery
     * @covers       Beotie\PSR7\Request\Uri\StringUri::duplicateWith
     */
    public function testWithQuery(string $query) : void
    {
        $currentQuery = 'query';

        $reflex = new \ReflectionClass(StringUri::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $propertyReflex = $reflex->getProperty('query');
        $propertyReflex->setAccessible(true);

        $propertyReflex->setValue($instance, $currentQuery);

        $newInstance = $instance->withQuery($query);

        $this->getTestCase()->assertEquals($currentQuery, $propertyReflex->getValue($instance));
        $this->getTestCase()->assertEquals($query, $propertyReflex->getValue($newInstance));

        return;
    }

    /**
     * Test getQuery
     *
     * This method validate the StringUri::getQuery method
     *
     * @param string $query The query to use as query
     *
     * @dataProvider queryProvider
     * @return       void
     * @covers       Beotie\PSR7\Request\Uri\StringUri::getQuery
     */
    public function testGetQuery(string $query) : void
    {
        $reflex = new \ReflectionClass(StringUri::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $propertyReflex = $reflex->getProperty('query');
        $propertyReflex->setAccessible(true);
        $propertyReflex->setValue($instance, $query);

        $this->getTestCase()->assertEquals($query, $instance->getQuery());

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
