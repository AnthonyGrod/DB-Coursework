<?php
    include_once './../header.php';
include_once './func-konkurs.php';
?>

<?php

$conn = pg_connect("host=lkdb dbname=bd user=ag438477 password=030757Jg");
// Sprawdzenie poprawnosci polaczenia z baza danych zawody.
if (!$conn) {
    echo "Wystąpił błąd podczas łączneia się z bazą.\n";
    exit;
}

$nazwa = $_SESSION['nazwa_konkursu']; // nazwa konkursu dla ktorego dodajemy kwote
$id_konkursu = get_id_konkursu($conn, $nazwa);
$jaka = jaka_seria($conn, $id_konkursu);
if ($jaka != "ZGLOSZENIA" && $jaka != "ZAKONCZONE") {
    header("location: ./wprowadz_skok.php");
    exit();
}
przekieruj($conn, $id_konkursu);



$jaka = jaka_seria($conn, $id_konkursu);
if ($jaka != "ZGLOSZENIA" && $jaka != "ZAKONCZONE") {
    header("location: ./wprowadz_skok");
    exit();
} else if ($jaka == "ZAKONCZONE") {
    header("location: ./../widz.php");
    exit();
}
?>

<?php
if (isset($_GET["komunikat"])) {
    if ($_GET["komunikat"] == "kwotaistnieje") {
        echo "<p>Wprowadzony kraj już istniał.</p>";
    } else if ($_GET["komunikat"] == "wprowadzkolejnykraj") {
        echo "<p>Wprowadz kolejny kraj</p>";
    } else if ($_GET["komunikat"] == "zawodnikistnieje") {
        echo "<p>Wprowadzony zawodnik już istnieje!</p>";
    } else if ($_GET["komunikat"] == "krajnieistnieje") {
        echo "<p>Wprowadzony kraj nie istnieje!</p>";
    } else if ($_GET["komunikat"] == "emptyinput") {
        echo "<p>Wprowadzony zawodnik nie istnieje!</p>";
    } else if ($_GET["komunikat"] == "przekroczylimityzawodnikow") {
        echo "<p>Przekroczono limit zawodnikow dla danego kraju!</p>";
    }
    if ($_GET["komunikat"] == "emptyinput") {
        echo "<p>Wprowadź wszystkie dane!</p>";
    } else if ($_GET["komunikat"] == "wronginput") {
        echo "<p>Wprowadź prawidłowo wszystkie dane!</p>";
    }
}

    

?>

<div class="form">
        <fieldset align="center">
            <legend>Dodaj Zawodnika Dla Konkursu <?php echo $nazwa ?></legend>
            <?php

            ?>
            <form name="dodaj_zawodnika" method="post" action="./dodaj_zawodnika.php">
                    <label for="Kraj Zawodnika">Kraj Zawodnika</label>
                    <input type="text" name="kraj_zawodnika" id="kraj_zawodnika">
                </p>
                <p>
                    <label for="Imie">Imie</label>
                    <input type="text" name="imie" id="imie">
                </p>
                <p>
                    <label for="Nazwisko">Nazwisko</label>
                    <input type="text" name="nazwisko" id="nazwisko">
                </p>
                <p>
                    <input type="submit" name="koniec_zawodnikow" value="Dodaje Zawodnika i Kończę Zamykając Zgłoszenia">
                </p>
                <p>
                    <input type="submit" name="dalej_zawodnikow" value="Dodaje Zawodnika ale Nie Kończę">
                </p>
            </form>
        </fieldset>

        <fieldset align="center">
            <form name="dodaj_zawodnika" method="post" action="./dodaj_zawodnika.php?info=przeskok_z_zawodnikow">
                    <input type="submit" name="skok" value="Przejdź do dodawania skoków i zamknij zgłoszenia natychmiast">
                </p>
            </form>
        </fieldset>
</div>


<?php
    include_once './../footer.php';
?>
