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
    <div class="logo">Prim <?= $_getLibraryVersion() ?></div>


    <div class="time">Time: <?=floor(xdebug_time_index() * 1000)?> ms</div>
    <div class="memory">Memory: <?=$_formatBytes(xdebug_memory_usage())?> / <?=$_formatBytes(xdebug_peak_memory_usage())?></div>
    <div class="sql">SQL: <?=$this->container->getPDO()->numExecutes?> / <?=$this->container->getPDO()->numExecutes?></div>
</div>