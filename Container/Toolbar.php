<?php
namespace PrimPack\Container;

trait Toolbar {
    /**
     * @return \PrimPack\Container\Toolbar
     */
    public function getToolbarService()
    {
        $obj = 'toolbarService';

        $this->setDefaultParameter($obj, '\PrimPack\Container\Toolbar');

        return $this->init($obj, $this->getView());
    }
}