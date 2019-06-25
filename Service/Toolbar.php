<?php
namespace PrimPack\Service;

class Toolbar
{
    protected $view;
    protected $elements = [];
    protected $options = [];

    public function __construct($view, $pdo, array $options = [])
    {
        $this->view = $view;
        $this->pdo = $pdo;

        $this->options = $options += [
            'root' => '',
            'db_enable' => false
        ];

        $this->addElement('Prim',  function() {});
        $this->addElement('Version',  function() {});

        $this->addElement('Time',  function() {
            return substr(round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3), 2) . ' ms';
        });

        $this->addElement('Memory',  function() {
            return $this->formatBytes(memory_get_usage()) . ' - ' . $this->formatBytes(memory_get_peak_usage()) . ' / ' . $this->formatBytes(memory_get_peak_usage(true));
        });

        $this->view->registerFunction('_getToolbar', function() {
            return $this->getElements();
        });

        $this->addElement('Prim',  function() {
            return $this->getLibraryVersion();
        });

        $this->addElement('Version',  function() {
            $latestTag = exec('git describe --tags `git rev-list --tags --max-count=1`');

            return $latestTag;
        });

        if($options['db_enable']) {
            $this->addElement('PDO', function() {
                return "{$this->pdo->numExecutes} / {$this->pdo->numStatements}";
            });
        }
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

        $composerFile = "{$this->options['root']}composer.lock";
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