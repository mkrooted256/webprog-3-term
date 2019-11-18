<!DOCTYPE html>
<html lang="uk">
<head>
    <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <link href="https://fonts.googleapis.com/css?family=Comfortaa&display=swap&subset=cyrillic,cyrillic-ext"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Source+Code+Pro&display=swap" rel="stylesheet">


    <meta charset="UTF-8">
    <title>mkrooted aws mainframe</title>

    <style>
        body {
            /*font-family: 'Comfortaa', sans-serif;*/
            font-family: 'Source Code Pro', monospace;
        }

        .box {
            display: flex;
            justify-content: center;
            align-items: center;
            align-content: center;
            flex-direction: row;
            width: 100%;
            padding-top: 10%;
            height: 100%;
        }

        .box > a {
            width: 200px;
            height: 200px;
            border-radius: 10px;
            border: solid black 1px;
            box-shadow: darkgrey 4px 4px;
            font-size: xx-large;
            display: flex;
            justify-content: center;
            align-items: center;
            align-content: center;
            flex-direction: column;
            text-decoration: none;
            color: black;
            padding: 20px;
            margin-left: 30px;
        }

        .box > a:hover {
            position: relative;
            top: 4px;
            left: 4px;
            box-shadow: 0 0;
        }

        .box > a:active {
            color: hotpink;
            border-color: hotpink;
        }
    </style>
</head>
<body>
Ehehehehehehehehe
<div class="box">
    <?php
    $exclusions = [
            "145841-avatar-set"
    ];

    $dirs = glob("*", GLOB_ONLYDIR);
    foreach ($dirs as $dir) {
        if (array_search($dir, $exclusions)) continue;
        echo "<a href=\"$dir\"><div>$dir</div></a>";
    }
    ?>
</div>
</body>
</html>