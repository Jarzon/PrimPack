<?php
use Prim\Container;

return [
    PrimPack\Controller\Admin::class => function(Container $dic) {
        return [
            $dic->get('adminService'),
        ];
    },
];