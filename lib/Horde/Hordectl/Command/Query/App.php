<?php
namespace Horde\Hordectl\Command\Query;
use \Horde_Cli_Modular_Module as Module;
use \Horde_Cli_Modular_ModuleUsage as ModuleUsage;
use \Horde\Hordectl\HordectlModuleTrait as ModuleTrait;
/**
 *
 * Query command module for resources implemented by Horde Registry Apps
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

    /**
     * Decide if this module handles the commandline
     * 
     * @params array $globalOpts  Commandline Options already parsed by previous levels
     * @params array $argv        The arguments for the parser to digest
     */
    public function handle(array $argv = [])
    {
        // Do not act on empty argv
        if (count($argv) < 1) {
            return false;
        }
        // Break out to apps to decide if we handle this.
        $apps = $this->dependencies->getRegistryApplications();
        list($app, $resource) = explode('/', $argv[0], 2);
        // Is that app registered?
        if (!in_array($app, $apps)) {
            return false;
        }
        $reader = $this->dependencies->getInstance('AppConfigReader');
        $config =  $reader->getAppConfig();
        $GLOBALS['conf'] = $config;

        $api = $this->dependencies->getApplicationResources($app);
        if (!$api) {
            return false;
        }
        $resources = $api->getTypeList();
        // Is that resource defined?
        if (!in_array($resource, $resources)) {
            return false;
        }
        // Does the app handle the query command?
        if (!method_exists($api, 'queryType')) {
            return false;
        }
        // Instanciate app context if necessary
        // Things outside PSR-4 compatible namespaces may be a bit brittle
        $writer = $this->dependencies->getInstance('\Horde\Hordectl\YamlWriter');
        $response = $api->queryType($resource);
        // Bail out if the resource does not implement queries
        if (empty($response)) {
            return false;
        }
        $writer->addResource($app, $resource, $response['items']);
        return true;
    }
}