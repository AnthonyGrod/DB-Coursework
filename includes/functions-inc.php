<?php
function emptyInputSignup($login, $pasw)
{
    $result = true;
    if (empty($login) || empty($pasw)) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}

function invalidUid($login)
{
    $result = true;
    if (!preg_match("/^[a-zA-Z0-9]*$/", $login)) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}

function uidExists($conn, $login)
{
    $sql = "SELECT * FROM uzytkownicy WHERE nazwa = $1";

    $query = pg_query_params($conn, $sql, array($login));
    $result = pg_fetch_all($query);
    $resultCheck = pg_num_rows($query);
    if ($resultCheck > 0) {
        return $result;
    } else {
        $result = false;
        return $result;
    }
}

function createUser($conn, $login, $pasw)
{
    $sql = "INSERT INTO uzytkownicy (nazwa, haslo) VALUES ($1, $2)";

    $hashedPasw = password_hash($pasw, PASSWORD_DEFAULT);

    $mode = $_SESSION["mode"];

    pg_query_params($conn, $sql, array($login, $hashedPasw));
    loginUser($conn, $login, $pasw, $mode);
    exit();
}

function emptyInputLogin($login, $pasw)
{
    $result = true;
    if (empty($login) || empty($pasw)) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}

function loginUser($conn, $login, $pasw, $mode)
{
    $uidExists = uidExists($conn, $login);

    if ($uidExists === false) {
        header("Location: /~ag438477/BD/zal/login.php?error=wronglogin");
        exit();
    }

    $pwdHashed = $uidExists[0]["haslo"];
    $checkPwd = password_verify($pasw, $pwdHashed);

    if ($checkPwd === false) {
        header("Location: /~ag438477/BD/zal/login.php?error=wrongpassword");
        exit();
    } else if ($checkPwd === true) {
        session_start();
        $_SESSION["login"] = $uidExists[0]["nazwa"];
        $_SESSION["haslo"] = $uidExists[0]["haslo"];
        if ($mode === "org") {
            $_SESSION["mode"] = "org";
            header("Location: /~ag438477/BD/zal/org.php");
            exit();
        } else if ($mode === "widz") {
            $_SESSION["mode"] = "widz";
            header("Location: /~ag438477/BD/zal/widz.php");
            exit();
        }
        exit();
    }
}