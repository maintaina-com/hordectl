<?php

namespace Horde\Hordectl\Command;
use \Horde_Cli_Modular_Module as Module;
use \Horde_Cli_Modular_ModuleUsage as ModuleUsage;
use \Horde\Hordectl\HordectlModuleTrait as ModuleTrait;
use \Horde\Hordectl\HasModulesTrait;
/**
 *
 * Query command module implements CLI Query Yaml output
 */
class Query
implements Module, ModuleUsage
{
    use ModuleTrait;
    use HasModulesTrait;
    public function __construct(\Horde_Injector $dependencies)
    {
        $this->dependencies = $dependencies;
        $this->cli = $dependencies->getInstance('\Horde_Cli');
        $this->_parser = $dependencies->getInstance('\Horde_Argv_Parser');
        // We stop parsing after the first positional
        $this->_parser->allowInterspersedArgs = false;
        $this->_initModules(
            $dependencies,
            '\Horde\Hordectl\Command\Query',
            dirname(__FILE__) . '/Query'
        );
    }

    /**
     * Decide if this module handles the commandline
     * 
     * @params array $globalOpts  Commandline Options already parsed by previous levels
     * @params array $argv        The arguments for the parser to digest
     */
    public function handle(array $argv = [])
    {
        // Do not act on empty argv
        if (count($argv) < 2) {
            return false;
        }
        if ($argv[1] != 'query') {
            return false;
        }
        $moduleArgs = $this->handleCommandline($argv);
        foreach ($this->listModules() as $module) {
            $module->handle($moduleArgs);
        }
        return true;
    }
}