<?php
namespace Horde\Hordectl\Repository;
use \Horde_Auth_Base as AuthDriver;
/**
 * Resource User handles querying and formatting 
 * user representations
 */
class User
{
    private $_driver;
    public function __construct(AuthDriver $driver)
    {
        $this->_driver = $driver;
    }

    /**
     * Export user uid's
     * 
     * TODO: handle primary identity (name, email)
     * 
     */
    public function export()
    {
        $items = [];

        foreach ($this->_driver->listUsers() as $uid) {
            print_r([$items, $uid]);
            $items[] = [
                'userUid' => $uid,
                'isLocked' => $this->_driver->hasCapability('lock') ? 
                    $this->_driver->isLocked($uid) :
                    false
            ];
        }
        return $items;
    }

    public function import(array $item)
    {
        // check if driver is readonly
        if (!$this->_driver->hasCapability('add')) {
            return;
        }
        if (!$this->_driver->exists($item['userUid'])) {
            $password = empty($item['password']) ? 
                \Horde_Auth::genRandomPassword() :
                $item['password'];
            $credentials = ['password' => $password];
            $this->_driver->addUser($item['userUid'], $credentials);
        }
        // TODO: Handle locked users and expiration
        // TODO: Handle primary identity (name, email)
    }


    public function exists(string $uid) : bool
    {
        return $this->_driver->exists($uid);
    }
}