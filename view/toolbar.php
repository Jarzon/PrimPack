<style>
    .primToolbar {
        font-size: 20px;
        position: fixed;
        bottom: 0;
        left: 20px;
        background: #333;
        color: #eee;
        z-index: 999;
        max-width: 100%;
        padding: 5px;
    }

    .primToolbar[open] {
        bottom: 0;
        left: 0;
        right: 0;
        padding: 0;
    }

    .primToolbar > summary {
        float: left;
        text-indent: 5px;
    }

    .primToolbar[open] > summary {
        padding: 5px;
        background: #222;
        text-align: center;
        width: 100%;
    }

    .primToolbar div {
        float: left;
        margin: 5px 5px 0 20px;
        min-width: 110px;
        max-width: 99%;
        overflow: auto;
    }

    .primToolbar b {
        background: #111;
    }

    @media print {
        .primToolbar {
            display: none;
        }
    }
</style>
<details class="primToolbar">
    <summary>Prim console</summary>
    <?php foreach ($_getToolbar() as $name => $callback): ?>
        <div><?=$name?> <?=$callback()?></div>
    <?php endforeach; ?>
</details>
