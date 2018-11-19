<?php
namespace PrimPack\Container;

trait Toolbar {
    /**
     * @return \PrimPack\Service\Toolbar
     */
    public function getToolbarService()
    {
        $obj = 'toolbarService';

        $this->setDefaultParameter($obj, \PrimPack\Service\Toolbar::class);

        $pdo = $this->options['db_enable']? $this->getPDO() : null;

        return $this->init($obj, $this->getView(), $pdo, $this->options);
    }
}