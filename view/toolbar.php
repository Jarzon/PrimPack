<style>
    .primToolbar {
        position: fixed;
        bottom: 0;
        left: 20px;
        background: #333;
        z-index: 999;
    }

    .primToolbar div {
        font-size: 20px;
        float: left;
        margin: 5px 5px 0 20px;
        min-width: 230px;
    }
</style>
<div class="primToolbar">
    <?php foreach ($_getToolbar() as $element): ?>
        <div class="<?=strtolower($element['name'])?>"><?=$element['name']?> <?=$element['value']?></div>
    <?php endforeach; ?>
</div>