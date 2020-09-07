<?php
namespace Horde\Hordectl;
/**
 * PermissionExporter handles querying and formatting permission representations
 */
class PermissionExporter
{
    private $_driver;
    public function __construct(\Horde_Perms_Base $driver, \Horde_Core_Perms $core)
    {
        $this->_driver = $driver;
        print_r($driver->getTree());
    }

    public function export()
    {
        $items = [];
        foreach ($this->_driver->getTree() as $permId => $permName) {
            // Filter parent node
            if ($permName == "-1") {
                continue;
            }
            $permission = $this->_driver->getPermission($permName);
            $data = $permission->getData();

            $items[] = [
                'permId' => $permId,
                'permName' => $permName,
                'type' => $data['type'],
                'users' => $data['users'] ?? [],
                'groups' => $permission->getGroupPermissions() ?? [],
                'default' => $permission->getDefaultPermissions(),
                'guest' => $permission->getGuestPermissions(),
                'creator' => $permission->getCreatorPermissions(),
                'present' => true
            ];
        }
        return $items;
    }
}