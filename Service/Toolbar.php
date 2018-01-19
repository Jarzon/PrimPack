<?php
namespace PrimPack\Service;

class Toolbar
{
    protected $view;
    protected $elements;

    public function __construct($view)
    {
        $this->view = $view;

        $this->view->registerFunction('_getToolbar', function() {
            return $this->getElements();
        });

        $this->addElement('Prim',  function() {
            return $this->getLibraryVersion();
        });

        $this->addElement('Time',  function() {
            return floor(xdebug_time_index() * 1000) . ' ms';
        });

        $this->addElement('Memory',  function() {
            return $this->formatBytes(xdebug_memory_usage()) . ' / ' . $this->formatBytes(xdebug_peak_memory_usage());
        });

        $this->addElement('PDO', function() {
            $pdo = $this->getPDO();
            return "{$pdo->numExecutes} / {$pdo->numStatements}";
        });
    }

    public function addElement($name, callable $func) {
        $this->elements[$name] = $func;
    }

    public function getElements() : array {
        return $this->elements;
    }

    protected function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    protected function getLibraryVersion($lib = 'jarzon/prim') {
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