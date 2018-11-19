<?php
namespace Libellum\BasePack\Controller;

use Prim\Controller;

class Admin extends Controller
{
    /** @var $admin \PrimPack\Service\Admin */
    protected $admin;

    public function build() {
        if(!$this->admin->isAdmin()) {
            header("HTTP/1.1 403 Forbidden");exit;
        }
    }

    public function index()
    {
        $this->render('admin', '', [
            'sections' => [
                'users' => '/admin/users/'
            ]
        ]);
    }
}
