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
 * StringUri user info part
 *
 * This trait is used to validate the StringUri instance relative to user info management
 *
 * @category Test
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait StringUriUserInfoPart
{
    /**
     * User info provider
     *
     * This method provide a set of user info to validate the StringUri methods
     *
     * @return [[string|null]]
     */
    public function userInfoProvider()
    {
        return  [
            ['user', 'password', 'user:password'],
            ['user', null, 'user'],
            ['', null, ''],
            ['', 'password', ':password']
        ];
    }

    /**
     * Test withUserInfo
     *
     * This method validate the StringUri::withUserInfo method
     *
     * @param string $user The user to use as user name
     * @param string $pass The pass to use as user password
     *
     * @dataProvider userInfoProvider
     * @return       void
     * @covers       Beotie\PSR7\Request\Uri\StringUri::withUserInfo
     * @covers       Beotie\PSR7\Request\Uri\StringUri::duplicateWith
     */
    public function testWithUserInfo(string $user, ?string $pass) : void
    {
        $baseUser = 'test_user';
        $basePass = 'test_pass';

        $reflex = new \ReflectionClass(StringUri::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        $userProperty = $reflex->getProperty('user');
        $userProperty->setAccessible(true);
        $userProperty->setValue($instance, $baseUser);

        $passProperty = $reflex->getProperty('pass');
        $passProperty->setAccessible(true);
        $passProperty->setValue($instance, $basePass);

        $newInstance = $instance->withUserInfo($user, $pass);

        $this->getTestCase()->assertEquals($baseUser, $userProperty->getValue($instance));
        $this->getTestCase()->assertEquals($basePass, $passProperty->getValue($instance));

        $this->getTestCase()->assertEquals($user, $userProperty->getValue($newInstance));
        if (empty($user)) {
            $pass = '';
        }
        if (!empty($user) && $pass === null) {
            $pass = $basePass;
        }
        $this->getTestCase()->assertEquals(
            $this->resolvePassword($user, $pass, $basePass),
            $passProperty->getValue($newInstance)
        );

        return;
    }

    /**
     * Test getUserInfo
     *
     * This method validate the StringUri::getUserInfo method
     *
     * @param string $user     The user to use as user name
     * @param string $pass     The pass to use as user password
     * @param string $expected The expectation of the getUserInfo method result
     *
     * @dataProvider userInfoProvider
     * @return       void
     * @covers       Beotie\PSR7\Request\Uri\StringUri::getUserInfo
     */
    public function testGetUserInfo(string $user, ?string $pass, string $expected) : void
    {
        $reflex = new \ReflectionClass(StringUri::class);
        $instance = $reflex->newInstanceWithoutConstructor();

        if ($user) {
            $userProperty = $reflex->getProperty('user');
            $userProperty->setAccessible(true);
            $userProperty->setValue($instance, $user);
        }

        if ($pass) {
            $passProperty = $reflex->getProperty('pass');
            $passProperty->setAccessible(true);
            $passProperty->setValue($instance, $pass);
        }

        $this->getTestCase()->assertEquals($expected, $instance->getUserInfo());

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

    /**
     * Resolve password
     *
     * This method resolve the expected password, according with user status to validate the StringUri::withUserInfo
     * method
     *
     * @param string      $user     The new username
     * @param string|null $password The new user password
     * @param string      $basePass The base password used in main instance
     *
     * @return string
     */
    private function resolvePassword(string $user, ?string $password, string $basePass) : string
    {
        if (empty($user)) {
            return '';
        }
        if ($password === null) {
            return $basePass;
        }

        return $password;
    }
}
