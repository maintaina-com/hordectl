<?php
namespace Horde\HordeCtl;
/**
 * Horde Installation finder
 * 
 * Abstract access to an installation's registry and backends
 * Don't pollute global namespace
 */
class HordeInstallationFinder
{
    private $_injector;
    private $_registry;
    private $_config;

    public function __construct()
    {

    }

    public function find()
    {
        $originalGlobals = array_keys($GLOBALS);
        // For now, safe us headache. Remove these lines for added debugging/fixing fun.
        $originalGlobals[] = 'conf';
        $originalGlobals[] = 'session';
        $originalGlobals[] = 'registry';
        $originalGlobals[] = 'injector';
        // This really only works in composer deployments but it is OK for now
        require_once realpath(dirname(__FILE__) .'/../../../../../../web/horde/lib/') . '/Application.php';
        \Horde_Registry::appInit('horde', array('cli' => true));
        $this->_injector = $GLOBALS['injector'];
        $this->_registry = $GLOBALS['registry'];
        $this->_config = $GLOBALS['injector']->getInstance('Horde_Config');
        $this->_config = $GLOBALS['conf'];
        // Can we undo pollution of $GLOBAL ?
 /*       foreach (array_keys($GLOBALS) as $key) {
            if (!in_Array($key, $originalGlobals)) {
                unset($GLOBALS[$key]);
            }
        }*/
    }

    public function getInjector()
    {
        return $this->_injector;
    }

    public function getConfig()
    {
        return $this->_config;
    }
}