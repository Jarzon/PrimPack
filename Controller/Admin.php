<?php
namespace PrimPack\Controller;

use Prim\AbstractController;

class Admin extends AbstractController
{
    public function index()
    {
        $this->render('admin/index', '', [
            'sections' => [
                'users' => '/admin/users/'
            ]
        ]);
    }
}
