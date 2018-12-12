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
            $options = $this->defaultOptions;
        }
        parent::__construct($dsn, $username, $passwd, $options);
    }


    /**
     * Query the database.
     * @param string $sql The SQL statement to run.
     * @param array $params (optional) Any parameters that should be included in the query.
     * @return \PDOStatement A PDOStatement object representing the query's prepared statement.
     */
    public function runQuery(string $sql, array $params = array()): \PDOStatement
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
     * Gets an instance of a specified class using data from the specified table to
     * construct the instance.
     * @param string $className The name of the class to return an instance of.
     * @param string $tableName The name of the table whose data will be used to construct the instance.
     * @return mixed An instance of the specified class constructed from the data in the specified table.
     */
    public function getObject(string $className, string $tableName)
    {
        return $this->runQuery("SELECT * FROM {$tableName} LIMIT ?", ['14'])->fetchAll(PDO::FETCH_CLASS, $className);
    }

}
