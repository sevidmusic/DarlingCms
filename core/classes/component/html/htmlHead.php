<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 1/15/18
 * Time: 9:35 AM
 */

namespace DarlingCms\classes\component\html;

/**
 * Class htmlHead. Generates the html header, and provides methods to enable or disable
 * themes, scripts, and meta tags that are incorporated into the generated html header.
 * i.e., This class can be used to add or remove stylesheets, javascript libraries,
 * and meta data to or from the html header.
 * @package DarlingCms\classes\component\html
 */
class htmlHead extends htmlContainer
{

    /**
     * @var string Name of the directory where all Darling Cms themes reside.
     */
    const THEME_DIR_NAME = 'themes';
    /**
     * @var string The name of the $_GET variable used to determine the page title.
     */
    const TITLE_VAR_NAME = 'page';
    /**
     * @var string Path to Darling Cms theme directory.
     */
    private $themeDir;
    /**
     * @var string Url to Darling Cms theme directory.
     */
    private $themeDirUrl;
    /**
     * @var array Array of html objects responsible for generating the <link> tags for each
     *            stylesheet that is to be loaded into the page.
     */
    private $themeLinks = array();
    /**
     * @var \DarlingCms\classes\crud\registeredJsonCrud Instance of a registeredJsonCrud that is used to update the
     *                                                  stored version of this object.
     */
    private $crud;

    /**
     * htmlHead constructor.
     */
    public function __construct(\DarlingCms\classes\crud\registeredJsonCrud $crud)
    {
        $this->crud = $crud;
        /* Determine and set the theme directory. */
        $this->themeDir = str_replace('core/classes/component/html', '', __DIR__) . self::THEME_DIR_NAME . '/';
        /* Determine and set the url to the theme directory | Exclude index.php and anything that follows from resulting string. */
        $this->themeDirUrl = $_SERVER['HTTP_HOST'] . explode('index.php', $_SERVER['REQUEST_URI'])[0] . self::THEME_DIR_NAME . '/';
        /* Configure this html container. Set tag type to 'head' since this object generates an html header.
           Set attributes array to an empty array since the <head> tag should not have any attributes. */
        parent::__construct('head', array());
        /* Check if a stored htmlHead object exists, if so, add any theme links it specifies, if not create it. */
        switch ($this->crud->read('htmlHead', 'DarlingCms\classes\component\html\htmlHead')) {
            /* Stored version does not exist, create it. */
            case false:
                $this->crud->create('htmlHead', $this);
                break;
            /* Stored version exists, add the stored versions theme links to the $themeLinks property's array. */
            default:
                foreach ($this->crud->read('htmlHead', 'DarlingCms\classes\component\html\htmlHead')->getThemeLinks() as $index => $themeLink) {
                    $this->themeLinks[$index] = $themeLink;
                }
                break;
        }
    }

    /**
     * Delete the stored version in order to reset the htmlHead object's properties.
     * WARNING: THIS WILL CAUSE ALL PROPERTIES TO BE RESET, WHICH MEANS ANY THEMES OR STYLESHEETS
     * THAT WERE ENABLED WILL NO LONGER BE ENABLED!
     * @return bool Deletes the stored version of this object in order to force the constructor to create a fresh instance.
     */
    public function resetHtmlHead()
    {
        return $this->crud->delete('htmlHead');
    }

    /**
     * Enable a Darling Cms theme. This method will add <link> tags for the specified theme's stylesheets
     * to the html <head> so they can be imported. If the optional $stylesheet parameter is specified,
     * only the specified stylesheet will be added.
     * WARNING: CALLING THIS METHOD EVEN ONCE WILL ENABLE THE SPECIFIED THEME OR STYLESHEET UNTIL IT THEY
     * ARE EXPLICITLY DISABLED BY THE disableTheme() METHOD, OR UNTIL A CALL TO THE resetHtmlHead() METHOD
     * IS MADE.
     * @param string $themeName Name of the theme to enable. i.e., theme to import stylesheets from.
     * @param string $stylesheet (optional) If specified, only this stylesheet will be enabled.
     * @return bool True if specified theme's stylesheets, or specified stylesheet, was enabled, false otherwise.
     */
    public function enableTheme(string $themeName, string $stylesheet = ''): bool
    {
        /* Initial number of links in the $themeLinks property's array. Used to determine if any stylesheets were
           successfully added to the $themeLinks property's array. */
        $initialCount = count($this->themeLinks);
        /* Determine theme's path. */
        $path = $this->themeDir . $themeName;
        /* Determine whether to load all the themes stylesheets, or just the specified stylesheet based
           on whether or not a stylesheet was specified via the $stylesheet parameter. */
        switch ($stylesheet === '') {
            /* No stylesheet was specified, load all the themes stylesheets. */
            case true:
                /* Locate specified theme's stylesheets. */
                $stylesheets = $this->locateStylesheets($themeName, $path);
                /* Create theme link for each stylesheet that was found and add it to the $themeLinks
                   property's array under the appropriate index. */
                foreach ($stylesheets as $index => $stylesheetRelativePath) {
                    $link = $this->createThemeLink($themeName, $stylesheetRelativePath);
                    $this->themeLinks[$index] = $link;
                }
                break;
            /* A stylesheet was specified, only load the specified stylesheet. */
            case false:
                $stylesheetRelativePath = $this->locateStylesheet($themeName, $path, $stylesheet);
                if ($stylesheetRelativePath !== false) {
                    $link = $this->createThemeLink($themeName, $stylesheetRelativePath);
                    $this->themeLinks[$themeName . '_' . $stylesheet] = $link;
                }
                break;
        }
        /* Post number of links in the $themeLinks property's array. Used to determine if any stylesheets were
           successfully added to the $themeLinks property's array. */
        $postCount = count($this->themeLinks);
        /* Update the stored version of this object. */
        $this->crud->update('htmlHead', $this);
        /* Return true if at least one theme link was added to the $themeLinks property's array, false otherwise. */
        return $postCount > $initialCount;
    }

    /**
     * Appends any html objects in the $themeLinks property's array to the html head, and returns the generated html.
     * @return string The generated html.
     */
    public function getHtml(): string
    {
        /* Determine the title of the page base on the $_GET variable indexed under TITLE_VAR_NAME, if not set use "Homepage" as the title. */
        $title = (filter_input(INPUT_GET, self::TITLE_VAR_NAME) === null ? 'Homepage' : filter_input(INPUT_GET, self::TITLE_VAR_NAME));
        /* Title will most likely be a camelCase string, break it into words via preg_match_all(). */
        preg_match_all('/((?:^|[A-Z])[a-z]+)/', $title, $matches);
        /* Create filtered title from preg_match_all() results making the first char of each word uppercase. */
        $filteredTitle = ucwords(implode(' ', $matches[0]));
        /* Add title to html head. */
        $this->appendHtml(new html('title', $filteredTitle));
        /* Add theme links to html head. */
        foreach ($this->themeLinks as $themeLink) {
            $this->appendHtml($themeLink);
        }
        return parent::getHtml();
    }

    /**
     * Locates stylesheets, i.e., css files, in a specified $path.
     * @param string $themeName The name of the theme whose stylesheets are to be located.
     * @param string $path The path to search in.
     * @param array $results (optional) Array of stylesheets already found. Typically, this parameter
     *                                  is used internally to recursively track any stylesheets that
     *                                  are discovered in sub-directories found in the specified $path.
     * @return array Array of any stylesheets that were found. Specifically, this method returns an array
     *               of paths relative to the specified theme of any stylesheets that were found indexed
     *               according to the following format: THEMENAME_STYLESHEET.css
     *               e.g., return array('SomeTheme_SomeStylesheet.css' => '/Relative/Path/SomeStylesheet.css')
     *               Note: This method will return false if the specified theme does not exist.
     */
    private function locateStylesheets(string $themeName, string $path, &$results = array())
    {
        /* Construct and assign recursive directory iterator for the directory specified in the $path, or assign
           false if file at the specified path is not a directory or does not exist. */
        $recursiveDirectoryIterator = (is_dir($path) ? new \RecursiveDirectoryIterator($path) : false);
        /* If a RecursiveDirectoryIterator() was instantiated for the specified $path, proceed. */
        if ($recursiveDirectoryIterator !== false) {
            /* Check each item in the directory to determine if it is a stylesheet, a directory, or something to ignore. */
            foreach ($recursiveDirectoryIterator as $SplFileInfoObject) {
                /* If item is in fact a file, and it's extension is "css", add it to the results array. */
                if ($SplFileInfoObject->isFile() && $SplFileInfoObject->getExtension() === 'css') {
                    /* Create the appropriate index to assign to the stylesheet in the $results array.
                       Format: THEMENAME_FILENAME.FILEEXTENSION */
                    $index = $themeName . '_' . $SplFileInfoObject->getFilename();
                    /* As long as the stylesheet is not already assigned to the results array, assign it. */
                    if (isset($results[$index]) === false) {
                        /* Determine the stylesheet's relative path, i.e. path relative to specified theme.
                         * (use explode() passing $themeName as delimiter to determine relative path, this
                         * path will be used by the createLink() method to create the link tag for this
                         * stylesheet.)
                         */
                        $relativePath = explode($themeName, $SplFileInfoObject->getRealPath())[1];
                        /* Assign the discovered stylesheet's relative path to the results array under
                           the appropriate index. */
                        $results[$index] = $relativePath;
                    }
                }
                /* If file is a directory, and is not the . or .. directory, locate any css files it holds and add
                   them to the $results array. */
                if ($SplFileInfoObject->isDir() && $SplFileInfoObject->getFileName() !== '.' && $SplFileInfoObject->getFileName() !== '..') {
                    /* Determine the sub-directories path. */
                    $subPath = $SplFileInfoObject->getPath() . '/' . $SplFileInfoObject->getFileName();
                    /* Locate the stylesheets in the sub-directory. */
                    $this->locateStylesheets($themeName, $subPath, $results);
                }
            }
        }
        /* Return the results. */
        return $results;
    }

    /**
     * @param string $themeName The name of the theme the stylesheet belongs to.
     * @param string $stylesheetRelativePath The stylesheet to load's relative path under the theme
     *                                       including the stylesheet's name and extension.
     *                                       i.e., '/Relative/Path/SomeStylesheet.css'
     * @return html The html object responsible for generating the link tag for the stylesheet.
     */
    private function createThemeLink(string $themeName, string $stylesheetRelativePath): html
    {
        return new html('link', '', array('href="http://' . $this->themeDirUrl . $themeName . $stylesheetRelativePath . '"', 'rel="stylesheet"'));
    }

    /**
     * Locate a specific stylesheet in a theme and return it's relative path under the theme's directory.
     * This method will return false if the specified stylesheet or theme does not exist.
     * @param string $themeName The name of the theme the stylesheet belongs to.
     * @param string $path The path to search in. (i.e., the theme's path)
     * @param string $stylesheet The name of the stylesheet to locate.
     * @return string|bool The relative path, under the theme, of the stylesheet including the
     *                     stylesheet's name and extension, or false on failure.
     *                     i.e., return '/Relative/Path/SomeStylesheet.css'
     */
    private function locateStylesheet(string $themeName, string $path, string $stylesheet)
    {
        $stylesheets = $this->locateStylesheets($themeName, $path);
        return (isset($stylesheets[$themeName . '_' . $stylesheet]) === true ? $stylesheets[$themeName . '_' . $stylesheet] : false);
    }


    /**
     * Returns the $themeLinks property's array, which contains any html objects responsible for generating the link
     * tags associated with any stylesheets that are enabled.
     * @return array Array of theme links associated with this htmlHead object.
     */
    public function getThemeLinks(): array
    {
        return $this->themeLinks;
    }

    public function disableTheme(string $themeName, string $stylesheet = ''): bool
    {
        $status = array();
        /* Determine if a stylesheet was specified. */
        switch ($stylesheet !== '') {
            /* Stylesheet was specified, only disable specified stylesheet. */
            case true:
                unset($this->themeLinks[$themeName . '_' . $stylesheet]);
                $status[] = !isset($this->themeLinks[$themeName . '_' . $stylesheet]);
                break;
            default:
                foreach (array_keys($this->getThemeLinks()) as $themeLink) {
                    /* Determine if theme link is associated with specified theme.
                     * (if it is explode()'s first item will be an empty string.)
                     */
                    if (explode($themeName, $themeLink)[0] === '') {
                        /* Theme link is associated with theme specified to be disabled, unset the link.  */
                        unset($this->themeLinks[$themeLink]);
                        /* Log status in $status array. */
                        $status[] = !isset($this->themeLinks[$themeLink]);
                    }
                }
                break;
        }
        /* Update stored htmlHead object. */
        $this->crud->update('htmlHead', $this);
        return !in_array(false, $status, true);
    }


    /**  @todo: Implement the following methods...
     * public function addMetaData()
     * {
     *
     * }
     *
     * public function removeMetaData()
     * {
     *
     * }
     *
     * public function enableJsLibrary()
     * {
     *
     * }
     *
     * public function disableJsLibrary()
     * {
     *
     * }
     */
}
