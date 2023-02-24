<?php
include_once './../includes/dbh-inc.php';
include_once "./func-konkurs.php";
?>

<?php
$nazwa_konkursu = $_SESSION["nazwa_konkursu"];
// Patrzymy, jakie jest id konkurusu, dla którego dodajemy skok.
$id_konkursu = get_id_konkursu($conn, $nazwa_konkursu);
if (isset($_GET['komunikat'])) {
    if ($_GET['komunikat'] == "skokdodany") {
        echo "Skok Dodany!";
    }
}

// Patrzymy, jaka jest seria konkursu
$seria = jaka_seria($conn, $id_konkursu);

if ($seria == "KWALFIKACYJNA") {
    $id_zgloszenia_do_skoku = id_zgloszenia_zawodnika_do_skoku($conn, $id_konkursu, 1, 0, 0);
    if ($id_zgloszenia_do_skoku == -1) {
        // Ustawiamy, że konkurs jest teraz w fazie skoków serii pierwszej
        $sql = "UPDATE konkurs SET czy_podczas_serii_kwalfikacyjnej = 'f', czy_podczas_serii_pierwszej = 't' WHERE id_konkursu = $id_konkursu;";
        pg_query($conn, $sql);
        header("Location: ./wprowadz_skok.php");
        exit();
    }
} else if ($seria == "PIERWSZA") {
    $id_zgloszenia_do_skoku = id_zgloszenia_zawodnika_do_skoku($conn, $id_konkursu, 0, 1, 0);
    if ($id_zgloszenia_do_skoku == -1) {
        // Ustawiamy, że konkurs jest teraz w fazie skoków serii drugieh
        $sql = "UPDATE konkurs SET czy_podczas_serii_pierwszej = 'f', czy_podczas_serii_drugiej = 't' WHERE id_konkursu = $id_konkursu;";
        pg_query($conn, $sql);
        header("Location: ./wprowadz_skok.php");
        exit();
    }
} else { // else if ($seria == "DRUGA")
    $id_zgloszenia_do_skoku = id_zgloszenia_zawodnika_do_skoku($conn, $id_konkursu, 0, 0, 1);
    if ($id_zgloszenia_do_skoku == -1) {
        // Ustawiamy, że konkurs jest teraz zakonczony
        $sql = "UPDATE konkurs SET czy_podczas_serii_drugiej = 'f', czy_konkurs_zakonczony = 't' WHERE id_konkursu = $id_konkursu;";
        header("Location: ./ekran_koncowy.php");
        exit();
    }
}

// Skoro mamy id_zgloszenia_do_skoku, to możemy pobrać imie, nazwisko i kraj zawodnika.
$imie = imie_zawodnika($conn, $id_zgloszenia_do_skoku);
$nazwisko = nazwisko_zawodnika($conn, $id_zgloszenia_do_skoku);
$kraj = kraj_zawodnika($conn, $id_zgloszenia_do_skoku);

if (isset($_GET["komunikat"])) {
    $komunikat = $_GET["komunikat"];
    if ($komunikat == "puste") {
        echo "Pola nie mogą być puste!";
    } else if ($komunikat == "nieprawidlowedane") {
        echo "Nieprawidlowe dane!";
    } else if ($komunikat == "skokdodany") {
        echo "Skok został wprowadzony!";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css">
    <title>Document</title>
</head>
<body>
    <div class="navbar" align="center">
        <div class="nav-links">
            <ul>
                <li><a href="org.php">Strona Główna</a></li>
                <?php
                if (isset($_SESSION["login"])) {
                    echo "<li><a href='includes/logout-inc.php'>Wyloguj</a></li>";
                } else {
                    echo "<li><a href='login.php'>Logowanie</a></li>";
                    echo "<li><a href='signup.php'>Tworzenie Konta</a></li>";
                }
                ?>
            </ul>
        </div>
    </div>


<div class="form">
    <fieldset align="center">
        <legend>Dodaj Skok Dla Konkursu <?php echo $nazwa_konkursu ?> w Turze <?php echo $seria ?> <br>
                Dla Zawodnika <?php echo $imie ?> <?php echo $nazwisko ?> z <?php echo $kraj ?> 
                (aby oznaczyć, że zawodnik jest zdyskwalifikowany, wpisz -1 w pole odległość i punkty)
        </legend>
    <form name="dodaj_skok" id="dodaj_skok" method="post" action="dodaj_skok.php?lol=jfdksljfskljfdslk" >
        <input type="hidden" id="imie" name="imie" value="<?= $imie ?>">
        <input type="hidden" id="nazwisko" name="nazwisko" value="<?= $nazwisko ?>">
        <input type="hidden" id="kraj" name="kraj" value="<?= $kraj ?>">
        <input type="hidden" id="id_zgloszenia" name="id_zgloszenia" value="<?= $id_zgloszenia_do_skoku ?>">
            <p>
                <label for="Odległość">Odległość</label>
                <input type="text" name="odleglosc" id="odleglosc">
            </p>
            <p>
                <label for="Punkty">Punkty</label>
                <input type="text" name="punkty" id="punkty">
            </p>
            <p>
                <input type="submit" name="dodaj_skok" value="Dodaje Skok">
            </p>
    </fieldset>
</div>


</body>
</html>