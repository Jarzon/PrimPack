<?php
/** @var $this \Prim\Router */
$this->get('/admin/', 'PrimPack\Admin', 'index');
$this->get('/admin/logs/', 'PrimPack\Admin', 'logs');
$this->get('/admin/logs/show/{name:\S+}', 'PrimPack\Admin', 'showLog');
$this->get('/admin/logs/delete/{name:\S+}', 'PrimPack\Admin', 'deleteLog');
