<?php declare(strict_types=1);

namespace PrimPack\Command;

class RootPackageVersion
{
    public static function save(): void
    {
        $file = './vendor/composer/installed.php';
        $str = file_get_contents($file);
        $str = str_replace('dev-master', str_replace('v', '', exec('git describe --tags `git rev-list --tags --max-count=1`')), $str);
        file_put_contents($file, $str);
    }
}
