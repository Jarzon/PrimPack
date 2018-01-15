<style>
    .primToolbar .prim, .primToolbar .time, .primToolbar .memory {
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

    .primToolbar {
        position: fixed;
        bottom: 0;
        left: 20px;
        background: #333;
        z-index: 999;
    }
</style>
<div class="primToolbar">
    <?php foreach ($_getToolbar() as $element): ?>
        <div class="<?=strtolower($element['name'])?>"><?=$element['name']?> <?=$element['value']?></div>
    <?php endforeach; ?>
</div>