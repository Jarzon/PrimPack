<?php /** @var $this \Prim\View */

$this->start('default'); ?>
    <h1>Logs</h1>

    <?php foreach ($logs as $section): ?>
        <h2><a href="/admin/logs/show/<?=$section?>"><?=$section?></a></h2>
    <?php endforeach; ?>
<?php $this->end(); ?>
