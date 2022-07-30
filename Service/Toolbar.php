<?php
namespace PrimPack\Service;

use Prim\Container;
use Prim\View;

class Toolbar
{
    protected View $view;
    protected Container $container;
    public array $elements = [];
    protected array $options = [];

    public function __construct(View $view, Container $container, array $options = [])
    {
        $this->view = $view;
        $this->container = $container;

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
            return $this->elements;
        });

        $this->addElement('Prim',  function() {
            return $this->getVersion('jarzon/prim');
        });

        $this->addElement('Env',  function() {
            return $this->options['environment'];
        });

        $this->addElement('Version',  function() {
            return $this->getVersion('root');
        });

        if($options['db_enable'] && $options['environment'] === 'dev') {
            $this->addElement('PDO', function() {
                return "{$this->container->get('pdo')->numExecutes} / {$this->container->get('pdo')->numStatements}";
            });
        }
    }

    public function getVersion(string $package): string
    {
        if($package === 'root') $package = \Composer\InstalledVersions::getRootPackage()['name'];
        return \Composer\InstalledVersions::getVersion($package);
    }

    public function addElement($name, callable $func) {
        $this->elements[$name] = $func;
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
