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
use PDOStatement;

/**
 * Class MySqlQuery. Defines an implementation of the ISQLQuery interface that
 * extends PHP's PDO class. This class can be used to run queries against
 * a MySql database.
 * @package DarlingCms\classes\database\SQL
 */
class MySqlQuery extends PDO implements ISQLQuery
{
    const DEFAULT_OPTIONS = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    const DEFAULT_CHARSET = 'utf8mb4';

    /**
     * @var string Unique id that can be used to identify MySqlQuery instances when debugging.
     *             Note: This property is specifically intended for use when debugging.
     */
    private $uid;

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
        $this->uid = password_hash(base64_encode(serialize(bcadd(rand(1000000, 9999999), rand(1000000, 9999999)))), PASSWORD_DEFAULT);
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
     * @return PDOStatement If the database server successfully prepares the statement, PDO::prepare()
     *                       returns a PDOStatement object. If the database server cannot successfully
     *                       prepare the statement, PDO::prepare() returns FALSE or emits PDOException
     *                       (depending on error handling)...
     * @see \PDO::prepare()
     * @see https://secure.php.net/manual/en/pdo.prepare.php
     * @see \PDOStatement::execute()
     * @see https://secure.php.net/manual/en/pdostatement.execute.php
     */
    public function executeQuery(string $sql, array $params = array()): PDOStatement
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
     * @param array $ctor_args Array of parameters to pass to the class being instantiated's __construct() method.
     * @return mixed|object An instance of the specified class constructed from the data in the specified table.
     * Hint:
     * This method determines which fetch style to use based on whether or not the $ctor_args
     * array is empty.
     *
     * If $ctor_args is empty, this method will use the PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE fetch style options.
     * This is best if the class being instantiated from the query results implements a NON EMPTY* __set() method.
     * This style gives preference to the __set() method as the __set() method will be called after the __construct() method
     * has been called, and the values have been set by PDO.
     * Action Order: __construct() called, PDO sets values, __set() called().
     *
     * If $ctor_args is not empty, this method will use the PDO::FETCH_CLASS option alone. This is best
     * if the class being instantiated does not implement a __set() method. This style gives preference to
     * the __construct() method as the __construct() method will be called after property values have been set.
     * Action Order: PDO sets values, __construct() called
     *
     * The documentation on php.net is vague about much of this if it is documented at all. I had to play
     * with this a lot to see the consequences of different fetch style option combinations. Please note
     * that these are my own dev notes, they do not serve as a sound documentation of the behavior of
     * PDO, the fetchAll() method, how values are set from a query when using FETCH_CLASS, or the order
     * of method calls when instantiating an object from a fetchAll(PDO::FETCH_CLASS) query. These are
     * just my personal observations.
     *
     * *...NON EMPTY...* Hint: An empty __set() method can be implemented by classes that wish to prevent
     *                         PDO from setting undeclared property values.
     *
     * @todo This should be part of the ISqlObjectQuery interface, therefore this method should be moved to a new class called MySqlObjectQuery and classes that build objects should implement that class instead of this class. this class should be specifically for generic MySql queries.
     */
    public function getClass(string $sql, string $className, array $params = array(), array $ctor_args = array())
    {
        $fetchStyle = (empty($ctor_args) ? PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE : PDO::FETCH_CLASS);
        return $this->executeQuery($sql, $params)->fetchAll($fetchStyle, $className, $ctor_args);
    }

}
