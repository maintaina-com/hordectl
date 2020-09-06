<?php
/**
 * hordectl CLI Root module
 */

namespace Horde\Hordectl;
use \Horde_Injector as Injector;
use \Horde_Injector_TopLevel as TopLevelInjector;
use \Horde_Cli_Modular as Cli_Modular;
use \Horde_Cli_Modular_Module as Module;
use \Horde_Argv_IndentedHelpFormatter as IndentedHelpFormatter;
use \Horde_Argv_Parser as Parser;
/**
 * Hordectl CLI Root Module
 *
 * The basic idea of hordectl is a very modular approach.
 * Everything is either a leaf module or a parent module regardless of its level.
 *
 * Apart from the static Cli::main method which handles all the initial setup,
 * Horde\Hordectl\Cli is just a parent module like any other
 *
 * Modules know their runtime parents and their parent's parsed config but they may appear in any other part of the tree if called by another frontend
 *
 * It is safe for a parent to assume its builtin children exist (like the help submodule)
 *
 * In fact, hordectl is a spinoff from an application specific solution
 */
class Cli implements Module
{
    use HordectlModuleTrait;
    use HasModulesTrait;

    public function __construct(\Horde_Injector $dependencies)
    {
        $this->dependencies = $dependencies;
        $this->cli = $dependencies->getInstance('\Horde_Cli');
        $this->parser = $dependencies->getInstance('\Horde_Argv_Parser');
        // We stop parsing after the first positional
        $this->parser->allowInterspersedArgs = false;
        $prefix = '\Horde\Hordectl\Command';
        $directory = dirname(__FILE__) . '/Command/';
        $exclude = [];
        $this->_initModules($dependencies, $prefix, $directory, $exclude);
    }

    // Setup a Horde_Cli_Modular, a Parser, setup self as root module
    public static function main(array $parameters = array())
    {
        // Use plain Horde Injector as long as we have no need to wrap it into something more specific
        $dependencies = new Injector(new TopLevelInjector);
        $dependencies->setInstance('\Horde\Hordectl\Dependencies', $dependencies);

        $cli = new \Horde_Cli(array('pager' => true));
        $dependencies->setInstance('\Horde_Cli', $cli);

        // Setup the CLI Parser.
        $parser = $dependencies->getInstance('\Horde_Argv_Parser');
        $parser->allowInterspersedArgs = false;
        // Setup the modules system
        $modular = self::_prepareModular($dependencies);
        // Setup self as the root module
        $CliModule = $dependencies->getInstance('\Horde\Hordectl\Cli');
        if ($CliModule->isRootModule()) {
            $cli->writeln($CliModule->getTitle() . " is the root module");
        }
        // preliminary index of commands
        $cli->writeln("Found Modules:");
        foreach ($CliModule->listModules() as $module) {
            $cli->writeln(\Horde_String::lower($module->getTitle()));
        }

        // Fetch the cli module's direct parameters and run its handle method

    }

   /**
     * Prepare the modular CLI instance.
     *
     * Adapted from Horde git-tools CLI
     * @param  \Horde_Injector $dependencies  The dependency container.
     *
     * @return \Horde_Cli_Modular  The modular CLI object.
     */
    protected static function _prepareModular($dependencies)
    {
        // The modular CLI helper.
        $formatter = new IndentedHelpFormatter();
        $modular = new Cli_Modular(array(
            'parser' => array('usage' => '[OPTIONS] COMMAND [ARGUMENTS]
  ' . $formatter->highlightOption('COMMAND') . ' - Selects the command to perform. This is a list of possible commands:
'
            ),
            'modules' => array(
                'directory' => __DIR__ . '/Command/',
                'exclude' => 'Base'
            ),
            'provider' => array(
                'prefix' => '\Horde\Hordectl\Command\\',
                'dependencies' => $dependencies
            ),
            'cli' => $dependencies->getInstance('\Horde_Cli'),
        ));
        return $modular;
    }
}