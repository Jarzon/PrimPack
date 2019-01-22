<?php
namespace PrimPack\Container;

trait Admin {
    /**
     * @return \PrimPack\Service\Admin
     */
    public function getAdminService()
    {
        $obj = 'adminService';

        $this->setDefaultParameter($obj, \PrimPack\Service\Admin::class);

        return $this->init($obj, $_SESSION?? []);
    }
}