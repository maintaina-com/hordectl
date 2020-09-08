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

    /**
     * Export Group names and members
     *
     * We don't export IDs as these don't reproduce well
     * TODO: handle email
     * 
     */
    public function export()
    {
        $items = [];
        foreach ($this->_driver->listAll() as $groupId => $groupName) {
            $items[] = [
                'groupName' => $groupName,
                'groupMembers' => $this->_driver->listUsers($groupId)
            ];
        }
        return $items;
    }
}