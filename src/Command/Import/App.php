<?php

namespace Horde\Hordectl\Command\Import;
use \Horde_Cli_Modular_Module as Module;
use \Horde_Cli_Modular_ModuleUsage as ModuleUsage;
use \Horde\Hordectl\HordectlModuleTrait as ModuleTrait;
/**
 *
 * Import command module for Horde App provided resources
 */
class App
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
        // Break out to apps to decide if we handle this.
        $apps = $this->dependencies->getRegistryApplications();

        // Is that app registered?
        if (!in_array($app, $apps)) {
            return false;
        }
        $reader = $this->dependencies->getInstance('AppConfigReader');
        $config =  $reader->getAppConfig($app);
        $GLOBALS['conf'] = $config;

        $api = $this->dependencies->getApplicationResources($app);
        $resources = $api->getTypeList();
        // Is that resource defined?
        if (!in_array($resource, $resources)) {
            return false;
        }
        // Does the app handle the query command?
        if (!method_exists($api, 'importType')) {
            return false;
        }
        $items = $tree['apps'][$app]['resources'][$resource]['items'];
        $response = $api->importType($resource, $items);

        return true;
    }
}