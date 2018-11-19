<?php $this->start('default'); ?>
    <h1>Administration</h1>

    <?php foreach ($sections as $name => $section): ?>
        <h2><a href="<?=$section?>"><?=$name?></a></h2>
    <?php endforeach; ?>
<?php $this->end(); ?>