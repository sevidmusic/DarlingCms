<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 4/27/18
 * Time: 11:21 AM
 */

namespace DarlingCms\interfaces\accessControl;

/**
 * Interface IAccessController. Defines the basic contract of an object that validates access.
 * @package DarlingCms\interfaces\accessControl
 * @see IAccessController::validateAccess()
 */
interface IAccessController
{
    /**
     * Validates access.
     * @return bool True if access is valid, false otherwise.
     */
    public function validateAccess(): bool;
}
