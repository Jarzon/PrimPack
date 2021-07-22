<?php
/** @var $this \Prim\Container */

$this
    ->register('toolbarService', \PrimPack\Service\Toolbar::class, [$this->get('view'), $this->options['db_enable']? $this->get('pdo'): null, $this->options])
    ->register('loggerService', \PrimPack\Service\Logger::class, [$this->options]);
