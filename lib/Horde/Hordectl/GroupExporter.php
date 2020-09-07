<?php
namespace Horde\Hordectl;
use \Horde_Group_Base as GroupDriver;
/**
 * GroupExporter handles querying and formatting group representations
 */
class GroupExporter
{
    private $_driver;
    public function __construct(GroupDriver $driver)
    {
        $this->_driver = $driver;
    }

    public function export()
    {
        $items = [];
        foreach ($this->_driver->listAll() as $groupUid => $groupName) {
            $items[] = [
                'groupUid' => $groupUid,
                'groupName' => $groupName,
                'groupMembers' => $this->_driver->listUsers($groupUid)
            ];
        }
        return $items;
    }
}