<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/~ag438477/BD/zal/css/main.css">
    <title>Document</title>
</head>
<body>
    <div class="navbar" align="center">
        <?php 
            session_start();
            if (isset($_SESSION["mode"])) {
                if ($_SESSION["mode"] == "org") {
                    echo '<li><a href="/~ag438477/BD/zal/org.php">Strona Główna</a>';
                    echo '<li><a href="/~ag438477/BD/zal/wyloguj.php">Wyloguj</a>';
                    echo '<li><a href="/~ag438477/BD/zal/widz.php">Wyniki</a>';
                } else {
                    echo '<li><a href="/~ag438477/BD/zal/widz.php">Strona Główna</a>';
                    echo '<li><a href="/~ag438477/BD/zal/wyloguj.php">Wyloguj</a>';
                }
            } else {
                echo '<li><a href="/~ag438477/BD/zal/login.php">Logowanie</a>';
                echo '<li><a href="/~ag438477/BD/zal/signup.php">Tworzenie Konta</a>';
            }
        ?>
    </div>