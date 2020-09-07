<?php

namespace Horde\Hordectl\Command\Query;
use \Horde_Cli_Modular_Module as Module;
use \Horde_Cli_Modular_ModuleUsage as ModuleUsage;
use \Horde\Hordectl\HordectlModuleTrait as ModuleTrait;
/**
 *
 * Query command module for Horde Group
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
        if ($argv[1] != 'group') {
            return false;
        }
        // TODO: accept some filters on which groups to export and which details to export
        $writer = $this->dependencies->getInstance('\Horde\Hordectl\YamlWriter');
        $hordeInjector = $this->dependencies->getInstance('HordeInjector');
        $hordeConfig = $this->dependencies->getInstance('HordeConfig');
        // Need to globalize $hordeConfig for the horde injector's factories
        $GLOBALS['conf'] = $hordeConfig;
        $groupDriver = $hordeInjector->getInstance('Horde_Group');
        unset($GLOBALS['conf']);

        $exporter = new \Horde\Hordectl\GroupExporter($groupDriver);
        $items = $exporter->export();
        $writer->addResource('builtin', 'group', $items);
        return true;
    }
}