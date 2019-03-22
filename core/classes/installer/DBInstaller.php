<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2019-03-16
 * Time: 12:55
 */

namespace DarlingCms\classes\installer;


use DarlingCms\classes\database\SQL\MySqlQuery;
use DarlingCms\interfaces\config\IDBConfig;
use DarlingCms\interfaces\installer\IInstaller;

/**
 * Class DBInstaller. Defines an implementation of the IInstaller interface that can be used
 * to install or uninstall mysql databases and related database users.
 * @package DarlingCms\classes\installer
 */
class DBInstaller implements IInstaller
{
    /**
     * @var MySqlQuery $mySqlQuery MySqlQuery implementation instance used to install/uninstall the databases.
     */
    private $mySqlQuery;
    /**
     * @var \SplObjectStorage Object map of the IDBConfig implementation instances that represent
     *                        the databases this DBInstaller instance is responsible for installing
     *                        and uninstalling.
     */
    private $dbConfigs;

    /**
     * DBInstaller constructor. Injects the MySqlQuery implementation instance
     * used to install/uninstall the databases. Attaches the provided IDBConfig
     * implementation instances that represent the databases this DBInstaller is
     * responsible for installing and uninstalling.
     * @param MySqlQuery $mySqlQuery MySqlQuery implementation instance used to install/uninstall the databases.
     * @param IDBConfig ...$dbConfigs IDBConfig implementation instances that represent
     *                                the databases this DBInstaller instance is responsible
     *                                for installing and uninstalling.
     */
    public function __construct(MySqlQuery $mySqlQuery, IDBConfig...$dbConfigs)
    {
        $this->mySqlQuery = $mySqlQuery;
        $this->dbConfigs = new \SplObjectStorage();
        foreach ($dbConfigs as $dbConfig) {
            $this->dbConfigs->attach($dbConfig);
        }
    }


    /**
     * Install the databases and the related database users.
     * @return bool True if installation was successful, false otherwise.
     */
    public function install(): bool
    {
        $status = array();
        /**
         * @var IDBConfig $dbConfig
         */
        foreach ($this->dbConfigs as $dbConfig) {
            try {
                $this->installDatabase($dbConfig);
                $this->installDatabaseUser($dbConfig);
            } catch (\Exception $exception) {
                error_log('DBInstaller Error: Failed to install the ' . $dbConfig->getDBName() . PHP_EOL . '(Error Message) ' . $exception->getMessage());
                array_push($status, false);
            }
        }
        return !in_array(false, $status, true);
    }

    /**
     * Installs a database.
     * @param IDBConfig $dbConfig The IDBConfig implementation instance that represents the database
     *                            to install.
     */
    private function installDatabase(IDBConfig $dbConfig): void
    {
        /* Create the database. */
        $this->mySqlQuery->executeQuery("CREATE DATABASE `" . $dbConfig->getDBName() . "`");
    }

    /**
     * Installs a database user.
     * @param IDBConfig $dbConfig The IDBConfig implementation instance that represents the database
     *                            the user is related to, i.e., the IDBConfig implementation instance
     *                            that defines the database user's username and password.
     */
    private function installDatabaseUser(IDBConfig $dbConfig): void
    {
        /* Create new user. */
        $this->mySqlQuery->executeQuery("CREATE USER '" . $dbConfig->getDBUsername() . "'@'" . $dbConfig->getDBHostName() . "' IDENTIFIED BY '" . $dbConfig->getDBUserPassword() . "'");
        /* Grant the user all privileges on specified database. */
        $this->mySqlQuery->executeQuery("GRANT ALL ON `" . $dbConfig->getDBName() . "`.* TO '" . $dbConfig->getDBUsername() . "'@'" . $dbConfig->getDBHostName() . "'");
        /* Flush privileges. */
        $this->mySqlQuery->executeQuery("FLUSH PRIVILEGES");
    }

    /**
     * Un-install the databases and the related database users.
     * @return bool True if un-installation was successful, false otherwise.
     */
    public function uninstall(): bool
    {
        // TODO: Implement uninstall() method.
        $status = array();
        /**
         * @var IDBConfig $dbConfig
         */
        foreach ($this->dbConfigs as $dbConfig) {
            try {
                $this->uninstallDatabase($dbConfig);
                $this->uninstallDatabaseUser($dbConfig);
            } catch (\Exception $exception) {
                error_log('DBInstaller Error: Failed to uninstall the ' . $dbConfig->getDBName() . PHP_EOL . '(Error Message) ' . $exception->getMessage());
                array_push($status, false);
            }
        }
        var_dump('Uninstall Status: ' . (!in_array(false, $status, true) ? 'True' : 'False'));
        return !in_array(false, $status, true);
    }

    /**
     * Un-installs a database.
     * @param IDBConfig $dbConfig The IDBConfig implementation instance that represents the database
     *                            to un-install.
     */
    private function uninstallDatabase(IDBConfig $dbConfig): void
    {
        $this->mySqlQuery->executeQuery("DROP DATABASE `" . $dbConfig->getDBName() . "`");
    }

    /**
     * Un-installs a database user.
     * @param IDBConfig $dbConfig The IDBConfig implementation instance that represents the database
     *                            the user is related to, i.e., the IDBConfig implementation instance
     *                            that defines the database user's username and password.
     */
    private function uninstallDatabaseUser(IDBConfig $dbConfig): void
    {
        $this->mySqlQuery->executeQuery("DROP USER '" . $dbConfig->getDBUsername() . "'@'" . $dbConfig->getDBHostName() . "'");
    }


}
