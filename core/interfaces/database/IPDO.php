<?php
/**
 * NOTE: This interface is just a reflection of PHP's PDO class. It is not an original work related to the
 * Darling Cms. The interface is defined for the Darling Cms as the Darling Cms aims to be strictly object
 * oriented at it's core, and interfaces provide a useful tool for enforcing implementation contracts.
 * Source @see http://php.net/manual/en/class.pdo.php.
 * Date: 2018-12-11
 * Time: 20:49
 */

namespace DarlingCms\interfaces\database;

use \PDO as PDO;

/**
 * Interface IPDO. This interface serves to define the contract of a PDO object reflective of the PDO
 * object made available by PHP. Sadly, PHP does not define such an interface, or at least I have not
 * discovered one yet, so this interface is here to fill the interface void for PHP's PDO.
 * All the methods defined in this interface mirror those of the PDO object, NO ADDITIONAL METHODS SHOULD EVER
 * BE ADDED TO THIS CONTRACT, only methods defined by the PDO class itself should be reflected. If additional
 * methods are needed, define a new interface that extends this interface and define the additional methods
 * in the contract of the extending interface.
 * @package DarlingCms\interfaces\database
 */
interface IPDO
{
    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.1.0)<br/>
     * Creates a PDO instance representing a connection to a database
     * @link https://php.net/manual/en/pdo.construct.php
     * @param string $dsn
     * @param string $username [optional]
     * @param string $passwd [optional]
     * @param array $options [optional]
     */
    public function __construct(string $dsn, string $username, string $passwd, array $options);

    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.1.0)<br/>
     * Prepares a statement for execution and returns a statement object
     * @link https://php.net/manual/en/pdo.prepare.php
     * @param string $statement <p>
     * This must be a valid SQL statement for the target database server.
     * </p>
     * @param array $driver_options [optional] <p>
     * This array holds one or more key=&gt;value pairs to set
     * attribute values for the <b>PDOStatement</b> object that this method
     * returns. You would most commonly use this to set the
     * <b>PDO::ATTR_CURSOR</b> value to
     * <b>PDO::CURSOR_SCROLL</b> to request a scrollable cursor.
     * Some drivers have driver specific options that may be set at
     * prepare-time.
     * </p>
     * @return PDOStatement|bool If the database server successfully prepares the statement,
     * <b>PDO::prepare</b> returns a
     * <b>PDOStatement</b> object.
     * If the database server cannot successfully prepare the statement,
     * <b>PDO::prepare</b> returns <b>FALSE</b> or emits
     * <b>PDOException</b> (depending on error handling).
     * </p>
     * <p>
     * Emulated prepared statements does not communicate with the database server
     * so <b>PDO::prepare</b> does not check the statement.
     */
    public function prepare($statement, array $driver_options = array());

    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.1.0)<br/>
     * Initiates a transaction
     * <p>
     * Turns off autocommit mode. While autocommit mode is turned off,
     * changes made to the database via the PDO object instance are not committed
     * until you end the transaction by calling {@link PDO::commit()}.
     * Calling {@link PDO::rollBack()} will roll back all changes to the database and
     * return the connection to autocommit mode.
     * </p>
     * <p>
     * Some databases, including MySQL, automatically issue an implicit COMMIT
     * when a database definition language (DDL) statement
     * such as DROP TABLE or CREATE TABLE is issued within a transaction.
     * The implicit COMMIT will prevent you from rolling back any other changes
     * within the transaction boundary.
     * </p>
     * @link https://php.net/manual/en/pdo.begintransaction.php
     * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
     * @throws PDOException If there is already a transaction started or
     * the driver does not support transactions <br/>
     * <b>Note</b>: An exception is raised even when the <b>PDO::ATTR_ERRMODE</b>
     * attribute is not <b>PDO::ERRMODE_EXCEPTION</b>.
     */
    public function beginTransaction();

    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.1.0)<br/>
     * Commits a transaction
     * @link https://php.net/manual/en/pdo.commit.php
     * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
     */
    public function commit();

    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.1.0)<br/>
     * Rolls back a transaction
     * @link https://php.net/manual/en/pdo.rollback.php
     * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
     */
    public function rollBack();

    /**
     * (PHP 5 &gt;= 5.3.3, Bundled pdo_pgsql, PHP 7)<br/>
     * Checks if inside a transaction
     * @link https://php.net/manual/en/pdo.intransaction.php
     * @return bool <b>TRUE</b> if a transaction is currently active, and <b>FALSE</b> if not.
     */
    public function inTransaction();

    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.1.0)<br/>
     * Set an attribute
     * @link https://php.net/manual/en/pdo.setattribute.php
     * @param int $attribute
     * @param mixed $value
     * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
     */
    public function setAttribute($attribute, $value);

    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.1.0)<br/>
     * Execute an SQL statement and return the number of affected rows
     * @link https://php.net/manual/en/pdo.exec.php
     * @param string $statement <p>
     * The SQL statement to prepare and execute.
     * </p>
     * <p>
     * Data inside the query should be properly escaped.
     * </p>
     * @return int <b>PDO::exec</b> returns the number of rows that were modified
     * or deleted by the SQL statement you issued. If no rows were affected,
     * <b>PDO::exec</b> returns 0.
     * </p>
     * This function may
     * return Boolean <b>FALSE</b>, but may also return a non-Boolean value which
     * evaluates to <b>FALSE</b>. Please read the section on Booleans for more
     * information. Use the ===
     * operator for testing the return value of this
     * function.
     * <p>
     * The following example incorrectly relies on the return value of
     * <b>PDO::exec</b>, wherein a statement that affected 0 rows
     * results in a call to <b>die</b>:
     * <code>
     * $db->exec() or die(print_r($db->errorInfo(), true));
     * </code>
     */
    public function exec($statement);


    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.1.0)<br/>
     * Returns the ID of the last inserted row or sequence value
     * @link https://php.net/manual/en/pdo.lastinsertid.php
     * @param string $name [optional] <p>
     * Name of the sequence object from which the ID should be returned.
     * </p>
     * @return string If a sequence name was not specified for the <i>name</i>
     * parameter, <b>PDO::lastInsertId</b> returns a
     * string representing the row ID of the last row that was inserted into
     * the database.
     * </p>
     * <p>
     * If a sequence name was specified for the <i>name</i>
     * parameter, <b>PDO::lastInsertId</b> returns a
     * string representing the last value retrieved from the specified sequence
     * object.
     * </p>
     * <p>
     * If the PDO driver does not support this capability,
     * <b>PDO::lastInsertId</b> triggers an
     * IM001 SQLSTATE.
     */
    public function lastInsertId($name = null);

    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.1.0)<br/>
     * Fetch the SQLSTATE associated with the last operation on the database handle
     * @link https://php.net/manual/en/pdo.errorcode.php
     * @return mixed an SQLSTATE, a five characters alphanumeric identifier defined in
     * the ANSI SQL-92 standard. Briefly, an SQLSTATE consists of a
     * two characters class value followed by a three characters subclass value. A
     * class value of 01 indicates a warning and is accompanied by a return code
     * of SQL_SUCCESS_WITH_INFO. Class values other than '01', except for the
     * class 'IM', indicate an error. The class 'IM' is specific to warnings
     * and errors that derive from the implementation of PDO (or perhaps ODBC,
     * if you're using the ODBC driver) itself. The subclass value '000' in any
     * class indicates that there is no subclass for that SQLSTATE.
     * </p>
     * <p>
     * <b>PDO::errorCode</b> only retrieves error codes for operations
     * performed directly on the database handle. If you create a PDOStatement
     * object through <b>PDO::prepare</b> or
     * <b>PDO::query</b> and invoke an error on the statement
     * handle, <b>PDO::errorCode</b> will not reflect that error.
     * You must call <b>PDOStatement::errorCode</b> to return the error
     * code for an operation performed on a particular statement handle.
     * </p>
     * <p>
     * Returns <b>NULL</b> if no operation has been run on the database handle.
     */
    public function errorCode();

    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.1.0)<br/>
     * Fetch extended error information associated with the last operation on the database handle
     * @link https://php.net/manual/en/pdo.errorinfo.php
     * @return array <b>PDO::errorInfo</b> returns an array of error information
     * about the last operation performed by this database handle. The array
     * consists of the following fields:
     * <tr valign="top">
     * <td>Element</td>
     * <td>Information</td>
     * </tr>
     * <tr valign="top">
     * <td>0</td>
     * <td>SQLSTATE error code (a five characters alphanumeric identifier defined
     * in the ANSI SQL standard).</td>
     * </tr>
     * <tr valign="top">
     * <td>1</td>
     * <td>Driver-specific error code.</td>
     * </tr>
     * <tr valign="top">
     * <td>2</td>
     * <td>Driver-specific error message.</td>
     * </tr>
     * </p>
     * <p>
     * If the SQLSTATE error code is not set or there is no driver-specific
     * error, the elements following element 0 will be set to <b>NULL</b>.
     * </p>
     * <p>
     * <b>PDO::errorInfo</b> only retrieves error information for
     * operations performed directly on the database handle. If you create a
     * PDOStatement object through <b>PDO::prepare</b> or
     * <b>PDO::query</b> and invoke an error on the statement
     * handle, <b>PDO::errorInfo</b> will not reflect the error
     * from the statement handle. You must call
     * <b>PDOStatement::errorInfo</b> to return the error
     * information for an operation performed on a particular statement handle.
     */
    public function errorInfo();

    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.2.0)<br/>
     * Retrieve a database connection attribute
     * @link https://php.net/manual/en/pdo.getattribute.php
     * @param int $attribute <p>
     * One of the PDO::ATTR_* constants. The constants that
     * apply to database connections are as follows:
     * PDO::ATTR_AUTOCOMMIT
     * PDO::ATTR_CASE
     * PDO::ATTR_CLIENT_VERSION
     * PDO::ATTR_CONNECTION_STATUS
     * PDO::ATTR_DRIVER_NAME
     * PDO::ATTR_ERRMODE
     * PDO::ATTR_ORACLE_NULLS
     * PDO::ATTR_PERSISTENT
     * PDO::ATTR_PREFETCH
     * PDO::ATTR_SERVER_INFO
     * PDO::ATTR_SERVER_VERSION
     * PDO::ATTR_TIMEOUT
     * </p>
     * @return mixed A successful call returns the value of the requested PDO attribute.
     * An unsuccessful call returns null.
     */
    public function getAttribute($attribute);

    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.2.1)<br/>
     * Quotes a string for use in a query.
     * @link https://php.net/manual/en/pdo.quote.php
     * @param string $string <p>
     * The string to be quoted.
     * </p>
     * @param int $parameter_type [optional] <p>
     * Provides a data type hint for drivers that have alternate quoting styles.
     * </p>
     * @return string a quoted string that is theoretically safe to pass into an
     * SQL statement. Returns <b>FALSE</b> if the driver does not support quoting in
     * this way.
     */
    public function quote($string, $parameter_type = PDO::PARAM_STR);

    public function __wakeup();

    public function __sleep();

    /**
     * (PHP 5 &gt;= 5.1.3, PHP 7, PECL pdo &gt;= 1.0.3)<br/>
     * Return an array of available PDO drivers
     * @link https://php.net/manual/en/pdo.getavailabledrivers.php
     * @return array <b>PDO::getAvailableDrivers</b> returns an array of PDO driver names. If
     * no drivers are available, it returns an empty array.
     */
    public static function getAvailableDrivers();

    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo_sqlite &gt;= 1.0.0)<br/>
     * Registers a User Defined Function for use in SQL statements
     * @link https://php.net/manual/en/pdo.sqlitecreatefunction.php
     * @param string $function_name <p>
     * The name of the function used in SQL statements.
     * </p>
     * @param callable $callback <p>
     * Callback function to handle the defined SQL function.
     * </p>
     * @param int $num_args [optional] <p>
     * The number of arguments that the SQL function takes. If this parameter is -1,
     * then the SQL function may take any number of arguments.
     * </p>
     * @param int $flags [optional] <p>
     * A bitwise conjunction of flags. Currently, only <b>PDO::SQLITE_DETERMINISTIC</b> is supported,
     * which specifies that the function always returns the same result given the same inputs within
     * a single SQL statement.
     * </p>
     * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
     */
    /**
     * WARNNING: Defining the following methods as part of the interface causes the following error when
     * implementing this interface:
     * PHP Fatal error: Class IMPLEMENTER contains 1 abstract method and must therefore be declared abstract
     * or implement the remaining methods (IMPLEMENTER::sqliteCreateFunction)...
     * @todo ! Find a way to properly define the query() method as part of the IPDO interface.
     */
    //public function sqliteCreateFunction($function_name, $callback, $num_args = -1, $flags = 0);

    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.2.0)<br/>
     * Executes an SQL statement, returning a result set as a PDOStatement object
     * @link https://php.net/manual/en/pdo.query.php
     * @param string $statement <p>
     * The SQL statement to prepare and execute.
     * </p>
     * <p>
     * Data inside the query should be properly escaped.
     * </p>
     * @param int $mode <p>
     * The fetch mode must be one of the PDO::FETCH_* constants.
     * </p>
     * @param mixed $arg3 <p>
     * The second and following parameters are the same as the parameters for PDOStatement::setFetchMode.
     * </p>
     * @param array $ctorargs [optional] <p>
     * Arguments of custom class constructor when the <i>mode</i>
     * parameter is set to <b>PDO::FETCH_CLASS</b>.
     * </p>
     * @return PDOStatement|false <b>PDO::query</b> returns a PDOStatement object, or <b>FALSE</b>
     * on failure.
     * @see PDOStatement::setFetchMode For a full description of the second and following parameters.
     */
    /**
     * WARNNING: Defining the query() method as part of the interface causes the following error when
     * implementing this interface:
     * PHP Fatal error: Declaration of PDO::query() must be compatible with IMPLEMENTER
     * @bug please fix
     */
    //public function query($statement, $mode = PDO::ATTR_DEFAULT_FETCH_MODE, $arg3 = null, array $ctorargs = array());


}
