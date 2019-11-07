<?php
namespace PrimPack\Service;

use PackageVersions\Versions;

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
            return Versions::getVersion('jarzon/prim');
        });

        $this->addElement('Version',  function() {
            return Versions::getVersion(Versions::ROOT_PACKAGE_NAME);
        });

        if($options['db_enable'] && $options['environment'] === 'dev') {
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
}
