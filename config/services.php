<?php
use Prim\Container;

return [
    PrimPack\Controller\Admin::class => function(Container $dic) {
        if(!$dic->get('adminService')->isAdmin()) {
            header("HTTP/1.1 403 Forbidden");exit;
        }

        return [];
    },
];
