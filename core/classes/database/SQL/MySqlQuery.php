<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-11
 * Time: 21:44
 */

namespace DarlingCms\classes\database\SQL;


use DarlingCms\interfaces\database\SQL\ISQLQuery;
use \PDO;

class MySqlQuery extends PDO implements ISQLQuery
{
    const DEFAULT_OPTIONS = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    const DEFAULT_CHARSET = 'utf8mb4';

    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.1.0)<br/>
     * Creates a PDO instance representing a connection to a database
     * @link https://php.net/manual/en/pdo.construct.php
     * @param string $dsn The Data Source Name, or DSN, contains the information required to connect to the database.
     *                    In general, a DSN consists of the PDO driver name, followed by a colon, followed by the
     *                    PDO driver-specific connection syntax. Further information is available from the PDO
     *                    driver-specific documentation.
     *
     *                    The dsn parameter supports three different methods of specifying the arguments required
     *                    to create a database connection:
     *
     *                    Driver invocation:
     *                        dsn contains the full DSN.
     *
     *                    URI invocation:
     *                        dsn consists of uri: followed by a URI that defines the location of a file containing
     *                                             the DSN string. The URI can specify a local file or a remote URL.
     *                    Aliasing
     *                        dsn consists of a name name that maps to pdo.dsn.name in php.ini defining the DSN
     *                        string.
     *                        Note: The alias must be defined in php.ini, and not .htaccess or httpd.conf
     * @param string $username [optional] The user name for the DSN string. This parameter is optional for
     *                                    some PDO drivers.
     * @param string $passwd [optional] The password for the DSN string. This parameter is optional for
     *                                  some PDO drivers.
     * @param array $options [optional] A key=>value array of driver-specific connection options.
     */
    public function __construct(string $dsn, string $username = '', string $passwd = '', array $options = array())
    {
        if (empty($options) === true) {
            $options = self::DEFAULT_OPTIONS;
        }
        parent::__construct($dsn, $username, $passwd, $options);
    }


    /**
     * Query the database.
     * @param string $sql The SQL statement to run.
     * @param array $params (optional) An array of values with as many elements as there are bound parameters
     * in the SQL statement being executed. All values are treated as PDO::PARAM_STR. Multiple values cannot
     * be bound to a single parameter; for example, it is not allowed to bind two values to a single named
     * parameter in an IN() clause. Binding more values than specified is not possible; if more keys exist
     * in input_parameters than in the SQL specified in the PDO::prepare(), then the statement will fail
     * and an error is emitted.
     * @return \PDOStatement If the database server successfully prepares the statement, PDO::prepare()
     *                       returns a PDOStatement object. If the database server cannot successfully
     *                       prepare the statement, PDO::prepare() returns FALSE or emits PDOException
     *                       (depending on error handling)...
     * @see \PDO::prepare()
     * @see https://secure.php.net/manual/en/pdo.prepare.php
     * @see \PDOStatement::execute()
     * @see https://secure.php.net/manual/en/pdostatement.execute.php
     */
    public function executeQuery(string $sql, array $params = array()): \PDOStatement
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }


    /**
     * Generate a DSN string based on specified parameters.
     * @param string $host The host.
     * @param string $dbName The database name.
     * @param string $charset (optional) The charset. Defaults to MySqlQuery::DEFAULT_CHARSET.
     * @return string The generated DSN string.
     */
    public static function getDsn(string $host, string $dbName, string $charset = self::DEFAULT_CHARSET): string
    {
        return "mysql:host={$host};dbname={$dbName};charset={$charset}";
    }

    /**
     * Gets an instance of a specified class using data returned by the specified SQL query to
     * construct the instance.
     * @param string $sql The SQL query to run.
     * @param string $className The name of the class to return an instance of.
     * @param array $params Array of query parameters.
     * @return mixed An instance of the specified class constructed from the data in the specified table.
     */
    public function getClass(string $sql, string $className, array $params = array())
    {
        return $this->executeQuery($sql, $params)->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $className);
    }

}
