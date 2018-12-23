<?php
/**
 * Created by Sevi Donnelly Foreman.
 * Date: 2018-12-12
 * Time: 17:30
 */

namespace DarlingCms\classes\privilege;


use DarlingCms\interfaces\privilege\IPermission;
use DarlingCms\interfaces\privilege\IRole;

/**
 * Class Role. Defines a simple implementation of the IRole interface.
 * @package DarlingCms\classes\privilege
 */
class Role implements IRole
{
    private $roleName;
    private $permissions = array();

    /**
     * Role constructor.
     * @param string $roleName The name to assign to the Role.
     * @param IPermission ...$permissions The permissions to assign to the Role.
     */
    public function __construct(string $roleName = 'Anonymous', IPermission ...$permissions)
    {
        $this->roleName = $roleName;
        $this->permissions = $permissions;
    }


    /**
     * Returns the name of the role.
     * @return string The name of the role.
     */
    public function getRoleName(): string
    {
        return $this->roleName;
    }

    /**
     * Returns an array of the permissions assigned to the role.
     * @return array Array of the permissions assigned to the role.
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * Returns true if the role is assigned the specified permission, false otherwise.
     * @param IPermission $permission The permission to check for.
     * @return bool True if the role is assigned the specified permission, false otherwise.
     */
    public function hasPermission(IPermission $permission): bool
    {
        return in_array($permission, $this->permissions, true);
    }

    private function unpackPermissions(string $packedPermissions)
    {
        // @todo imlement this method
        // Implement steps
        // 1. Unpack permissions (json_decode())
        // 2. Find permission by name
        // 3. Assign permission via addPermission() method
        // foreach($permissions as $permission){$this->addPermisssion()}
        $this->permissions = array();
    }

    private function addPermisssion(IPermission $permission)
    {
        array_push($this->permissions, $permission);
    }

    /**
     * run when writing data to inaccessible members.
     *
     * @param $name string
     * @param $value mixed
     * @return void
     * @link https://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __set($name, $value)
    {
        //var_dump($name);
        // TODO: Implement __set() method.
        if ($name === 'rolePermissions') {
            $this->unpackPermissions($value);
        }
    }


}
