<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 4/29/18
 * Time: 3:32 PM
 */

namespace DarlingCms\classes\startup;

use DarlingCms\interfaces\accessControl\IAppConfig;
use DarlingCms\interfaces\startup\IAppStartup;

/**
 * Class AppStartup. Defines an implementation of the IAppStartup interface that is responsible for starting up
 * a single Darling Cms app.
 * @package DarlingCms\classes\startup
 * @see AppStartup::getCssPaths()
 * @see AppStartup::getJsPaths()
 * @see AppStartup::getAppOutput()
 * @see AppStartup::getPaths()
 * @see AppStartup::startup()
 * @see AppStartup::setCssPaths()
 * @see AppStartup::setJsPaths()
 * @see AppStartup::shutdown()
 * @see AppStartup::restart()
 */
class AppStartup implements IAppStartup
{
    /**
     * @var array Array of paths set by the __construct() method, and, if startup was successful ,the startup() method.
     */
    private $paths = array();

    /**
     * @var IAppConfig Local instance of an object that implements the IAppConfig interface. This property's value
     * is set by the __construct() method upon instantiation.
     */
    private $appConfig;

    /**
     * @var string The app's output, or an empty string if there is no output, or app failed to startup.
     */
    private $appOutput = '';

    /**
     * AppStartup constructor. Injects an instance of an object that implements the IAppConfig interface, determines
     * the path to the Darling Cms root directory, and initializes the $paths property's array.
     * @param IAppConfig $appConfig
     */
    public function __construct(IAppConfig $appConfig)
    {
        $this->appConfig = $appConfig;
        $rootDir = str_replace('core/classes/startup', '', __DIR__);
        $this->paths = array(
            'rootDir' => $rootDir,
            'rootUrl' => (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/DarlingCms/',
            'appsDir' => $rootDir . 'apps/',
            'themesDir' => 'themes/',
            'jsDir' => 'js/',
            'cssPaths' => array(),
            'jsPaths' => array(),
        );
    }

    /**
     * Returns an array of paths to the css files belonging to the themes assigned to the app, or an empty array
     * if the app is not assigned any themes, or startup failed.
     * @return array Array of paths to the css files belonging to the themes assigned to the app, or an empty array
     * if the app is not assigned any themes, or startup failed.
     */
    public function getCssPaths(): array
    {
        return $this->paths['cssPaths'];
    }

    /**
     * Returns an array of paths to the javascript files belonging to the javascript libraries assigned to the app,
     * or an empty array if the app is not assigned any javascript libraries, or startup failed.
     * @return array Array of paths to the javascript files belonging to the javascript libraries assigned to the app,
     * or an empty array if the app is not assigned any javascript libraries, or startup failed.
     */
    public function getJsPaths(): array
    {
        return $this->paths['jsPaths'];
    }

    /**
     * Returns the app's output. If app has no output, or the app failed to startup, this method will
     * return an empty string.
     * @return string The app's output.
     */
    public function getAppOutput(): string
    {
        return $this->appOutput;
    }

    /**
     * Returns an array of the following paths: The path to the Darling Cms root directory, the root url, the path
     * to the apps directory, the relative path to the themes directory, and the relative path to the js directory.
     * Additionally this array is assigned an array of css file paths, and an array of javascript file paths,
     * belonging to the themes and javascript libraries assigned to the app, respectively.
     * Note: If startup is not successful, the cssPaths and jsPaths arrays will be empty.
     *
     * The paths are indexed by the following indexes:
     *
     * 'rootDir' : The path to the Darling Cms root directory.
     *
     * 'rootUrl' : The site's root url.
     *
     * 'appsDir' : The path to the Darling Cms apps directory.
     *
     * 'themesDir' : The relative path to the Darling Cms themes directory, i.e., '/themes'.
     *
     * 'jsDir' : The relative path to the Darling Cms js directory, i.e., '/js'.
     *
     * 'cssPaths' : Array of paths to the css files belonging to the themes assigned to the app, or an empty array if
     *              the app is not assigned any themes, or startup failed.
     *
     * 'jsPaths' : Array of paths to the javascript files belonging to the javascript libraries assigned to the app,
     *             or an empty array if the app is not assigned any javascript libraries, or startup failed.
     * @return array The array of paths assigned to the $paths property's array.
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * Handles startup logic. Specifically, loads the app's output, and sets the paths to the css files, and
     * javascript files, belonging to the themes and javascript libraries assigned to the app, respectively.
     *
     * WARNING: If the app's IAppConfig implementation's validateAccess() method returns false, the app will not
     * be started up, the paths to the css files, and javascript files, belonging to the themes and javascript
     * libraries assigned to the app will not be set, and this method will return false.
     *
     * WARNING: If the app's APPNAME.php file does not exist, this method will log an error and return false.
     * e.g., if an attempt is made to startup an app named helloWorld, and helloWorld does not provide a
     * helloWorld.php file, this method will log an error and return false.
     *
     * WARNING: If the app's APPNAME.php file does not exist, the paths to the css files, and javascript files,
     * belonging to the themes and javascript libraries assigned to the app will not be set.
     *
     * @return bool True if startup was successful, false otherwise.
     * @see IAppConfig::validateAccess()
     * @see IAppConfig::getName()
     * @see AppStartup::setCssPaths()
     * @see AppStartup::setJsPaths()
     */
    public function startup(): bool
    {
        if ($this->appConfig->validateAccess() === true) {
            $appFilePath = $this->paths['appsDir'] . $this->appConfig->getName() . '/' . $this->appConfig->getName() . '.php';
            ob_start();
            if (file_exists($appFilePath) === false) {
                error_log('Darling Cms Startup Error: Failed to start app ' . $this->appConfig->getName() . '. The ' . str_replace('core/classes/startup', 'apps/' . $this->appConfig->getName() . '/', __DIR__) . $this->appConfig->getName() . '.php file does not exist.');
                return false;
            }
            include $appFilePath;
            $this->appOutput = ob_get_clean();
            $this->setCssPaths();
            $this->setJsPaths();
            return true;
        }
        return false;
    }

    /**
     * Assigns the paths to the css files belonging to the themes assigned to the app to the array assigned to
     * the 'cssPaths' index in the $paths property's array.
     * @see IAppConfig::getThemeNames()
     */
    private function setCssPaths(): void
    {
        $cssPaths = array();
        foreach ($this->appConfig->getThemeNames() as $themeName) {
            foreach ($this->getDirectoryListing($this->paths['rootDir'] . $this->paths['themesDir'] . $themeName, 'css') as $stylesheet) {
                array_push($cssPaths, $this->paths['rootUrl'] . $this->paths['themesDir'] . $themeName . '/' . $stylesheet);
            }
        }
        $this->paths['cssPaths'] = $cssPaths;
    }

    /**
     * Assigns the paths to the javascript files belonging to the javascript libraries assigned to the app to
     * the array assigned to the 'jsPaths' index in the $paths property's array.
     * @see IAppConfig::getJsLibraryNames()
     */
    private function setJsPaths(): void
    {
        $jsPaths = array();
        foreach ($this->appConfig->getJsLibraryNames() as $jsLibraryName) {
            foreach ($this->getDirectoryListing($this->paths['rootDir'] . $this->paths['jsDir'] . $jsLibraryName, 'js') as $script) {
                array_push($jsPaths, $this->paths['rootUrl'] . $this->paths['jsDir'] . $jsLibraryName . '/' . $script);
            }
        }
        $this->paths['jsPaths'] = $jsPaths;
    }

    /**
     * Returns an array of file names of files of a specified type from a specified directory.
     * @param string $path The path to the directory.
     * @param string $type The file extension of the type of files to include in the array.
     * @return array Array of file names of files of a specified type from a specified directory.
     */
    private function getDirectoryListing(string $path, string $type): array
    {
        $directoryListing = array();
        $directoryIterator = new \DirectoryIterator($path);
        foreach ($directoryIterator as $directory) {
            if ($directory->isFile() === true && $directory->isDot() === false && $directory->getExtension() === $type) {
                array_push($directoryListing, $directory->getFilename());
            }
        }
        return $directoryListing;
    }

    /**
     * Handles shutdown logic. Specifically, resets the $appOutput property to an empty string, resets the array
     * assigned to the 'cssPaths' index in the $paths property's array back to an empty array, and resets the array
     * assigned to the 'jsPaths' index in the $paths property's array back to an empty array.
     * @return bool True if shutdown was successful, false otherwise.
     */
    public function shutdown(): bool
    {
        $this->appOutput = '';
        $this->paths['cssPaths'] = array();
        $this->paths['jsPaths'] = array();
        return (empty($this->appOutput) && empty($this->paths['cssPaths']) && empty($this->paths['jsPaths']));
    }

    /**
     * Handles restart logic. Calls the shutdown() method, and then the startup() method.
     * @return bool True if restart was successful, i.e. shutdown and startup were both successful, false otherwise.
     * @see AppStartup::shutdown()
     * @see AppStartup::startup()
     */
    public function restart(): bool
    {
        return ($this->shutdown() === true && $this->startup() === true);
    }

}
