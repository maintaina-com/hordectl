<?php
namespace Horde\Hordectl;

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
            ),
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
     * Return a list of resources implemented by an application
     * 
     * Resource Type IDs will be namespaced $App\$Resource
     * 
     * CLI will use lowercase names
     * 
     * @return string[] All resource names
     */
    public function getApplicationResources(string $app): array
    {
        /**
         * Check if the application provides a 
         * \Horde\$App\ApplicationResources class
         * TODO: Maybe check the registry for file root and deduce a location
         * For now, rely on the autoloader
         */
        $classname = '\Horde\\' . ucfirst($app) . '\ApplicationResources';

        if (!class_exists($classname)) {
            return [];
        }
        $app = $this->getInstance($classname);
        return $app->getTypeList();
    }
}