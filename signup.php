<?php
session_destroy();
include_once 'header.php';
?>

    <h2> Tworzenie Konta Do Panelu Organizatora </H2>
    <form action="/~ag438477/BD/zal/includes/signup-inc.php" method="post">
        <input type="text" name="nazwa" placeholder="login"><br><br>
        <input type="password" name="pasw" placeholder="haslo"><br><br>
        <button type="submitOrg" value="submitOrg" name="submitOrg">Stworz</button>
</form>

    <h2> Tworzenie Konta Do Panelu Widza </h2>
    <form action="/~ag438477/BD/zal/includes/signup-inc.php" method="post">
        <input type="text" name="nazwa" placeholder="login"><br><br>
        <input type="password" name="pasw" placeholder="haslo"><br><br>
        <button type="submitWidz" value="submitWidz" name="submitWidz">Stworz</button>
    </form>

    <?php
    if (isset($_GET["error"])) {
        if ($_GET["error"] == "emptyinput") {
            echo "<p>Wypełnij wszystkie pola!</p>";
        } else if ($_GET["error"] == "invaliduid") {
            echo "<p>Wybierz poprawny login!</p>";
        } else if ($_GET["error"] == "uidtaken") {
            echo "<p>Login jest już zajęty!</p>";
        } else if ($_GET["error"] == "none") {
            echo "<p>Utworzono konto!</p>";
        }
    }
    ?>


    <?php
    include_once 'footer.php';
    ?>