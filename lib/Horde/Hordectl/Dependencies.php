<?php
/**
 * Dependencies of hordectl
 */
namespace Horde\Hordectl;
use \Horde\Hordectl\Configuration\AppConfigReader;

class Dependencies extends \Horde_Injector
{
    public function __construct($scope)
    {
        parent::__construct($scope);
        $finder = new HordeInstallationFinder();
        $finder->find();

        $this->setInstance('HordeInjector', $finder->getInjector());
        $this->setInstance('HordeConfig', $finder->getConfig());
        $this->setupCommonDependencies();
    }

    /**
     * Setup some dependencies which are too simple/common for a full factory
     */
    public function setupCommonDependencies()
    {

//        $this->globalizeHordeConfig();
        // Yes, this is really necessary for some factories. Sort this out
//        global $conf;
        $hordeInjector = $this->getInstance('HordeInjector');
        $hordeGroup = $hordeInjector->getInstance('Horde_Group');
        $hordePerms = $hordeInjector->getInstance('Horde_Perms');
//        $hordePrefs = $hordeInjector->getInstance('\Horde\Hordectl\Compat\Horde_Core_Factory_Prefs');
//        $hordeInjector->setInstance('Horde_Core_Factory_Prefs', $hordePrefs);
//        $this->setInstance('Horde_Core_Factory_Prefs', $hordePrefs);
        $hordeAuth = $hordeInjector->getInstance('Horde_Core_Factory_Auth')->create();
        $this->setInstance('\Horde_Auth_Base', $hordeAuth);
        $this->setInstance('HordeInstallationFinder', new HordeInstallationFinder());
        $this->setInstance('AppConfigReader', $this->getInstance('\Horde\Hordectl\Configuration\AppConfigReader'));

        $hordeIdentity = $hordeInjector->getInstance('Horde_Core_Factory_Identity');
/*        $this->setInstance('Horde_Core_Factory_Identity', $hordeIdentity);
        $hordeInjector->setInstance('Horde_Core_Factory_Identity', $hordeIdentity);*/
        $hordeCorePerms = $hordeInjector->getInstance('Horde_Core_Perms');

        $this->setInstance('GroupRepo',
            new Repository\Group($hordeGroup)
        );
        $this->setInstance('UserRepo',
            new Repository\User($hordeAuth, $hordeIdentity)
        );
        $this->setInstance('PermsRepo',
            new Repository\Permission(
                $hordePerms, 
                $hordeCorePerms, 
                $this->getInstance('GroupRepo')
            )
        );
//        $this->unglobalizeHordeConfig();
        return $this;
    }
    /**
     * Expose Horde Config in global namespace
     * 
     * @return Dependencies
     */
    public function globalizeHordeConfig()
    {
        global $conf;
        $GLOBALS['conf'] = $this->getInstance('HordeConfig');
        return $this;
    }

    /**
     * Hide Horde Config from global namespace
     * 
     * @return Dependencies
     */
    public function unglobalizeHordeConfig()
    {
        unset($GLOBALS['conf']);
        return $this;
    }

    /**
     * Return a list of applications from the Horde Registry
     * 
     * If we have not connected to a working registry, the list will be empty
     * 
     * @return string[]
     */
    public function getRegistryApplications(): array
    {
        $finder = $this->getInstance('HordeInstallationFinder');
        if ($finder->find()) {
            $registry = $finder->getRegistry();
            return $registry->listAllApps();
        }
        return[];
    }

    /**
     * Return the application resource provider
     * 
     * @return object
     */
    public function getApplicationResources(string $app): ?object
    {
        $hordeInjector = $this->getInstance('HordeInjector');
        $registry = $hordeInjector->getInstance('Horde_Registry');

        /**
         * Check if registry has that application
         */
         if (empty($registry->applications[$app])) {
             return null;
         }
        /**
         * Setup basic autoloading for this app
         */
         // Get the Horde or Composer Autoloader
         $hordeAutoloader = null;
         $composerAutoloader = null;
         foreach (spl_autoload_functions() as $id => $loader) {
             // TODO: Make this more robust
             if (get_class($loader[0]) == 'Composer\Autoload\ClassLoader') {
                 $composerAutoloader = $loader[0];
             }
             if (get_class($loader[0]) == 'Horde_Autoloader_Default') {
                 $hordeAutoloader = $loader[0];
             }
         }
        // Set application dirs
        $libdir = realpath($registry->get('fileroot', $app)) . '/lib/';
        if ($composerAutoloader) {
            // This seems to do nothing
            $composerAutoloader->add(ucfirst($app) . '_', $libdir);
            // PSR-4
            $composerAutoloader->addPsr4('Horde\\' . ucfirst($app) . '\\', $libdir);
            $composerAutoloader->register();
        }

        if ($hordeAutoloader) {
            $hordeAutoloader->addClassPathMapper(
            new \Horde_Autoloader_ClassPathMapper_PrefixString(
                $app, $libdir
            ));
        }

        /**
         * Check if the application provides a 
         * \Horde\$App\ApplicationResources class
         * TODO: Maybe check the registry for file root and deduce a location
         * For now, rely on the autoloader
         */
        $classname = '\Horde\\' . ucfirst($app) . '\ApplicationResources';

        if (!class_exists($classname)) {
            return null;
        }
        $appResources = $hordeInjector->getInstance($classname);
        return $appResources;
    }
}
