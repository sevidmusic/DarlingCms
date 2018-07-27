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
 * a single Darling Cms app. Note: This implementation will cache the app's output on startup. Consequently, if
 * an attempt is made by any instance of this class to startup an app that was already started up, either by the
 * same AppStartup instance or a different AppStartup instance, the app's cached output will be used, so long as
 * the cached output is still valid. The cached app output MUST not be older than the value of the CACHE_LIFETIME
 * constant in seconds for the cached app output to be considered valid.
 *
 * @package DarlingCms\classes\startup
 * @see AppStartup::CACHE_LIFETIME
 * @see AppStartup::ROOT_DIR_INDEX
 * @see AppStartup::ROOT_URL_INDEX
 * @see AppStartup::APPS_DIR_INDEX
 * @see AppStartup::THEMES_DIR_INDEX
 * @see AppStartup::JS_DIR_INDEX
 * @see AppStartup::CSS_PATHS_INDEX
 * @see AppStartup::JS_PATHS_INDEX
 * @see AppStartup::CACHE_PATH_INDEX
 * @see AppStartup::getCssPaths()
 * @see AppStartup::getJsPaths()
 * @see AppStartup::getAppOutput()
 * @see AppStartup::getPaths()
 * @see AppStartup::startup()
 * @see AppStartup::compileAppOutput()
 * @see AppStartup::includeOutput()
 * @see AppStartup::includeCachedOutput()
 * @see AppStartup::cacheAppOutput()
 * @see AppStartup::cleanCache()
 * @see AppStartup::loadCache()
 * @see AppStartup::setCssPaths()
 * @see AppStartup::setJsPaths()
 * @see AppStartup::getDirectoryListing()
 * @see AppStartup::shutdown()
 * @see AppStartup::restart()
 */
class AppStartup implements IAppStartup
{
    /**
     * @var int The number of seconds cached app output is considered valid.
     */
    const CACHE_LIFETIME = 5;

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
     * @var string Value of the index assigned to the cache file's absolute path in the $paths property's array.
     */
    const CACHE_PATH_INDEX = 'cachePath';

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
     * the path to the Darling Cms root directory, initializes the $paths property's array, and calls the cleanCache()
     * method to insure the cache is clean.
     * @param IAppConfig $appConfig An instance of an object that implements the IAppConfig interface. This will most
     *                              likely be the App's implementation of the IAppConfig interface.
     * @see AppStartup::ROOT_DIR_INDEX
     * @see AppStartup::ROOT_URL_INDEX
     * @see AppStartup::APPS_DIR_INDEX
     * @see AppStartup::THEMES_DIR_INDEX
     * @see AppStartup::JS_DIR_INDEX
     * @see AppStartup::CSS_PATHS_INDEX
     * @see AppStartup::JS_PATHS_INDEX
     * @see AppStartup::CACHE_PATH_INDEX
     * @see AppStartup::cleanCache()
     */
    public function __construct(IAppConfig $appConfig)
    {
        $this->appConfig = $appConfig;
        $rootDir = str_replace('core/classes/startup', '', __DIR__);
        $this->paths = array(
            self::ROOT_DIR_INDEX => $rootDir,
            self::ROOT_URL_INDEX => (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/DarlingCms/',
            self::APPS_DIR_INDEX => $rootDir . 'apps/',
            self::THEMES_DIR_INDEX => 'themes/',
            self::JS_DIR_INDEX => 'js/',
            self::CSS_PATHS_INDEX => array(),
            self::JS_PATHS_INDEX => array(),
            self::CACHE_PATH_INDEX => $rootDir . 'AppOutput.json',
        );
        $this->cleanCache();
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
     * the absolute path to the apps directory, the relative path to the themes directory, the relative path to
     * the js directory, and the absolute path to the file used to cache app output. Additionally, if startup was
     * successful, this array is assigned an array of css file paths, and an array of javascript file paths,
     * belonging to the themes and javascript libraries assigned to the app, respectively.
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
     * AppStartup::CACHE_PATH_INDEX : The absolute path to the file used to cache app output.
     *
     * @return array The array of paths assigned to the $paths property's array.
     * @see AppStartup::ROOT_DIR_INDEX
     * @see AppStartup::ROOT_URL_INDEX
     * @see AppStartup::APPS_DIR_INDEX
     * @see AppStartup::THEMES_DIR_INDEX
     * @see AppStartup::JS_DIR_INDEX
     * @see AppStartup::CSS_PATHS_INDEX
     * @see AppStartup::JS_PATHS_INDEX
     * @see AppStartup::CACHE_PATH_INDEX
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
     * @see AppStartup::compileAppOutput()
     * @see AppStartup::setCssPaths()
     * @see AppStartup::setJsPaths()
     */
    public function startup(): bool
    {
        if ($this->appConfig->validateAccess() === true && $this->compileAppOutput() === true) {
            $this->setCssPaths();
            $this->setJsPaths();
            return true;
        }
        return false;
    }

    /**
     * Compiles the app's output. If the output is already cached, the cached output will be used, otherwise
     * the output will be compiled by including the app. This method will return true if the app output was
     * compiled successfully either from the cache or by including the app, false otherwise.
     * @return bool True if app's output was compiled successfully, false otherwise.
     * @see AppStartup::includeCachedOutput()
     * @see AppStartup::includeOutput()
     */
    private function compileAppOutput(): bool
    {
        if ($this->includeCachedOutput() === true) {
            return true;
        }
        return $this->includeOutput();
    }

    /**
     * Includes the app's APP_NAME.php file to get the app's output. If include is successful, the output
     * is assigned to the $appOutput property and cached.
     * @return bool True if app output was included and cached successfully, false otherwise.
     * @see AppStartup::APPS_DIR_INDEX
     * @see IAppConfig::getName()
     * @see AppStartup::cacheAppOutput()
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
        return $this->cacheAppOutput();
    }

    /**
     * Assigns the cached app output to the $appOutput property.
     * @return bool True if cached output was assigned to the $appOutput property, false otherwise.
     * @see AppStartup::loadCache()
     * @see IAppConfig::getName()
     */
    private function includeCachedOutput(): bool
    {
        $cache = $this->loadCache();
        if (isset($cache[time()][$this->appConfig->getName()]) === true) {
            $this->appOutput = strval($cache[time()][$this->appConfig->getName()]);
            return true;
        }
        return false;
    }

    /**
     * Caches the app output assigned to the $appOutput property.
     * @return bool True if app output was cached, false otherwise.
     * @see AppStartup::loadCache()
     * @see IAppConfig::getName()
     * @see AppStartup::CACHE_PATH_INDEX
     */
    private function cacheAppOutput(): bool
    {
        $cache = $this->loadCache();
        $cache[time()][$this->appConfig->getName()] = $this->appOutput;
        file_put_contents($this->paths[self::CACHE_PATH_INDEX], json_encode($cache));
        return true;
    }


    /**
     * Removes outdated app output from the cache. This method will return true if cache was cleaned and updated,
     * false otherwise.
     *
     * Note: Any cached app output that is older than the value of the AppStartup::CACHE_PATH_INDEX constant
     * in seconds will be removed.
     *
     * WARNING: If there is no cached app output to clean, this method will return false.
     *
     * WARNING: If the cache fails to update itself, this method will return false.
     *
     * @return bool True if cache was cleaned and updated, false otherwise.
     * @see AppStartup::loadCache()
     * @see AppStartup::CACHE_LIFETIME
     * @see AppStartup::CACHE_PATH_INDEX
     */
    private function cleanCache(): bool
    {
        $cache = $this->loadCache();
        $cleaned = array();
        foreach (array_keys($cache) as $time) {
            if ($time <= (time() - self::CACHE_LIFETIME)) {
                unset($cache[$time]);
                array_push($cleaned, $time);
            }
        }
        $saved = file_put_contents($this->paths[self::CACHE_PATH_INDEX], json_encode($cache));
        return !empty($cleaned) && !empty($saved);
    }

    /**
     * Returns an array of cached data, or an empty array if there is no cached data.
     * @return array An array of cached data, or an empty array if there is no cached data.
     * @see AppStartup::CACHE_PATH_INDEX
     */
    private function loadCache(): array
    {
        if (file_exists($this->paths[self::CACHE_PATH_INDEX]) === true) {
            $cache = json_decode(file_get_contents($this->paths[self::CACHE_PATH_INDEX]), true);
            return is_array($cache) === true ? $cache : array();
        }
        return array();
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
