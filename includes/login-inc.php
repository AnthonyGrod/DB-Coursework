<?php
    $username = $_POST["nazwa"];
    $pwd = $_POST["pasw"];

    require_once 'dbh-inc.php';
    require_once 'functions-inc.php';

    if (emptyInputLogin($username, $pwd) !== false) {
        header("Location: /~ag438477/BD/zal/login.php?error=emptyinput");
        exit();
    }
    if (isset($_POST["submitOrg"])) {
        loginUser($conn, $username, $pwd, "org");
    } else if (isset($_POST["submitWidz"])) {
        loginUser($conn, $username, $pwd, "widz");
    } else {
        header("Location: /~ag438477/BD/zal/login.php?error=unknownerror");
        exit();
    }