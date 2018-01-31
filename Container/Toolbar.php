<?php
namespace PrimPack\Container;

trait Toolbar {
    /**
     * @return \PrimPack\Service\Toolbar
     */
    public function getToolbarService()
    {
        $obj = 'toolbarService';

        $this->setDefaultParameter($obj, '\PrimPack\Service\Toolbar');

        return $this->init($obj, $this->getView(), $this->getPDO(), $this->options);
    }
}