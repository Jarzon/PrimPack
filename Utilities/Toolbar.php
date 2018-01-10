<?php
namespace PrimPack\Utilities;

trait Toolbar
{
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

    public function toolbar() {
        if(DEBUG): ?>
            <style>
                .primToolbar .logo, .primToolbar .time, .primToolbar .memory, .primToolbar .sql {
                    font-size: 20px;
                    float: left;
                    margin: 5px 5px 0 20px;
                }

                .primToolbar .time {
                    min-width: 110px;
                }

                .primToolbar .memory {
                    min-width: 230px;
                }

                .primToolbar .sql {
                    min-width: 110px;
                }

                .primToolbar {
                    position: fixed;
                    bottom: 0;
                    left: 20px;
                    background: #333;
                    z-index: 999;
                }
            </style>
            <div class="primToolbar">
                <div class="logo">Prim <?= $this->getLibraryVersion() ?></div>


                <div class="time">Time: <?=floor(xdebug_time_index() * 1000)?> ms</div>
                <div class="memory">Memory: <?=$this->formatBytes(xdebug_memory_usage())?> / <?=$this->formatBytes(xdebug_peak_memory_usage())?></div>
                <div class="sql">SQL: <?=$this->container->getPDO()->numExecutes?> / <?=$this->container->getPDO()->numExecutes?></div>
            </div>

        <?php endif;
    }
}