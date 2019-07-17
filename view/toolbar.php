<style>
    .primToolbar {
        position: fixed;
        bottom: 0;
        left: 20px;
        background: #333;
        color: #eee;
        z-index: 999;
    }

    .hiddenBar {
        height: 30px;
        width: 62px;
        overflow: hidden;
    }

    .primToolbar div {
        font-size: 20px;
        float: left;
        margin: 5px 5px 0 20px;
        min-width: 110px;
    }

    @media print {
        .primToolbar {
            display: none;
        }
    }
</style>
<div class="primToolbar hiddenBar" onclick="if(this.classList == 'primToolbar') this.classList = 'primToolbar hiddenBar'; else this.classList = 'primToolbar';">
    <?php foreach ($_getToolbar() as $name => $callback): ?>
        <div><?=$name?> <?=$callback()?></div>
    <?php endforeach; ?>
</div>
