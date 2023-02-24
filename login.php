<?php
session_destroy();
include_once 'header.php';
?>
<div class="body">
    <h2> Logowanie Do Panelu Organizatora </H2>
    <form action="/~ag438477/BD/zal/includes/login-inc.php" method="POST">
        Użytkownik: <input type="text" name="nazwa" value=""><br><br>
        Hasło: <input type="password" name="pasw" value=""><br><br>
        <button type="submitOrg" value="submitOrg" name=submitOrg>Zaloguj Do Panelu Organizatora</button>
    </form>

    <h2> Logowanie Do Panelu Widza </h2>
    <form action="/~ag438477/BD/zal/includes/login-inc.php" method="POST">
        Użytkownik: <input type="text" name="nazwa" value=""><br><br>
        Hasło: <input type="password" name="pasw" value=""><br><br>
        <button type="submitWidz" value="submitWidz" name="submitWidz">Zaloguj Do Panelu Widza</button>
    </form>

    <?php
    if (isset($_GET["error"])) {
        if ($_GET["error"] == "wronglogin") {
            echo "<p>Nieprawidłowy Login!</p>";
        } else if ($_GET["error"] == "wrongpassword") {
            echo "<p>Nieprawidłowe Hasło!</p>";
        } else if ($_GET["error"] == "emptyinput") {
            echo "<p>Pola nie mogą być puste!</p>";
        } else if ($_GET["error"] == "none") {
            echo "<p>Zalogowano!</p>";
        }
    }
    ?>
    </div>
    <?php
    include_once 'footer.php';
    ?>