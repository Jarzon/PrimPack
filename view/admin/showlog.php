<?php /** @var $this \Prim\View */

$this->start('default'); ?>
<style>
    .goback {
        float: left;
    }

    .tabs {
        float: left;
        height: 4px;
        width: 40px;
    }
</style>

<a href="/admin/logs/" class="goback">Go back</a>

<h1>Logs</h1>

<div>
    <?=nl2br(str_replace("  ", "<div class=\"tabs\"></div>", $content))?>
</div>
<?php $this->end(); ?>
