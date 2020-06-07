<?php
namespace PrimPack\Controller;

use Prim\AbstractController;

class Admin extends AbstractController
{
    public function index()
    {
        $this->render('admin/index', '', [
            'sections' => [
                'logs' => '/admin/logs/',
                'users' => '/admin/users/'
            ]
        ]);
    }

    public function logs()
    {
        $root = $this->options['root'] . 'data/logs/';

        $logs = glob("$root*");

        foreach ($logs as $index => $dir) {
            $logs[$index] = str_replace($root, '', $dir);
        }

        $this->render('admin/logs', '', [
            'logs' => $logs
        ]);
    }

    public function showLog($name)
    {
        $file = $this->options['root'] . 'data/logs/'.$name;

        $content = "The log file doesn't exist";

        if(file_exists($file)) {
            $content = file_get_contents($file);
        }

        $this->render('admin/showlog', '', [
            'name' => $name,
            'content' => $content
        ]);
    }

    public function deleteLog($name)
    {
        $file = $this->options['root'] . 'data/logs/'.$name;

        if(file_exists($file)) unlink($file);

        $this->redirect('/admin/logs/');
    }
}
