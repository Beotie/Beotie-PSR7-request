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
 * @category Request
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
namespace Beotie\PSR7\Request\Uri\StringUriElement;

use Beotie\PSR7\Request\Uri\StringUri;

/**
 * User info element trait
 *
 * This trait is use for decoupling the user info part of the StringUri from the main class
 *
 * @category Request
 * @package  Beotie_Core_Bundle
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
trait UserInfoElementTrait
{
    /**
     * User
     *
     * This property store the user part of the url
     *
     * @var string
     */
    protected $user = '';

    /**
     * Password
     *
     * This property store the password part of the url
     *
     * @var string
     */
    protected $pass = '';

    /**
     * Return an instance with the specified user information.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified user information.
     *
     * Password is optional, but the user information MUST include the
     * user; an empty string for the user is equivalent to removing user
     * information.
     *
     * @param string      $user     The user name to use for authority.
     * @param null|string $password The password associated with $user.
     *
     * @return static A new instance with the specified user information.
     */
    public function withUserInfo($user, $password = null)
    {
        $instance = $this->duplicateWith('user', $user);
        if (empty($user)) {
            $instance->pass = '';
        }

        if (!empty($password) && !empty($user)) {
            $instance->pass = $password;
        }

        return $instance;
    }

    /**
     * Retrieve the user information component of the URI.
     *
     * If no user information is present, this method MUST return an empty
     * string.
     *
     * If a user is present in the URI, this will return that value;
     * additionally, if the password is also present, it will be appended to the
     * user value, with a colon (":") separating the values.
     *
     * The trailing "@" character is not part of the user information and MUST
     * NOT be added.
     *
     * @return string The URI user information, in "username[:password]" format.
     */
    public function getUserInfo()
    {
        $result = $this->user;
        if (!empty($this->pass)) {
            $result .= sprintf(':%s', $this->pass);
        }

        return $result;
    }

    /**
     * Duplicate with
     *
     * This method duplicate the current object and update a property with a given value before returning it
     *
     * @param string $property The property name to update
     * @param mixed  $value    The new property value
     *
     * @return StringUri
     */
    protected abstract function duplicateWith(string $property, $value) : StringUri;
}
