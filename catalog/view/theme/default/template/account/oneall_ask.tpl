<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $heading_title ?></title>
</head>

<style type="text/css">

    body {
        background-color: #EEE;
        margin: 0;

    }

    .container {
        font-family: 'Open Sans', sans-serif;
        font-size: 18px;
        max-width: 550px;
        margin-left: auto;
        margin-right: auto;
        background-color: white;
        height: 100vh;
        padding: 30px;
    }

    .entry {
        margin-bottom: 10px;
    }

    .entry span {
        display: inline-block;
        min-width: 150px;
        text-align: right;
        color: grey;
        margin-right: 10px;
    }

    .entry input {
        font-size: 18px;
        width: 250px;
        border: 3px solid #ddd;
        background: #f2f2f2;
        padding: 3px;
    }

    .invalid span {
        color: red;
    }

    .entry input:hover, .entry input:focus {
        border: 3px solid #bbb;
        background: #f7f7f7;
    }

    h1 {
        color: #4e69a2;
        margin-left: 160px;
    }

    input[type=submit] {
        margin-top: 20px;
        border: 3px solid #9ea9f2;
        background: #4e69a2;
        color: white;
        font-size: 22px;
        padding: 10px;
        margin-left: 160px;
    }

    input[type=submit]:hover {
        border: 3px solid #eee;
        background: #6e89c2;
        color: white;
    }

    .hidden {
        display: none;
    }

</style>

<body>

<div class="container">

    <h1><?php echo $heading_title ?></h1>

    <form action="<?php echo $action ?>" method="post">
        <?php foreach ($valid as $name => $ok) { ?>
            <div class="entry <?php if ($ok==-1) echo 'hidden'; elseif (!$ok) echo 'invalid' ?>">
                <span><?php if ($name[0]!='_') echo ${"entry_$name"} ?></span><input type="text" value="<?php echo ${$name} ?>" name="<?php echo $name ?>"/>
            </div>
        <?php } ?>
        <input type="submit" value="<?php echo $button_submit ?>"/>
    </form>
</div>

</body>
</html>