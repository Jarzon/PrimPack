<style>
    .primToolbar {
        font-size: 22px;
        position: fixed;
        bottom: 0;
        left: 20px;
        background: #333;
        color: #eee;
        z-index: 999;
        max-width: 100%;
        max-height: 100%;
        padding: 5px;
        overflow: auto;
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
        user-select: none;
        cursor: pointer;
    }

    .primToolbar[open] > summary {
        padding: 5px;
        background: #222;
        text-align: center;
        width: calc(100% - 10px);
    }

    .primToolbar div {
        float: left;
        margin: 10px 20px;
    }

    .primToolbar b {
        background: #eee;
        color: #000;
    }

    .PDO, .DEBUG {
        clear: both;
        width: 100%;
        margin: 20px 0 0 !important;
    }

    .PDO details {
        overflow: auto;
        padding: 10px;
    }

    .PDO > details:nth-child(even) {
        background: #222;
    }

    @media print {
        .primToolbar {
            display: none;
        }
    }
</style>
<details class="primToolbar">
    <summary>Prim console</summary>
    <?php foreach ($_getToolbar() as $name => $callback):
        $output = $callback();
        if($output === '') continue;
        ?>
        <div class="<?=$name?>"><?=$name?> <?=$output?></div>
    <?php endforeach; ?>
</details>
