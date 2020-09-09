<?php
namespace Horde\Hordectl\Repository;
/**
 * Resource Group handles querying and formatting 
 * group representations
 */
class Permission
{
    private $_perms;
    private $_corePerms;
    private $_groupRepo;

    public function __construct(\Horde_Perms_Base $perms, \Horde_Core_Perms $corePerms, Group $group)
    {
        $this->_perms = $perms;
        $this->_corePerms = $corePerms;
        $this->_groupRepo = $group;
    }
    public function export()
    {
        $items = [];
        foreach ($this->_perms->getTree() as $permId => $permName) {
            // Filter parent node
            if ($permName == "-1") {
                continue;
            }
            $permission = $this->_perms->getPermission($permName);
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