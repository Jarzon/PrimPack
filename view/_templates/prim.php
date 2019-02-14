<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <title>Prim</title>
        <style>
            body {
                color: #eee;
                background: #444;
                margin: 0;
            }

            header {
                font-size: 25px;
                background: #333;
                padding: 25px 15%;
            }

            main {
                padding: 0 15%;
            }

            h1 {
                text-align: center;
            }

            h2 {
                color: rgb(190, 50, 50);
            }

            h3 {
                text-align: left;
            }

            .debug {
                text-align: center;
                font-size: 20px;
                color: #fff;
            }

            .debug .message {
                font-size: 30px;
            }

            .debug .location {

            }

            .debug .trace {
                text-align: left;
                padding: 10px;
                border: 2px solid #000;
                border-bottom: 0;
                font-size: 18px;
                color: #191919;
            }

            .debug .trace:last-child {
                border-bottom: 2px solid #000;
            }

            .debug .calledMethod {
                color: #ccc;
            }

            .debug .important {
                font-size: 22px;
                background: #555;
            }

            .debug .sql {
                text-align: left;
            }

            .debug .params {
                text-align: left;
            }

            table {
                color: #222;
                width: 100%;
            }

            th {
                padding: 5px;
            }

            tr {
                background-color: rgb(230,230,230);
            }
        </style>
    </head>
    <body>
        <header>
            <div class="logo">Prim</div>
        </header>

        <main role="main">
            <?= $this->section('default') ?>
        </main>
    </body>
</html>