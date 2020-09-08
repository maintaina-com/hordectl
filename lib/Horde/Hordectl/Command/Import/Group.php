<?php

namespace Horde\Hordectl\Command\Import;
use \Horde_Cli_Modular_Module as Module;
use \Horde_Cli_Modular_ModuleUsage as ModuleUsage;
use \Horde\Hordectl\HordectlModuleTrait as ModuleTrait;
/**
 *
 * Import command module for Horde groups
 */
class Group
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

    public function import(string $app, string $resource, array $tree)
    {
        if ($app != 'builtin' || $resource != 'group') {
            return false;
        }
        $items = $tree['apps']['builtin']['resources']['group']['items'];
        $hordeInjector = $this->dependencies->getInstance('HordeInjector');
        $hordeConfig = $this->dependencies->getInstance('HordeConfig');
        // initialize GroupImporter, mind any commandline or tree meta
        // Need to globalize $hordeConfig for the horde injector's factories
        $GLOBALS['conf'] = $hordeConfig;
        $groupDriver = $hordeInjector->getInstance('Horde_Group');
        unset($GLOBALS['conf']);
        $importer = new \Horde\Hordectl\GroupImporter($groupDriver);

        foreach ($items as $item) {
            $importer->import($item);
        }
        return true;
    }
}