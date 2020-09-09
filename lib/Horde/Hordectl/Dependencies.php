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
        $this->globalizeHordeConfig();

        $hordeInjector = $this->getInstance('HordeInjector');
        $hordeGroup = $hordeInjector->getInstance('Horde_Group');
        $hordePerms = $hordeInjector->getInstance('Horde_Perms');
        $hordeAuth = $hordeInjector->getInstance('Horde_Core_Factory_Auth')->create();
        $hordeCorePerms = $hordeInjector->getInstance('Horde_Core_Perms');

        $this->setInstance('GroupRepo',
            new Repository\Group($hordeGroup)
        );
        $this->setInstance('UserRepo',
            new Repository\User($hordeAuth)
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