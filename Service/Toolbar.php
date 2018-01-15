<?php
namespace PrimPack\Service;

class Toolbar
{
    protected $view;

    public function __construct( $view)
    {
        $this->view = $view;

        $this->view->registerFunction('_formatBytes', function($bytes, $precision = 2) {
            return $this->formatBytes($bytes, $precision);
        });

        $this->view->registerFunction('_getLibraryVersion', function($lib = 'jarzon/prim') {
            return $this->getLibraryVersion($lib);
        });
    }

    function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    function getLibraryVersion($lib = 'jarzon/prim') {
        $version = '';

        $composerFile = ROOT . 'composer.lock';
        if(file_exists($composerFile)) {
            $jsonIterator = new \RecursiveIteratorIterator(
                new \RecursiveArrayIterator(json_decode(file_get_contents($composerFile), TRUE)),
                \RecursiveIteratorIterator::SELF_FIRST);

            $in = false;
            foreach ($jsonIterator as $key => $val) {
                if($key == 'name' && $val == $lib) {
                    $in = true;
                }

                if($in && $key == 'version') {
                    $version = $val;
                    break;
                }
            }

            $version = str_replace('v', '', $version);
        }

        return $version;
    }
}