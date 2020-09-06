<?php
/**
 * HasModulesTrait provides implementation for the ModuleProvider interface
 * 
 * Use this in your root module / Modular cli bootstrapping class or in
 * a module which has submodules
 * 
 * This is essentially similar to \Horde_Cli_Modular_Modules
 * and \Horde_Cli_Modular_ModuleProvider
 */
namespace Horde\Hordectl;
trait HasModulesTrait {

    private $_modules = [];


    /**
     * Initialize the list of direct submodules to the current module
     *
     * First check a certain folder for files and assume they adhere to a certain class name schema
     * TODO: Then check all registered apps for a list of well known classnames
     *
     * Initialize all modules and assign them their parent
     */
    private function _initModules(\Horde_Injector $dependencies, string $prefix, string $directory, array $exclude = [])
    {
        if (empty($directory)) {
            throw new \Horde_Cli_Modular_Exception(
                'The "directory" parameter is missing!'
            );
        }
        if (!file_exists($directory)) {
            throw new \Horde_Cli_Modular_Exception(
                sprintf(
                    'The indicated directory %s does not exist!',
                    $directory
                )
            );
        }
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $file) {
            if ($file->isFile() && preg_match('/.php$/', $file->getFilename())) {
                $class = preg_replace("/^(.*)\.php/", '\\1', $file->getFilename());
                if (!in_array($class, $exclude)) {
                    $fullname = $prefix . '\\' .$class;
                    $this->_modules[$fullname] =  $dependencies->getInstance($fullname);
                }
            }
        }
        sort($this->_modules);
    }

    /**
     * List the available modules.
     *
     * @return array The list of modules.
     */
    public function listModules()
    {
        return $this->_modules;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->_modules);
    }

    /**
     * Implementation of Countable count() method. Returns the number of modules.
     *
     * @return integer Number of modules.
     */
    public function count()
    {
        return count($this->_modules);
    }
}