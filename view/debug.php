<?php
/** @var $error Throwable */
?>
<div class="debug">
    <h2>An error occured:</h2>

    <div class="message"><?=$error->getMessage()?></div>
    <div class="location">in <?=$error->getFile()?> at line <?=$error->getLine()?></div>

    <?php if(strpos(get_class($error), 'PDO') !== false): ?>
        <?php $PDO = $this->container->getPDO();
        if(isset($PDO->lastQuery)):?>
            <h3>Query:</h3>
            <div class="sql"><?=nl2br($PDO->lastQuery)?></div>

            <h3>Params:</h3>
            <div class="params"><?=var_export($PDO->lastParams)?></div>
        <?php endif; ?>
    <?php endif; ?>

    <h2>Call trace:</h2>

    <?php
    foreach ($error->getTrace() as $i):
        if(!isset($i['file']) && !isset($i['line']) || !isset($i['class'])) continue; ?>
        <div class="trace">
            <div class="calledMethod"><?=($i['class'] ?? '') . '->'?><?=$i['function'] ?? ''?>()</div>

            <?=$i['file'] ?? ''?>:<?=$i['line'] ?? 0?>
        </div>
    <?php endforeach; ?>
</div>
