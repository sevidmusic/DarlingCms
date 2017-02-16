<?php
/**
 * Copyright (c) 2017. Sevi Donnelly Foreman
 */

/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2/2/17
 * Time: 6:56 PM
 */

namespace DarlingCms\abstractions\startup;

/**
 * Class Astartup. Abstract implementation of the \DarlingCms\interfaces\startup\Istartup
 * interface designed to accommodate child implementation's startup and shutdown logic.
 *
 * Also provides concrete methods for internal error handling.
 *
 * @package DarlingCms\abstractions\startup
 */
abstract class Astartup implements \DarlingCms\interfaces\startup\Istartup
{
    /**
     * @var bool $errorReporting Boolean representing whether or not error reporting is on or off, true
     *                           or false respectively.
     */
    protected $errorReporting;
    /**
     * @var array Associative array of errors since the last call to startup(), shutdown(),
     *            or restart().
     */
    private $errors;

    /**
     * Turns error reporting on or off.
     *
     * @param bool $value True to switch error reporting on, false to turn error reporting off.
     *
     * @return bool Returns true if error reporting is on, false otherwise.
     */
    public function errorReporting(bool $value)
    {
        /* Set error reporting to the specified $value. */
        $this->errorReporting = $value;
        /* Return error reporting state. */
        return $this->errorReporting;
    }

    /**
     * @inheritDoc
     */
    public function restart()
    {
        /* Reset the errors array. */
        $this->resetErrors();

        /* If shutdown is successful, startup again. */
        if ($this->shutdown() === true) {
            return $this->startup();
        }

        /* Restart failed, register error. */
        $this->registerError('Restart error', 'Restart was unsuccessful.', $this);

        /* Restart failed, return false. */
        return false;
    }

    /**
     * Resets the errors array.
     *
     * @return bool Returns true if errors array was successfully reset, false otherwise.
     */
    private function resetErrors()
    {
        /* Unset the errors array before re-initializing it. */
        unset($this->errors);

        /* Initialize the errors array. */
        $this->errors = array();

        /* Return true if errors array was reset, i.e., "empty", false otherwise. */
        return empty($this->errors);
    }

    /**
     * @inheritDoc
     */
    public function shutdown()
    {
        /* Reset the errors array. */
        $this->resetErrors();

        /* Run implementations shutdown logic. */
        return $this->stop();
    }

    /**
     * Process any shutdown logic specific to the implementation.
     *
     * @return bool True if implementations specific shutdown logic
     *              was processed successfully, false otherwise.
     */
    abstract protected function stop();

    /**
     * @inheritDoc
     */
    public function startup()
    {
        /* Reset the errors array. */
        $this->resetErrors();

        /* Run implementations startup logic. */
        return $this->run();
    }

    /**
     * Process any startup logic specific to the implementation.
     *
     * @return bool True if implementations specific startup logic
     *              was processed successfully, false otherwise.
     */
    abstract protected function run();

    /**
     * Register an error in the errors array.
     *
     * @param string $index An index to associate the error with in the errors array.
     * @param string $message The error message.
     * @param mixed $data (optional) Any data associated with the error.
     *
     * @return bool True if error was registered successfully, false otherwise.
     */
    protected function registerError(string $index, string $message, $data = null)
    {
        /* Register error differently based on whether or not $data was provided. */
        switch ($data) {
            case null:
                $this->errors[$index] = $message;
                break;
            default:
                $this->errors[$index] = array('message' => $message, 'data' => $data);
        }
        /* Return true if the error was registered, false otherwise. */
        return isset($this->errors[$index]);
    }

    /**
     * If error reporting is turned on, display any errors that occurred during last call to
     * startup(), shutdown(), or restart().
     */
    public function displayErrors()
    {
        /* Check if error reporting is on. */
        if ($this->errorReporting === true) {
            /* Loop through and display each error. */
            foreach ($this->getErrors() as $index => $error) {
                /**
                 * If $error is an array, the error message and error data should extracted for display,
                 * otherwise, just display the error message.
                 */
                switch (is_array($error)) {
                    case true:
                        /* Extract and display error message from $error array. Use $index to indicate
                           which app caused the error. */
                        echo "$index: {$error['message']}";
                        /* var_dump() the error data from the $error array. */
                        var_dump($error['data']);
                        break;
                    case false:
                        /* $error is the error message, so, display $error. Use $index to indicate which
                           app caused the error. */
                        echo "$index: $error";
                        break;
                }
            }
        }
    }

    /**
     * Returns the errors array, an associative array of errors
     * that have occurred since the last call to startup(), shutdown(),
     * or restart().
     *
     * @return array Array of errors that have occurred since last startup, shutdown,
     *               or restart.
     */
    public function getErrors()
    {
        return $this->errors;
    }
}