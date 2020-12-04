<?php
use Prim\Container;
use PrimPack\Controller\Admin;

return [
    PrimPack\Service\Logger::class => function(Container $dic) {
        return [$dic->options];
    },
    Admin::class => function(Container $dic) {
        $dic->get('userService')->verification(true);

        return [];
    },
    PrimPack\Controller\Error::class => function(Container $dic) {
        return [$dic->get('loggerService'), $dic];
    },
];
