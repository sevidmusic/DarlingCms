<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 10/6/18
 * Time: 2:17 AM
 */

namespace DarlingCms\classes\startup;


use DarlingCms\classes\staticClasses\core\CoreValues;
use DarlingCms\interfaces\accessControl\IAppConfig;
use DarlingCms\interfaces\startup\IAppStartup;

/**
 * Class AppStartup
 * @package DarlingCms\classes\startup
 * @see AppStartup::ROOT_DIR_INDEX
 * @see AppStartup::ROOT_URL_INDEX
 * @see AppStartup::APPS_DIR_INDEX
 * @see AppStartup::THEMES_DIR_INDEX
 * @see AppStartup::JS_DIR_INDEX
 * @see AppStartup::CSS_PATHS_INDEX
 * @see AppStartup::JS_PATHS_INDEX
 * @see AppStartup::getCssPaths()
 * @see AppStartup::getJsPaths()
 * @see AppStartup::getAppOutput()
 * @see AppStartup::getPaths()
 * @see AppStartup::startup()
 * @see AppStartup::includeOutput()
 * @see AppStartup::setCssPaths()
 * @see AppStartup::setJsPaths()
 * @see AppStartup::getDirectoryListing()
 * @see AppStartup::shutdown()
 * @see AppStartup::restart()
 */
class AppStartup implements IAppStartup
{
    /**
     * @var string Value of the index assigned to the root directory's absolute path in the $paths property's array.
     */
    const ROOT_DIR_INDEX = 'rootDir';

    /**
     * @var string Value of the index assigned to the root url in the $paths property's array.
     */
    const ROOT_URL_INDEX = 'rootUrl';

    /**
     * @var string Value of the index assigned to the apps directory's absolute path in the $paths property's array.
     */
    const APPS_DIR_INDEX = 'appsDir';

    /**
     * @var string Value of the index assigned to the themes directory's relative path in the $paths property's array.
     */
    const THEMES_DIR_INDEX = 'themesDir';

    /**
     * @var string Value of the index assigned to the js directory's relative path in the $paths property's array.
     */
    const JS_DIR_INDEX = 'jsDir';

    /**
     * @var string Value of the index assigned to the array of css paths in the $paths property's array.
     */
    const CSS_PATHS_INDEX = 'cssPaths';

    /**
     * @var string Value of the index assigned to the array of js paths in the $paths property's array.
     */
    const JS_PATHS_INDEX = 'jsPaths';

    /**
     * @var array Array of paths set by the __construct() method, and, if startup was successful, the startup() method.
     */
    private $paths = array();

    /**
     * @var IAppConfig Local instance of an object that implements the IAppConfig interface. This property's value
     * is set by the __construct() method upon instantiation. This will most likely be the App's implementation of
     * the IAppConfig interface.
     */
    private $appConfig;

    /**
     * @var string The app's output, or an empty string if there is no output, or app failed to startup.
     */
    private $appOutput = '';

    /**
     * AppStartup constructor. Injects an instance of an object that implements the IAppConfig interface, determines
     * the path to the Darling Cms root directory, and initializes the $paths property's array.
     * @param IAppConfig $appConfig An instance of an object that implements the IAppConfig interface. This will most
     *                              likely be the App's implementation of the IAppConfig interface.
     * @see AppStartup::ROOT_DIR_INDEX
     * @see AppStartup::ROOT_URL_INDEX
     * @see AppStartup::APPS_DIR_INDEX
     * @see AppStartup::THEMES_DIR_INDEX
     * @see AppStartup::JS_DIR_INDEX
     * @see AppStartup::CSS_PATHS_INDEX
     * @see AppStartup::JS_PATHS_INDEX
     */
    public function __construct(IAppConfig $appConfig)
    {
        $this->appConfig = $appConfig;
        $rootDir = str_replace('core/classes/startup', '', __DIR__);
        $this->paths = array(
            self::ROOT_DIR_INDEX => $rootDir,
            self::ROOT_URL_INDEX => CoreValues::getSiteRootUrl(),//(!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/DarlingCms/',
            self::APPS_DIR_INDEX => $rootDir . 'apps/',
            self::THEMES_DIR_INDEX => 'themes/',
            self::JS_DIR_INDEX => 'js/',
            self::CSS_PATHS_INDEX => array(),
            self::JS_PATHS_INDEX => array(),
        );
    }

    /**
     * Returns an array of paths to the css files belonging to the themes assigned to the app, or an empty array
     * if the app is not assigned any themes, or startup failed.
     * @return array Array of paths to the css files belonging to the themes assigned to the app, or an empty array
     *               if the app is not assigned any themes, or startup failed.
     * @see AppStartup::CSS_PATHS_INDEX
     */
    public function getCssPaths(): array
    {
        return $this->paths[self::CSS_PATHS_INDEX];
    }

    /**
     * Returns an array of paths to the javascript files belonging to the javascript libraries assigned to the app,
     * or an empty array if the app is not assigned any javascript libraries, or startup failed.
     * @return array Array of paths to the javascript files belonging to the javascript libraries assigned to the app,
     *               or an empty array if the app is not assigned any javascript libraries, or startup failed.
     * @see AppStartup::JS_PATHS_INDEX
     */
    public function getJsPaths(): array
    {
        return $this->paths[self::JS_PATHS_INDEX];
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
     * Returns an array of the following paths: The absolute path to the Darling Cms root directory, the root url,
     * the absolute path to the apps directory, the relative path to the themes directory, and the relative path to
     * the js directory. Additionally, if startup was successful, this array is assigned an array of css file paths,
     * and an array of javascript file paths, belonging to the themes and javascript libraries assigned to the app,
     * respectively.
     *
     * Note: If startup is not successful, the arrays assigned to the CSS_PATHS_INDEX and the JS_PATHS_INDEX will
     * be empty.
     *
     * The path values are indexed by the following constants:
     *
     * AppStartup::ROOT_DIR_INDEX : The absolute path to the Darling Cms root directory.
     *
     * AppStartup::ROOT_URL_INDEX : The site's root url.
     *
     * AppStartup::APPS_DIR_INDEX : The absolute path to the Darling Cms apps directory.
     *
     * AppStartup::THEMES_DIR_INDEX : The relative path to the Darling Cms themes directory, i.e., '/themes'.
     *
     * AppStartup::JS_DIR_INDEX : The relative path to the Darling Cms js directory, i.e., '/js'.
     *
     * AppStartup::CSS_PATHS_INDEX : Array of paths to the css files belonging to the themes assigned to the app,
     *                               or an empty array if the app is not assigned any themes, or startup failed.
     *
     * AppStartup::JS_PATHS_INDEX : Array of paths to the javascript files belonging to the javascript libraries
     *                              assigned to the app, or an empty array if the app is not assigned any
     *                              javascript libraries, or startup failed.
     *
     *
     * @return array The array of paths assigned to the $paths property's array.
     * @see AppStartup::ROOT_DIR_INDEX
     * @see AppStartup::ROOT_URL_INDEX
     * @see AppStartup::APPS_DIR_INDEX
     * @see AppStartup::THEMES_DIR_INDEX
     * @see AppStartup::JS_DIR_INDEX
     * @see AppStartup::CSS_PATHS_INDEX
     * @see AppStartup::JS_PATHS_INDEX
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * Handles startup logic. Specifically, compiles the app's output, and sets the paths to the css files, and
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
     * @see AppStartup::setCssPaths()
     * @see AppStartup::setJsPaths()
     */
    public function startup(): bool
    {
        if ($this->appConfig->validateAccess() === true && $this->includeOutput() === true) {
            $this->setCssPaths();
            $this->setJsPaths();
            return true;
        }
        return false;
    }

    /**
     * Includes the app's APP_NAME.php file to get the app's output. If include is successful, the output
     * is assigned to the $appOutput property.
     * @return bool True if app output was included successfully, false otherwise.
     * @see AppStartup::APPS_DIR_INDEX
     * @see IAppConfig::getName()
     */
    private function includeOutput(): bool
    {
        $appFilePath = $this->paths[self::APPS_DIR_INDEX] . $this->appConfig->getName() . '/' . $this->appConfig->getName() . '.php';
        ob_start();
        if (file_exists($appFilePath) === false) {
            error_log('Darling Cms Startup Error: Failed to start app ' . $this->appConfig->getName() . '. The ' . str_replace('core/classes/startup', 'apps/' . $this->appConfig->getName() . '/', __DIR__) . $this->appConfig->getName() . '.php file does not exist.');
            return false;
        }
        include_once $appFilePath;
        $this->appOutput = ob_get_clean();
        return true;
    }

    /**
     * Assigns the paths to the css files belonging to the themes assigned to the app to the array assigned to
     * the CSS_PATHS_INDEX in the $paths property's array.
     * @see IAppConfig::getThemeNames()
     * @see AppStartup::getDirectoryListing()
     * @see AppStartup::ROOT_DIR_INDEX
     * @see AppStartup::THEMES_DIR_INDEX
     * @see AppStartup::ROOT_URL_INDEX
     * @see AppStartup::CSS_PATHS_INDEX
     */
    private function setCssPaths(): void
    {
        $cssPaths = array();
        foreach ($this->appConfig->getThemeNames() as $themeName) {
            foreach ($this->getDirectoryListing($this->paths[self::ROOT_DIR_INDEX] . $this->paths[self::THEMES_DIR_INDEX] . $themeName, 'css') as $stylesheet) {
                array_push($cssPaths, $this->paths[self::ROOT_URL_INDEX] . $this->paths[self::THEMES_DIR_INDEX] . $themeName . '/' . $stylesheet);
            }
        }
        $this->paths[self::CSS_PATHS_INDEX] = $cssPaths;
    }

    /**
     * Assigns the paths to the javascript files belonging to the javascript libraries assigned to the app to
     * the array assigned to the JS_PATHS_INDEX in the $paths property's array.
     * @see IAppConfig::getJsLibraryNames()
     * @see AppStartup::getDirectoryListing()
     * @see AppStartup::ROOT_DIR_INDEX
     * @see AppStartup::JS_DIR_INDEX
     * @see AppStartup::ROOT_URL_INDEX
     * @see AppStartup::JS_PATHS_INDEX
     */
    private function setJsPaths(): void
    {
        $jsPaths = array();
        foreach ($this->appConfig->getJsLibraryNames() as $jsLibraryName) {
            foreach ($this->getDirectoryListing($this->paths[self::ROOT_DIR_INDEX] . $this->paths[self::JS_DIR_INDEX] . $jsLibraryName, 'js') as $script) {
                array_push($jsPaths, $this->paths[self::ROOT_URL_INDEX] . $this->paths[self::JS_DIR_INDEX] . $jsLibraryName . '/' . $script);
            }
        }
        $this->paths[self::JS_PATHS_INDEX] = $jsPaths;
    }

    /**
     * Returns an array of file names of files of a specified type from a specified directory.
     * @param string $path The path to the directory.
     * @param string $type The file extension of the type of files to include in the array.
     * @return array Array of file names of files of a specified type from a specified directory.
     * @see \DirectoryIterator
     * @see \DirectoryIterator::isDot()
     * @see \DirectoryIterator::getExtension()
     * @see \DirectoryIterator::getFilename()
     */
    private function getDirectoryListing(string $path, string $type): array
    {
        $directoryListing = array();
        if (is_dir($path) === true) {
            $directoryIterator = new \DirectoryIterator($path);
            foreach ($directoryIterator as $directory) {
                if ($directory->isFile() === true && $directory->isDot() === false && $directory->getExtension() === $type) {
                    array_push($directoryListing, $directory->getFilename());
                }
            }
        }
        return $directoryListing;
    }

    /**
     * Handles shutdown logic. Specifically, resets the $appOutput property to an empty string, resets the array
     * assigned to the AppStartup::CSS_PATHS_INDEX index in the $paths property's array back to an empty array,
     * and resets the array assigned to the AppStartup::JS_PATHS_INDEX index in the $paths property's array back
     * to an empty array.
     * @return bool True if shutdown was successful, false otherwise.
     * @see AppStartup::CSS_PATHS_INDEX
     * @see AppStartup::JS_PATHS_INDEX
     */
    public function shutdown(): bool
    {
        $this->appOutput = '';
        $this->paths[self::CSS_PATHS_INDEX] = array();
        $this->paths[self::JS_PATHS_INDEX] = array();
        return (empty($this->appOutput) && empty($this->paths[self::CSS_PATHS_INDEX]) && empty($this->paths[self::JS_PATHS_INDEX]));
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
