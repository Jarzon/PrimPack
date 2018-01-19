<?php
/** @var $error Throwable */
?>
<div class="debug">
    <h2>An error occured:</h2>

    <?php if(strpos($error->getMessage(), 'PDO') !== false): ?>
        <?php $PDO = $this->container->getPDO();?>
        <div class="sql">Query: <?=nl2br($PDO->lastQuery)?></div>
        <div class="params">Params: <?=var_export($PDO->lastParams)?></div>
    <?php endif; ?>

    <div class="message"><?=$error->getMessage()?></div>
    <div class="location">in <?=$error->getFile()?> at line <?=$error->getLine()?></div>

    <h2>Call trace:</h2>

    <?php
    $line = $error->getLine();
    foreach ($error->getTrace() as $i): ?>
        <div class="trace">
            <?=isset($i['class'])? $i['class'] . '->': ''?><?=isset($i['function'])? $i['function']: ''?>(<?=implode(', ', isset($i['params'])? $i['params']: [])?>):<?=$line?>
        </div>
    <?php
        $line = isset($i['line'])? $i['line']: 0;
    endforeach; ?>
</div>
