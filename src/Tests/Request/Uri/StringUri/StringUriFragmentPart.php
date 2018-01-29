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

use PHPUnit\Framework\TestCase;
use Beotie\PSR7\Request\Uri\StringUri;

/**
 * StringUri fragment part
 *
 * This trait is used to validate the StringUri instance relative to fragment management
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait StringUriFragmentPart
{
    /**
     * Fragment provider
     *
     * This method provide a set of fragment to validate the StringUri methods
     *
     * @return [[string]]
     */
    public function fragmentProvider()
    {
        return [
            ['#fragment'],
            ['#long/fragment']
        ];
    }

    /**
     * Test withFragment
     *
     * This method validate the StringUri::withFragment method
     *
     * @param string $fragment The fragment to use as new fragment
     *
     * @dataProvider fragmentProvider
     * @return       void
     * @covers       Beotie\PSR7\Request\Uri\StringUri::withFragment
     * @covers       Beotie\PSR7\Request\Uri\StringUri::duplicateWith
     */
    public function testWithFragment(string $fragment) : void
    {
        $currentFragment = 'fragment';

        $reflex = new \ReflectionClass(StringUri::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $propertyReflex = $reflex->getProperty('fragment');
        $propertyReflex->setAccessible(true);

        $propertyReflex->setValue($instance, $currentFragment);

        $newInstance = $instance->withFragment($fragment);

        $this->getTestCase()->assertEquals($currentFragment, $propertyReflex->getValue($instance));
        $this->getTestCase()->assertEquals($fragment, $propertyReflex->getValue($newInstance));

        return;
    }

    /**
     * Test getFragment
     *
     * This method validate the StringUri::getFragment method
     *
     * @param string $fragment The fragment to use as fragment
     *
     * @dataProvider fragmentProvider
     * @return       void
     * @covers       Beotie\PSR7\Request\Uri\StringUri::getFragment
     */
    public function testGetFragment(string $fragment) : void
    {
        $reflex = new \ReflectionClass(StringUri::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $propertyReflex = $reflex->getProperty('fragment');
        $propertyReflex->setAccessible(true);
        $propertyReflex->setValue($instance, $fragment);

        $this->getTestCase()->assertEquals($fragment, $instance->getFragment());

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
