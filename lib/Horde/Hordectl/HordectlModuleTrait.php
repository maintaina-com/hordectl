<?php
/**
 * HordectlModuleTrait provides implementation for the HordectlModuleInterface
 *
 */
namespace Horde\Hordectl;
trait HordectlModuleTrait {

    private $_parentModule;

    public function isRootModule()
    {
        return $this->getParentModule() === $this;
    }

    public function getParentModule() : \Horde_Cli_Modular_Module
    {
        return $this->_parentModule ?? $this;
    }

    public function setParentModule(\Horde_Cli_Modular_Module $module)
    {
        // TODO: prevent circular relation
        $this->_parentModule = $module;
    }

    public function getUsage()
    {

    }

    public function getBaseOptions()
    {

    }

    public function hasOptionGroup()
    {
        return true;
    }

    public function getOptionGroupDescription()
    {

    }

    public function getOptionGroupOptions($action = null)
    {
    }

    public function getOptionGroupTitle()
    {
        return '';
    }

    public function getTitle()
    {
        return (new \ReflectionClass($this))->getShortName();
    }

}