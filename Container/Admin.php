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

        $isAdmin = (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true)? true: false;

        return $this->init($obj, $isAdmin);
    }
}