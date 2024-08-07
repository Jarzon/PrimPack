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

        $this->addElement('',  function() {
            return $this->getVersion('jarzon/prim');
        });

        $this->addElement($this->options['project_name'] ?? 'Project version',  function() {
            return $this->getVersion('root');
        });

        $this->addElement('Env',  function() {
            return $this->options['environment'];
        });

        $this->addElement('Time',  function() {
            return $this->formatTime(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3);
        });

        $this->addElement('Memory',  function() {
            return $this->formatBytes(memory_get_usage()) . ' - ' . $this->formatBytes(memory_get_peak_usage()) . ' / ' . $this->formatBytes(memory_get_peak_usage(true));
        });

        $this->addElement('Stats', function() {
            $stats = [];

            if($this->container->exists('localizationService')) {
                $stats[] = count($this->container->get('localizationService')->messages) . ' messages';
            }

            $stats[] = $this->container->get('router')->getRoutesCount() . ' routes';

            return ': ' . implode(' - ', $stats);
        });

        $this->view->registerFunction('_getToolbar', function() {
            return $this->elements;
        });

        if($options['db_enable'] && $options['environment'] === 'dev') {
            $this->addElement('PDO', function() {
                $output = '';
                foreach ($this->container->get('pdo')->queries as $query) {
                    $output .= "<details><summary>$query[0]</summary>";

                    if(!isset($query[2]) || $query[2] === [[[]]]) {
                        $output .= "$query[1]";
                    } else {
                        $args = var_export($query[2], true);
                        $args = preg_replace("/ /m", "&nbsp;", $args);

                        $output .= "<details><summary>$query[1]</summary><pre>$args</pre></details>";
                    }

                    $output .= "</details>";
                }
                return "{$this->container->get('pdo')->numExecutes} / {$this->container->get('pdo')->numStatements}<br>$output";
            });
        }

        if(isset($GLOBALS['primDebug'])) {
            $this->addElement('DEBUG', function() {
                $output = '';
                foreach ($GLOBALS['primDebug'] as $debug) {
                    $output .= "<details><summary>$debug[0]</summary><pre>$debug[1]</pre></details>";
                }

                return $output;
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

    protected function formatTime(float $time, $precision = 3) {
        if($time < 1) {
            $time = round($time * 1000) . " ms";
        }
        else {
            $time = round($time, $precision) . " s";
        }

        return $time;
    }
}
