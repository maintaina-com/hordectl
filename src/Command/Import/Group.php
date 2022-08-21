<?php

namespace Horde\Hordectl\Command\Import;

use Horde\Hordectl\HordectlModuleTrait as ModuleTrait;
use Horde_Cli_Modular_Module as Module;
use Horde_Cli_Modular_ModuleUsage as ModuleUsage;

/**
 *
 * Import command module for Horde groups
 */
class Group implements Module, ModuleUsage
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

    public function import(string $app, string $resource, array $tree)
    {
        if ($app != 'builtin' || $resource != 'group') {
            return false;
        }
        $items = $tree['apps']['builtin']['resources']['group']['items'];
        $importer = $this->dependencies->getInstance('GroupRepo');

        foreach ($items as $item) {
            $importer->import($item);
        }
        return true;
    }
}
