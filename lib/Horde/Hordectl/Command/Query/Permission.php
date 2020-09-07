<?php

namespace Horde\Hordectl\Command\Query;
use \Horde_Cli_Modular_Module as Module;
use \Horde_Cli_Modular_ModuleUsage as ModuleUsage;
use \Horde\Hordectl\HordectlModuleTrait as ModuleTrait;
/**
 *
 * Query command module for Horde permissions
 */
class Permission
implements Module, ModuleUsage
{
    use ModuleTrait;
    public function __construct(\Horde_Injector $dependencies)
    {
        $this->dependencies = $dependencies;
        $this->cli = $dependencies->getInstance('\Horde_Cli');
        $this->parser = $dependencies->getInstance('\Horde_Argv_Parser');
        // We stop parsing after the first positional
        $this->parser->allowInterspersedArgs = false;
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
        if ($argv[1] != 'permission') {
            // TODO: Identify modules. If no module argument is given or module does not exist,
            // print global help. Otherwise print module-specific help
            return false;
        }
        return true;
    }
}