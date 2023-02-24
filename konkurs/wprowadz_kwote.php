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

$nazwa = $_SESSION['nazwa_konkursu'];
$kraj = $_SESSION['nazwa_kraj'];
$id_konkursu = get_id_konkursu($conn, $nazwa);
$jaka = jaka_seria($conn, $id_konkursu);
if ($jaka != "ZGLOSZENIA" && $jaka != "ZAKONCZONE") {
    header("location: ./wprowadz_skok.php");
    exit();
}
przekieruj($conn, $id_konkursu);
?>

<?php
    if (isset($_GET["komunikat"])) {
        if ($_GET["komunikat"] == "kwotaistnieje") {
            echo "<p>Wprowadzony kraj już istnieje!</p>";
        } else if ($_GET["komunikat"] == "wprowadzkolejnykraj") {
            echo "<p>Wprowadz kolejny kraj</p>";
        } else if ($_GET['komunikat'] == "emptyinput") {
            echo "<p>Wypełnij wszystkie pola!</p>";
        } else if ($_GET["komunikat"] == "wronginput") {
            echo "<p>Wprowadź prawidłowo wszystkie dane!</p>";
        }
    }

?>

<div class="form">
        <fieldset align="center">
            <legend>Dodaj Kwote Dla Konkursu <?php echo $nazwa ?></legend>
            <?php

            ?>
            <form name="dodaj_kwote" method="post" action="./dodaj_kwote.php">
                    <label for="Kraj">Kraj</label>
                    <input type="text" name="kraj" id="kraj">
                </p>
                <p>
                    <label for="Kwota">Kwota</label>
                    <input type="number" name="kwota" id="kwota">
                </p>
                <p>
                    <input type="submit" name="koniec" value="Wprowadzam Kwotę i Kończę Wprowadzanie">
                </p>
                    <input type="submit" name="dalej" value="Wprowadzam Kwotę ale Nie Kończę">
                </p>
                <input type="submit" name="koniec_bez_kwoty" value="Nie Wprowadzam Żadmej Kwoty i Kończę Wprowadzanie">
            </form>
        </fieldset>
</div>


<?php
    include_once './../footer.php';
?>
