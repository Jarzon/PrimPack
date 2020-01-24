<?php /** @var $this \Prim\View */

$content = nl2br(str_replace('  ', '<div class="tabs"></div>', $content));

foreach (['HTTP code', 'Date', 'Uri', 'IP', 'Type', 'Message', 'Finale file', 'Finale line', 'File', 'Line'] as $item) {
    $content = str_replace("{$item}:", "<div class='title'>{$item}</div>", $content);
}

$content = str_replace('Session:', "<details><summary>Session</summary>", $content);

$content = str_replace('POST:', "</details><details><summary>POST</summary>", $content);

$content = preg_replace("/\)$/s", ")</details>", $content, PREG_OFFSET_CAPTURE);

$this->start('default'); ?>
<style>
    .goback {
        float: left;
    }

    .delete {
        float: right;
    }

    .logs {
        font-size: 20px;
    }

    .title {
        float: left;
        width: 125px;
        font-weight: bold;
    }

    .tabs {
        float: left;
        height: 4px;
        width: 40px;
    }
</style>

<a href="/admin/logs/" class="goback">Go back</a>

<a href="/admin/logs/delete/<?=$name?>" class="delete">Delete</a>

<h1>Logs</h1>

<div class="logs">
    <?=$content?>
</div>
<?php $this->end(); ?>
