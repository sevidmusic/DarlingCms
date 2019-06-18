<?php
/**
 * Created by Sevi Darling.
 * Date: 2018-12-11
 * Time: 21:44
 */

namespace DarlingCms\classes\database\SQL;


use DarlingCms\interfaces\database\SQL\ISqlQuery;
use \PDO;
use PDOStatement;

/**
 * Class MySqlQuery. Defines an implementation of the ISQLQuery interface that
 * extends PHP's PDO class. This class can be used to run queries against
 * a MySql database.
 * @package DarlingCms\classes\database\SQL
 *
 * @see PDO
 * @see ISqlQuery
 * @see MySqlQuery
 * @see MySqlQuery::DEFAULT_OPTIONS
 * @see MySqlQuery::DEFAULT_CHARSET
 * @see MySqlQuery::__construct()
 * @see MySqlQuery::executeQuery()
 * @see MySqlQuery::getDsn()
 */
class MySqlQuery extends PDO implements ISqlQuery
{
    /**
     * @var array DEFAULT_OPTIONS Array of default PDO options that can be assigned
     *                            to the __construct() methods $options parameter.
     *                            The array is structured as follows:
     *                            <br>array(
     *                              <br>PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
     *                              <br>PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
     *                              <br>PDO::ATTR_EMULATE_PREPARES => false
     *                            <br>);
     *
     * @see PDO::ATTR_ERRMODE
     * @see PDO::ERRMODE_EXCEPTION
     * @see PDO::ATTR_DEFAULT_FETCH_MODE
     * @see PDO::FETCH_ASSOC
     * @see PDO::ATTR_EMULATE_PREPARES
     */
    const DEFAULT_OPTIONS = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    /**
     * @var string DEFAULT_CHARSET Default charset assigned to the
     *                             MySqlQuery::getDsn() method's
     *                             $charset parameter.
     *
     * @see MySqlQuery::getDsn()
     */
    const DEFAULT_CHARSET = 'utf8mb4';

    /**
     * @var string Unique id that can be used to identify MySqlQuery instances when
     *             debugging. Note: This property is specifically intended for use
     *             when debugging.
     */
    protected $uid;

    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.1.0)<br/>
     * Creates a PDO instance representing a connection to a database.
     *
     * @link https://php.net/manual/en/pdo.construct.php
     *
     * @param string $dsn The Data Source Name, or DSN, contains the information required
     *                    to connect to the database. In general, a DSN consists of the
     *                    PDO driver name, followed by a colon, followed by the PDO
     *                    driver-specific connection syntax. Further information is
     *                    available from the PDO driver-specific documentation.
     *
     *                    The dsn parameter supports three different methods of specifying
     *                    the arguments required to create a database connection:
     *
     *                    Driver invocation:
     *                        dsn contains the full DSN.
     *
     *                    URI invocation:
     *                        dsn consists of a uri, followed by a URI that defines the
     *                        location of a file containing the DSN string. The URI can
     *                        specify a local file or a remote URL.
     *                    Aliasing
     *                        dsn consists of a name that maps to pdo.dsn.name in
     *                        php.ini defining the DSN string.
     *                        Note: The alias must be defined in php.ini, and not
     *                              .htaccess or httpd.conf
     *
     * @param string $username [optional] The user name for the DSN string. This parameter
     *                                    is optional for some PDO drivers.
     *
     * @param string $passwd [optional] The password for the DSN string. This parameter is
     *                                  optional for some PDO drivers.
     *
     * @param array $options [optional] A key=>value array of driver-specific connection
     *                                  options.
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
     *
     * @param string $sql The SQL statement to run.
     *
     * @param array $params (optional) An array of values with as many elements as there
     *                                 are bound parameters in the SQL statement being
     *                                 executed. All values are treated as PDO::PARAM_STR.
     *                                 Multiple values cannot be bound to a single
     *                                 parameter; for example, it is not allowed to bind
     *                                 two values to a single named parameter in an IN()
     *                                 clause. Binding more values than specified is not
     *                                 possible; if more keys exist in input_parameters
     *                                 than in the SQL specified in the PDO::prepare(),
     *                                 then the statement will fail and an error is emitted.
     *
     * @return PDOStatement If the database server successfully prepares the statement,
     *                      PDO::prepare() returns a PDOStatement object. If the database
     *                      server cannot successfully prepare the statement, PDO::prepare()
     *                      returns FALSE or emits PDOException (depending on error handling).
     *
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
     *
     * @param string $host The host.
     *
     * @param string $dbName The database name.
     *
     * @param string $charset (optional) The charset. Defaults to MySqlQuery::DEFAULT_CHARSET.
     *
     * @return string The generated DSN string.
     */
    public static function getDsn(string $host, string $dbName, string $charset = self::DEFAULT_CHARSET): string
    {
        return "mysql:host={$host};dbname={$dbName};charset={$charset}";
    }

}
