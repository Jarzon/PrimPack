<?php
/**
 * @var $error Throwable
 */
?>
<div class="debug">

    <?php if(get_class( $error ) == 'Exception'): ?>
        <h2>Exception occured:</h2>
    <?php elseif(get_class( $error ) == 'Error'): ?>
        <h2>Error occured:</h2>
    <?php elseif(get_class( $error ) == 'ParseError'): ?>
        <h2>Fatal parsing error occured:</h2>
    <?php else: ?>
        <h2>Error occured:</h2>
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
