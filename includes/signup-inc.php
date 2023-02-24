<?php

    $login = $_POST["nazwa"];
    $pasw = $_POST["pasw"];
    if (isset($_POST["submitWidz"])) {
        $mode = "widz";
    } else {
        $mode = "org";
    }

    require_once 'dbh-inc.php';
    require_once 'functions-inc.php';

    $_SESSION["mode"] = $mode;

    if ($conn === false) {
        echo 'Connection status not ok';
        return false;
    }

    if (emptyInputSignup($login, $pasw) !== false) {
        header("Location: ../signup.php?error=emptyinput");
        exit();
    }

    if (invalidUid($login) !== false) {
        header("Location: ../signup.php?error=invaliduid");
        exit();
    }

    if (uidExists($conn, $login) !== false) {
        header("Location: ../signup.php?error=uidtaken");
        exit();
    }

    createUser($conn, $login, $pasw);
    pg_close($conn);

