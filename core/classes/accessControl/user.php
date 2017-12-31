<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 12/30/17
 * Time: 12:42 PM
 */

namespace DarlingCms\classes\accessControl;

/**
 * Class user. Defines an access controller that represents an actual user with a user name, a password,
 * and public and private meta data associated with it.
 * @package DarlingCms\classes\accessControl
 */
class user extends accessController
{
    /**
     * @var string The user's user name.
     */
    private $userName;
    /**
     * @var string The user's password. Note: This string is encrypted via the encrypt() method whenever the
     *             password is set via the setPassword() or changePassword() methods.
     */
    private $password;
    /**
     * @var array Array of user's private meta data. Data in this array can only be accessed via
     *            the getPrivateMetaData() method. Sensitive user data should be stored in this array
     *            using the setPrivateMetaData() method.
     */
    private $privateMetaData = array();
    /**
     * @var array Array of user's public meta data. Data can be stored in this array via the setPublicMetaData()
     *            method and can be accessed via the getPublicMetaData() method. WARNING: Do not store sensitive
     *            user data in this array, use the privateMetaData property's array to store sensitive user data
     *            by calling the setPrivateMetaData() method.
     */
    private $publicMetaData = array();

    /**
     * user constructor. Instantiate's a new user object.
     * @param string $userName The user name to assign to this user.
     * @param string $password The password to assign to this user.
     * @param string $email The email to assign to this user. Note: This data will be stored in the array
     *                      assigned to the privateMetaData property under the index 'email'. WARNING: If
     *                      $email is not a valid email then the boolean false will used. A valid email
     *                      conforms to the following structure: someUser@someServer.type
     *                      For example: someUser@example.com
     */
    public function __construct(string $userName, string $password, string $email)
    {
        /* Set the user's password. */
        $this->setPassword($password);
        /* Set the user's user name. */
        $this->setUserName($userName, $password);
        /* Store user's email in the privateMetaData property's array. */
        $this->setPrivateMetaData('email', filter_var($email, FILTER_VALIDATE_EMAIL), $password);
    }

    /**
     * Set the user's password. This method should only be called by the constructor upon instantiation,
     * or by the changePassword() method for security reasons. This method is responsible for assigning the
     * encrypted version of the user's password whenever a new user is instantiated, or when the changePassword()
     * method is called.
     * @param string $password The password to assign to this user.
     * @return bool True if password was set and properly encrypted, false otherwise.
     */
    private function setPassword(string $password): bool
    {
        /* Encrypt specified password and assign the encrypted password to this user's password property. */
        $this->password = $this->encrypt($password);
        /* Return true if password was set and properly encrypted, false otherwise. */
        return ((isset($this->password) === true) && ($this->password === $this->encrypt($password)));
    }

    /**
     * Encrypts a string using the SHA256 algorithm.
     * @param string $string The string to be encrypted.
     * @return string The encrypted string.
     */
    private function encrypt(string $string): string
    {
        return hash_hmac('sha256', $string, 'zxcvsdkjfi39djk8930sdmjmc8788fsikd99kdodjqamcl99302owreclle192930499dsl030', false);
    }

    /**
     * Returns the user's user name.
     * @return string The user's user name.
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * Set the user's user name.
     * @param string $userName The user name to assign to this user.
     * @param string $password The user's password. Required for security to insure user name can only be
     *                         set or changed by this user.
     * @return bool True if specified $userName was assigned to this user, false otherwise.
     */
    public function setUserName(string $userName, string $password): bool
    {
        /* If valid password provided, assign specified $userName to this user's userName property. */
        if ($this->validatePassword($password) === true) {
            $this->userName = $userName;
            return ((isset($this->userName) === true) && ($this->userName === $userName));
        }
        /* Invalid password, username was not set, return false. */
        return false;
    }

    /**
     * Change the user's assigned password. For security, this method requires that the user's original assigned
     * password be provided via the $originalPassword parameter to prevent unauthorized changes to the user's
     * password.
     * @param string $originalPassword The user's original password.
     * @param string $newPassword The new password to assign to this user.
     * @return bool True if new password was assigned, false otherwise. Note: If the password supplied to the
     *              $originalPassword parameter does not match the user's original password this method will
     *              return false, and the user's password will not be changed.
     */
    public function changePassword(string $originalPassword, string $newPassword): bool
    {
        /* If the specified $originalPassword matches the user's password, assign the specified
           $newPassword as the user's password. */
        if ($this->validatePassword($originalPassword) === true) {
            return $this->setPassword($newPassword);
        }
        /* The specified $originalPassword was not valid, user's password was not changed, return false.*/
        return false;
    }

    /**
     * Validate if the specified password matches the user's password.
     * @param string $password The password to validate.
     * @return bool True if supplied password matches user's password, false otherwise.
     */
    public function validatePassword(string $password): bool
    {
        /* If the encrypted version of the specified $password matches the user's password, return true. */
        if ($this->encrypt($password) === $this->password) {
            return true;
        }
        /* The specified $password was not valid, return false. */
        return false;
    }

    /**
     * Gets data from the privateMetaData property's array.
     * NOTE: This method will return false if data does not exist, or if the supplied $password is not valid.
     * @param string $index The index of the data to be returned from the privateMetaData property's array.
     * @param string $password The user's password, required for security to prevent unauthorized access to
     *                         the data in the privateMetaData property's array.
     * @return mixed|bool The data, or false if password was not valid or the data does not exist. Note: booleans
     *                    are stored as strings, so if expected data is a boolean, expect it's string value to be
     *                    returned.
     */
    public function getPrivateMetaData(string $index, string $password)
    {
        /* If specified $password matches user's password, and the requested data exists, return it. */
        if (($this->validatePassword($password) === true) && (isset($this->privateMetaData[$index]) === true)) {
            return $this->privateMetaData[$index];
        }
        /* Either the $password was not valid, or the data does not exist, return false. */
        return false;
    }

    /**
     * Set data in the privateMetaData property's array. This array is used to store sensitive user data. Data
     * stored in this array can only be accessed via the getPrivateMetaData() method.
     * @param string $index The index to use for the data in the array.
     * @param mixed $data The data. Note: $data can be of any type, however, booleans will be converted to
     *                    strings. The boolean true is converted to the string "true", and the boolean false
     *                    is converted to the string "false". This is done to prevent confusion when retrieving
     *                    data via the getPrivateMetaData() method since the boolean false is returned by the
     *                    getPrivateMetaData() method when it fails.
     * @param string $password This user's password, required for security. If the specified $password does
     *                         not match this user's password, the data will not be set.
     * @return bool True if data was set, false otherwise.
     */
    public function setPrivateMetaData(string $index, $data, string $password): bool
    {
        if ($this->validatePassword($password) === true) {
            switch (gettype($data)) {
                case 'boolean':
                    $this->privateMetaData[$index] = ($data === true ? 'true' : 'false');
                    break;
                default:
                    $this->privateMetaData[$index] = $data;
                    break;
            }
            return isset($this->privateMetaData[$index]);
        }
        /* Data was not set, return false. */
        return false;
    }

    /**
     * Gets data from the publicMetaData property's array.
     * @param string $index The index of the data to be returned from the publicMetaData property's array.
     * @return mixed|bool The data, or false if the data does not exist. Note: booleans are stored as
     *                    strings, so if expected data is a boolean, expect it's string value to be
     *                    returned.
     */
    public function getPublicMetaData(string $index)
    {
        if (isset($this->publicMetaData[$index]) === true) {
            return $this->publicMetaData[$index];
        }
        return false;
    }

    /**
     * Set data in the publicMetaData property's array.
     * @param string $index The index to use for the data in the array.
     * @param mixed $data The data. Note: $data can be of any type, however, booleans will be converted to
     *                    strings. The boolean true is converted to the string "true", and the boolean false
     *                    is converted to the string "false". This is done to prevent confusion since the boolean
     *                    false is returned by the getPublicMetaData() method when it fails.
     * @return bool True if the data was set, false otherwise.
     */
    public function setPublicMetaData(string $index, $data): bool
    {
        switch (gettype($data)) {
            case 'boolean':
                $this->publicMetaData[$index] = ($data === true ? 'true' : 'false');
                break;
            default:
                $this->publicMetaData[$index] = $data;
                break;
        }
        return isset($this->publicMetaData[$index]);
    }
}
