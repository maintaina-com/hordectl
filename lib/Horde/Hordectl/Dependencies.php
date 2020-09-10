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
        $hordePrefs = $hordeInjector->getInstance('\Horde\Hordectl\Compat\Horde_Core_Factory_Prefs');
/*        $hordeInjector->setInstance('Horde_Core_Factory_Prefs', $hordePrefs);
        $this->setInstance('Horde_Core_Factory_Prefs', $hordePrefs);*/
        $hordeAuth = $hordeInjector->getInstance('Horde_Core_Factory_Auth')->create();
        $this->setInstance('\Horde_Auth_Base', $hordeAuth);

        $hordeIdentity = $hordeInjector->getInstance('\Horde\Hordectl\Compat\Horde_Core_Factory_Identity');
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
        $this->unglobalizeHordeConfig();
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

}