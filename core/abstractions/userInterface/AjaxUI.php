<?php
/**
 * Created by PhpStorm.
 * User: sevidmusic
 * Date: 2018-12-30
 * Time: 23:33
 */

namespace DarlingCms\abstractions\userInterface;


use DarlingCms\interfaces\userInterface\IUserInterface;

/**
 * Class AjaxUi. Defines an implementation of the IUserInterface interface that can be implemented
 * by apps that use ajax to generate their user interface's content from  defined "views".
 * @package DarlingCms\abstractions\userInterface
 */
abstract class AjaxUI implements IUserInterface
{
    /**
     * Name of the url parameter that specifies the view when user is directed via url. This allows the ajax requests to be saved, i.e. someurl.com/index.php?ajaxUiView=view1 should display view 1.
     */
    const VIEW_PARAMETER_NAME = 'ajaxUiView';
    /**
     * @var string Name of the app implementing this class. This MUST match the name of the app exactly.
     */
    protected $appName = '';
    /**
     * @var string Default view | i.e. default.php
     */
    protected $defaultView = 'default';
    /**
     * @var string The current view.
     */
    protected $currentView = '';
    /**
     * @var string The id of the element that views should be output to.
     */
    protected $viewContainerId = 'defaultViewContainer';

    /**
     * AjaxUi constructor. Sets the app's name, sets the app's views container id, and determines the
     * current view from selected view.
     * @param string $appName Name of the app implementing this class. This MUST match the name of the app exactly.
     * @param string $viewContainerId Id of the html element that contains each views content, i.e., id of the parent element of all views.
     */
    public function __construct(string $appName, string $viewContainerId)
    {
        $this->appName = $appName;
        $this->viewContainerId = $viewContainerId;
        $selectedView = $this->getSelectedView();
        $this->currentView = (!empty($selectedView) === true ? $selectedView : $this->defaultView);
    }

    /**
     * Determines the selected view. This method will first look
     * in $_GET for a parameter whose name matches the value
     * of the AjaxUI::VIEW_PARAMETER_NAME constant. If it cannot
     * find the value in $_GET, it will look in $_POST, and if it
     * cannot find the value in $_GET or $_POST it will return an
     * empty string.
     * @return string The selected view, or an empty string if the
     *                selected view could not be determined.
     */
    private function getSelectedView(): string
    {
        return (
        filter_input(INPUT_GET, self::VIEW_PARAMETER_NAME)
            ? filter_input(INPUT_GET, self::VIEW_PARAMETER_NAME)
            :
            (
            filter_input(INPUT_POST, self::VIEW_PARAMETER_NAME)
                ? filter_input(INPUT_POST, self::VIEW_PARAMETER_NAME)
                : '' /* Default to empty string if not set in get or post... */
            )
        );
    }

    final protected function getViewsDirPath(): string
    {
        return $this->getAppDirPath() . '/' . $this->getViewsDirName();
    }

    final protected function getAppDirPath(): string
    {
        return str_replace('core/abstractions/userInterface', 'apps/' . $this->appName, __DIR__);
    }

    final protected function getCurrentViewPath(): string
    {
        return $this->getViewsDirPath() . '/' . $this->currentView . '.php';
    }

    final protected function getViewNames(): array
    {
        $viewNames = array();
        foreach (scandir($this->getViewsDirPath()) as $view) {
            if ($view !== '.' && $view !== '..') {
                $viewName = str_replace('.php', '', $view);
                array_push($viewNames, $viewName);
            }
        }
        return $viewNames;
    }

    final protected function getCurrentViewHtml(): string
    {
        ob_start();
        include_once $this->getCurrentViewPath();
        $viewHtml = ob_get_clean();
        return (!empty($viewHtml) === true ? $viewHtml : '');
    }

    /**
     * Convert a "camelCase" string to "Normal Case".
     * @param string $string The camelCase string.
     * @return string The converted string.
     */
    final protected function convertFromCamelCase(string $string): string
    {
        // Both REGEX solutions found on stackoverflow. @see https://stackoverflow.com/questions/4519739/split-camelcase-word-into-words-with-php-preg-match-regular-expression
        $pattern = '/(?(?<=[a-z])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z]))/x'; // ridgerunner's answer | BETTER: This pattern can accommodate even malformed camel case like camelCASEString
        //$pattern = '/((?:^|[A-Z])[a-z]+)/'; // codaddict's answer | approved answer | WARNING: This pattern does not handle malformed camel case strings like camelCASEString, kept for reference.
        $words = preg_split($pattern, $string);
        return ucwords(implode(' ', $words));
    }

    /**
     * Generate an AjaxRouterRequest() function call using the provided parameters.
     * @param array $params Associative array of parameters to pas to the AjaxRouterRequest() call.
     *
     * The following parameters can be set using the following indexes:
     *
     * "issuingApp" => The name of the Darling Cms App issuing the request.
     *
     * "handlerName" => The name of the file that handles this request. (Note: Do not include extension)
     *
     * "outputElementId" => The id of the html element the request output should be written to.
     *
     * "requestType" => The type of request, either GET or POST
     *
     * "contentType" => application/x-www-form-urlencoded, multipart/form-data, or text/plain (html 5 only)
     *
     * "additionalParams" => Url parameter string, e.g., "someParam=someVal&someOtherParam=Val2"
     *
     * "ajaxDirName" => Name of the handler's parent directory, defaults to ajax
     *
     * "callFunction" => Name of the javascript function to call when request is issued
     *
     * "callContext" => The current call context, e.g. "window", or "document". defaults to window.
     *
     * "callArgs" => Array of function parameters to pass to the callFunction
     *
     * @return string The Ajax Request string.
     */
    final public static function generateAjaxRequest(array $params): string
    {
        $options = array(
            'issuingApp' => (!empty($params['issuingApp']) === true ? "'{$params['issuingApp']}'" : 'undefined'),
            'handlerName' => (!empty($params['handlerName']) === true ? "'{$params['handlerName']}'" : 'undefined'),
            'outputElementId' => (!empty($params['outputElementId']) === true ? "'{$params['outputElementId']}'" : 'undefined'),
            'requestType' => (!empty($params['requestType']) === true ? "'{$params['requestType']}'" : 'undefined'),
            'contentType' => (!empty($params['contentType']) === true ? "'{$params['contentType']}'" : 'undefined'),
            'additionalParams' => (!empty($params['additionalParams']) === true ? "'{$params['additionalParams']}'" : 'undefined'),
            //            'additionalParams' => (!empty($params['additionalParams']) === true ? (substr($params['additionalParams'], 0, 4) === 'this' ? $params['additionalParams'] : "'{$params['additionalParams']}'") : 'undefined'),
            'ajaxDirName' => (!empty($params['ajaxDirName']) === true ? "'{$params['ajaxDirName']}'" : 'undefined'),
            'callFunction' => (!empty($params['callFunction']) === true ? "'{$params['callFunction']}'" : 'undefined'),
            'callContext' => (!empty($params['callContext']) === true ? "'{$params['callContext']}'" : 'undefined'),
            'callArgs' => (!empty($params['callArgs']) === true ? "'{$params['callArgs']}'" : 'undefined')
        );
        return "AjaxRouterRequest({$options['issuingApp']}, {$options['handlerName']}, {$options['outputElementId']}, {$options['requestType']}, {$options['contentType']}, {$options['additionalParams']}, {$options['ajaxDirName']}, {$options['callFunction']}, {$options['callContext']}, {$options['callArgs']})";
    }

    final protected function getViewLinks(): array
    {
        $links = array();
        foreach ($this->getViewNames() as $viewName) {
            $ajaxRouterRequest = $this->generateAjaxRequest([
                'issuingApp' => $this->appName,
                'handlerName' => $viewName,
                'outputElementId' => $this->viewContainerId,
                'requestType' => 'GET',
                'contentType' => 'application/x-www-form-urlencoded',
                'additionalParams' => '',
                'ajaxDirName' => $this->getViewsDirName(),
                //'callFunction' => '', // @devNote if these are ever needed create params for them
                //'callContext' => '',
                //'callArgs' => ''
            ]);
            $links[$viewName] = '<a class="dcms-link ' . $viewName . '-view-link" onclick="return ' . $ajaxRouterRequest . '">' . $this->convertFromCamelCase($viewName) . '</a>';
        }
        return $links;
    }

    abstract protected function getViewsDirName(): string;

    /**
     * Gets the user interface.
     * @return string The user interface.
     */
    abstract public function getUserInterface(): string;
}
