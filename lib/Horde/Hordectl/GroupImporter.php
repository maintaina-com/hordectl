<?php
namespace Horde\Hordectl;
use \Horde_Group_Base as GroupDriver;
/**
 * GroupImporter handles importing group representations
 */
class GroupImporter
{
    private $_driver;
    public function __construct(GroupDriver $driver)
    {
        $this->_driver = $driver;
    }

    public function import(array $item)
    {
        // check if driver is readonly
        if ($this->_driver->readOnly()) {
            return;
        }
        // get gid if item already exists
        $gid = $this->getGroupIdByName($item['groupName']);
        // else create and get key
        if (empty($gid)) {
            $gid = $this->_driver->create($item['groupName']);
        }
        // TODO: Handle group email
        // ensure group has all wanted members
        if (!empty($item['groupMembers'])) {
            $members = $this->_driver->listUsers($gid);
            foreach ($item['groupMembers'] as $candidate) {
                if (!in_array($candidate, $members)) {
                    $this->_driver->addUser($gid, $candidate);
                }
            }
        }
    }

    /**
     * Resolve group names to Ids
     * 
     * In yaml, we generally reference groups by name
     * We don't have any means to import groups by a specific gid
     * 
     * However, most resources relate to groups by gid
     * Gid are also not really portable over backends
     * 
     * Sql gids are numeric, Ldap gids are strings
     */
    public function getGroupIdByName(string $name) : string
    {
        $id = '';
        foreach ($this->_driver->search($item['groupName']) as $gid => $name) {
            // search is fuzzy. Find exact match or none
            if ($name == $item['groupName']) {
                $id = $gid;
                break;
            }
        }
        return (string) $id;
    }
}