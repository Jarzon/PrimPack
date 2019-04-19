<?php
namespace PrimPack\Controller;

use Prim\Controller;

class Admin extends Controller
{
    protected $admin;

    public function __construct(\Prim\View $view, array $options, \PrimPack\Service\Admin $admin)
    {
        parent::__construct($view, $options);

        $this->admin = $admin;

        if(!$this->admin->isAdmin()) {
            header("HTTP/1.1 403 Forbidden");exit;
        }
    }

    public function index()
    {
        $this->render('admin/index', '', [
            'sections' => [
                'users' => '/admin/users/'
            ]
        ]);
    }
}
