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
            $groupPerms = $this->_exportGroupPermissions($permission);

            $items[] = [
                'permId' => $permId,
                'permName' => $permName,
                'type' => $data['type'],
                'users' => $data['users'] ?? [],
                'groups' => $groupPerms,
                'default' => $permission->getDefaultPermissions(),
                'guest' => $permission->getGuestPermissions(),
                'creator' => $permission->getCreatorPermissions(),
                'present' => true
            ];
        }
        return $items;
    }

    protected function _exportGroupPermissions($permission) : array
    {
        $perms = [];
        foreach ($permission->getGroupPermissions() as $gid => $level)
        {
            if (!$this->_groupRepo->exists($gid)) {
                continue;
            }
            $perms[$this->_groupRepo->getGroupNameById($gid)] = $level;
        }
        return $perms;
    }
}