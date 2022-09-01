<?php
/**
 * @var $error Throwable
 * @var $container \Prim\Container
 * @var $PDO \PrimPack\Service\PDO
 */
?>
<div class="debug">
    <h2>An error occured:</h2>

    <div class="message"><?=$error->getMessage()?></div>
    <div class="location">in <?=$error->getFile()?> at line <?=$error->getLine()?></div>

    <?php if(strpos(get_class($error), 'PDO') !== false): ?>
        <?php $PDO = $container->get('pdo');
        if(!empty($PDO->queries)):
            $lastQuery = $PDO->queries[array_key_last($PDO->queries)];?>
            <h3>Query:</h3>
            <div class="sql"><?=nl2br($lastQuery[1])?></div>

            <?php if(!empty($lastQuery[2])): ?>
            <h3>Params:</h3>
            <div class="params"><?=var_export($lastQuery[2], true)?></div>
        <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>

    <h2>Call trace:</h2>

    <?php
    foreach ($error->getTrace() as $i):
        $class = $i['class'] ?? null;

        if(!isset($i['file']) && !isset($i['line']) || !$class) continue; ?>
        <div class="trace<?=strpos($class, $this->options['project_name']) !== false ? ' important':'' ?>">
            <div class="calledMethod"><?=$class . '->'?><?=$i['function'] ?? ''?>()</div>

            <?=$i['file'] ?? ''?>:<?=$i['line'] ?? 0?>
        </div>
    <?php endforeach; ?>
</div>
<?=(isset($_getToolbar) AND $this->insert('toolbar', 'PrimPack'))?>