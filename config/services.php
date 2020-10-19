<?php
use Prim\Container;
use PrimPack\Controller\Admin;

return [
    Admin::class => function(Container $dic) {
        if(!$dic->get('adminService')->isAdmin()) {
            header("HTTP/1.1 403 Forbidden");exit;
        }

        return [];
    },
    PrimPack\Controller\Error::class => function(Container $dic) {
        return [$dic->get('loggerService'), $dic];
    },
];
