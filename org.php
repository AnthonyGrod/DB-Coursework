<?php
    include_once 'header.php';
    if (isset($_GET['komunikat'])) {
        if ($_GET['komunikat'] == "emptyinput") {
            echo "<p>Wypełnij wszystkie pola!</p>";
        } else if ($_GET['komunikat'] == "wronginput") {
            echo "<p>Wypełnij prawidłowo wszystkie pola!</p>";
        } else if ($_GET['komunikat'] == "konkursnieistnieje") {
            echo "<p>Wprowadzony konkurs nie istnieje!</p>";
        } else if ($_GET['komunikat'] == "zlanazwa") {
            echo "<p>W nazwie konkursu mogą być tylko litery!</p>";
        }
    }
?>


    <div class="form">
        <fieldset align="center">
            <legend>Utwórz Konkurs</legend>
            <form name="stworz_konkurs" method="post" action="/~ag438477/BD/zal/konkurs/tworzenie_konkursu.php">
                <p>
                    <label for="NazwaKonkursu">Nazwa Konkursu</label>
                    <input type="text" name="nazwa_konkursu" id="nazwa_konkursu">
                <p>
                    <label for="Kraj">Kraj</label>
                    <input type="text" name="kraj" id="kraj">
                </p>
                <p>
                    <label for="KwotaStartowaKraju">Kwota Startowa Kraju</label>
                    <input type="number" name="kwota" id="kwota">
                </p>
                <p>
                    <label for="TerminRejestracji">Termin</label>
                    <input type="date" name="termin" id="termin">
                </p>
                <p>
                    <input type="submit" name="submit" id="submit" value="submit">
                </p>
            </form>
        </fieldset>

        <fieldset align="center">
            <legend>Zmodyfikuj Konkurs</legend>
            <form name="zmodyfikuj" method="post" action="/~ag438477/BD/zal/konkurs/zmodyfikuj_konkurs.php">
                <p>
                    <label for="Konkurs">Nazwa Konkursu</label>
                    <input type="text" name="nazwa_konkursu" id="nazwa_konkursu">
                </p>
                <p>
                    <input type="submit" name="submit" id="submit" value="submit">
                </p>
            </form>
        </fieldset>

    </div>
        
    <?php
    include_once 'footer.php';
?>
