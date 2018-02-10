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
        min-width: 110px;
    }

    .primToolbar .close {
        color: #882222;
        font-size: 18px;
        float: left;
        margin: 5px 10px 0 ;
    }

    @media print {
        .primToolbar {
            display: none;
        }
    }
</style>
<div class="primToolbar">
    <?php foreach ($_getToolbar() as $name => $callback): ?>
        <div><?=$name?> <?=$callback()?></div>
    <?php endforeach; ?>
    <span class="close" onclick="this.parentElement.style = 'display:none;'">❌</span>
</div>