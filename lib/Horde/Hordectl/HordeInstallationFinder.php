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

    /**
     * Find a horde installation and setup key assets
     * 
     * Currently, only implemented by assuming we are in a composer setup.
     * This allows us to hardcode a path.
     * More options are desirable.
     * 
     * @return boolean True if we found an installation
     */
    public function find(): bool
    {
        $originalGlobals = array_keys($GLOBALS);
        // For now, safe us headache. Remove these lines for added debugging/fixing fun.
        $originalGlobals[] = 'conf';
        $originalGlobals[] = 'session';
        $originalGlobals[] = 'registry';
        $originalGlobals[] = 'injector';
        // This really only works in composer deployments but it is OK for now
        $usualSuspect = realpath(dirname(__FILE__) .'/../../../../../../web/horde/lib/') . '/Application.php';
        if (file_exists($usualSuspect)) {
            require_once $usualSuspect;
            \Horde_Registry::appInit('horde', ['cli' => true]);
            $this->_injector = $GLOBALS['injector'];
            $this->_registry = $GLOBALS['registry'];
            $this->_config = $GLOBALS['injector']->getInstance('Horde_Config');
            $this->_config = $GLOBALS['conf'];
            return true;
        }
        return false;

        // Can we undo pollution of $GLOBAL ?
    }

    /**
     * Provide an installation's registry
     * 
     * @return Horde_Registry|null
     */
    public function getRegistry(): ?\Horde_Registry
    {
        return $this->_registry;
    }

    /**
     * Provide an installation's injector
     * 
     * @return Horde_Injector|null
     */
    public function getInjector(): ?\Horde_Injector
    {
        return $this->_injector;
    }

    /**
     * Provide an installation's config array
     * 
     * If possible, rather use th application's registry to
     * retrieve a Horde_Config instance instead
     * 
     * @return array
     */
    public function getConfig(): array
    {
        return $this->_config ?? [];
    }
}