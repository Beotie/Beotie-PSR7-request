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
namespace Beotie\PSR7\Tests\Request\HttpRequestServerAdapter;

use PHPUnit\Framework\MockObject\MockObject;
use Beotie\PSR7\Request\HttpComponent\DuplicationComponent;
use PHPUnit\Framework\TestCase;

/**
 * Duplication test trait
 *
 * This trait is used to validate the HttpRequestServerAdapter instance regarding duplication logic
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait DuplicationTestTrait
{
    /**
     * Test mergeParam
     *
     * This method validate the HttpRequestServerAdapter::mergeParam method
     *
     * @return void
     */
    public function testMergeParams()
    {
        $instance = $this->getMockForTrait(DuplicationComponent::class);
        $reflex = new \ReflectionClass($instance);

        $mergeParam = $reflex->getMethod('mergeParam');
        $mergeParam->setAccessible(true);

        $result = $mergeParam->invoke(
            $instance,
            ['hello'=>'world', 'hella' => 'warld'],
            ['hello' => 'torvald'],
            false
        );
        $this->getTestCase()->assertEquals(['hello'=>'torvald', 'hella' => 'warld'], $result);

        $result = $mergeParam->invoke(
            $instance,
            ['hello'=>'world', 'hella' => 'warld'],
            ['hello' => 'torvald'],
            true
        );
        $this->getTestCase()->assertEquals(['hello' => 'torvald'], $result);
    }

    /**
     * Get test case
     *
     * Return an instance of TestCase
     *
     * @return TestCase
     */
    protected abstract function getTestCase() : TestCase;

    /**
     * Get mock for trait
     *
     * Returns a mock object for the specified trait with all abstract methods
     * of the trait mocked. Concrete methods to mock can be specified with the
     * `$mockedMethods` parameter.
     *
     * @param string $traitName               The trait name
     * @param array  $arguments               The constructor arguments
     * @param string $mockClassName           The mock class to generate
     * @param bool   $callOriginalConstructor Original constructor call status
     * @param bool   $callOriginalClone       Original clone call status
     * @param bool   $callAutoload            Original autoload call status
     * @param array  $mockedMethods           The method set to mock
     * @param bool   $cloneArguments          The cloned arguments
     *
     * @return                  MockObject
     * @SuppressWarnings(PHPMD)
     * @throws                  \Exception
     */
    protected abstract function getMockForTrait(
        $traitName,
        array $arguments = [],
        $mockClassName = '',
        $callOriginalConstructor = true,
        $callOriginalClone = true,
        $callAutoload = true,
        $mockedMethods = [],
        $cloneArguments = false
    );
}
