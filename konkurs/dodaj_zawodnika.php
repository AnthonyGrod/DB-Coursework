<?php

include_once './../includes/dbh-inc.php';
include_once './func-konkurs.php';

$nazwa = $_SESSION['nazwa_konkursu']; // nazwa konkursu dla ktorego dodajemy zawodnika
$kraj_zawodnika = $_POST['kraj_zawodnika']; // kraj zawodnika
$imie = $_POST['imie']; // imie zawodnika
$nazwisko = $_POST['nazwisko']; // nazwisko zawodnika

$id_konkursu = get_id_konkursu($conn, $nazwa);

if (isset($_GET["info"])) {
    $info = $_GET["info"];
    if ($info == "przeskok_z_zawodnikow") {
        // Musimy ustawić, że konkurs nie jest w fazie otwartych zgłoszeń, ale jest w fazie x
        $sql_liczba_zawodnikow = "SELECT id_zgloszenia FROM zgloszenie WHERE id_konkursu = (SELECT id_konkursu FROM konkurs WHERE nazwa_konkursu = $1) AND id_zawodnika = (SELECT id_zawodnika FROM zawodnik WHERE imie = $2 AND nazwisko = $3 AND id_kraju = $4)";
        $result = pg_query_params($conn, $sql_liczba_zawodnikow, array($nazwa, $imie, $nazwisko, $id_kraju_zawodnika));
        $liczba = pg_num_rows($result);
        if ($liczba > 50) {
            $sql_czy_kwalfikacje_otwarte = "UPDATE konkurs SET czy_podczas_kwalfikacji = 't', czy_zgloszenia_otwarte = 'f' WHERE nazwa_konkursu = $1";
            $result = pg_query_params($conn, $sql_czy_kwalfikacje_otwarte, array($nazwa));
            pg_close($conn);
            header("Location: ./wprowadz_skok.php");
            exit();
        } else {
            $sql_seria_pierwsza = "UPDATE konkurs SET czy_podczas_serii_pierwszej = 't', czy_zgloszenia_otwarte = 'f' WHERE nazwa_konkursu = $1";
            $result = pg_query_params($conn, $sql_seria_pierwsza, array($nazwa));
            pg_close($conn);
            header("Location: ./wprowadz_skok.php");
            exit();
        }
    }
}

if (empty($kraj_zawodnika) || empty($imie) || empty($nazwisko)) {
    header("Location: ./wprowadz_zawodnika.php?komunikat=emptyinput");
    exit();
}

if (!is_string($kraj_zawodnika) || !is_string($imie) || !is_string($nazwisko)) {
    header("Location: ./wprowadz_zawodnika.php?komunikat=wronginput");
    exit();
}

$id_kraju_zawodnika = get_id_kraju($conn, $kraj_zawodnika);
$id_konkursu = get_id_konkursu($conn, $nazwa);

// Sprawdzamy, czy kraj zawodnika istnieje
if (czy_kraj_istnieje($conn, $kraj_zawodnika) === false) {
    pg_close($conn);
    header("Location: ./wprowadz_zawodnika.php?komunikat=krajnieistnieje");
    exit();
}

// Sprawdzamy, czy zawodnik o podanych danych już istnieje
if (czy_zawodnik_istnieje($conn, $imie, $nazwisko, $kraj_zawodnika) === true) {
    pg_close($conn);
    header("Location: ./wprowadz_zawodnika.php?komunikat=zawodnikistnieje");
    exit();
}

// Sprawdzamy, czy kraj nie przekroczył limitu zawodników
// $sql_liczba_zawodnikow = "SELECT id_zawodnika FROM zawodnik INNER JOIN zgloszenie z ON zawodnik.id_zawodnika = z.id_zawodnika INNER JOIN konkurs k ON z.id_konkursu = k.id_konkursu
//                           WHERE id_kraju = (SELECT id_kraju FROM kraj WHERE nazwa_kraju = $1)";
$id_kraju = get_id_kraju($conn, $kraj_zawodnika);
$sql_liczba_zawodnikow = "SELECT id_zgloszenia FROM zgloszenie WHERE id_konkursu = $id_konkursu AND id_zawodnika IN (SELECT id_kraju FROM zawodnik WHERE id_kraju = $id_kraju)";
$result = pg_query_params($conn, $sql_liczba_zawodnikow, array());
$aktualna_liczba_zawodnikow_kraju = pg_num_rows($result);
// Liczymy jaka jest kwota startowa kraju zawodnika
$sql_kwota_startowa = "SELECT kwota FROM kwota WHERE id_kraju = (SELECT id_kraju FROM kraj WHERE nazwa_kraju = $1 AND id_konkursu = $id_konkursu)";
$query = pg_query_params($conn, $sql_kwota_startowa, array($kraj_zawodnika));
$result = pg_fetch_all($query);
$kwota_startowa_kraju = $result[0]["kwota"]; // pobieramy kwote
if ($aktualna_liczba_zawodnikow_kraju == $kwota_startowa_kraju) {
    header("Location: ./wprowadz_zawodnika.php?komunikat=przekroczylimityzawodnikow");
    exit();
    // $sql_liczba_zawodnikow = "SELECT id_zgloszenia FROM zgloszenie WHERE id_konkursu = (SELECT id_konkursu FROM konkurs WHERE nazwa_konkursu = $1) AND id_zawodnika = (SELECT id_zawodnika FROM zawodnik WHERE imie = $2 AND nazwisko = $3 AND id_kraju = $4)";
    // $result = pg_query_params($conn, $sql_liczba_zawodnikow, array($nazwa, $imie, $nazwisko, $id_kraju_zawodnika));
    // $liczba = pg_num_rows($result);
    // if ($liczba > 50) {
    //     $sql_czy_kwalfikacje_otwarte = "UPDATE konkurs SET czy_podczas_kwalfikacji = 't' WHERE nazwa_konkursu = $1";
    //     $result = pg_query_params($conn, $sql_czy_kwalfikacje_otwarte, array($nazwa));
    //     pg_close($conn);
    //     header("Location: ./wprowadz_skok.php?tura=kwaflikacyjna&czy_pierwszy_skok_w_serii=tak");
    //     exit();
    // } else {
    //     $sql_seria_pierwsza = "UPDATE konkurs SET czy_podczas_serii_pierwszej = 't' WHERE nazwa_konkursu = $1";
    //     $result = pg_query_params($conn, $sql_seria_pierwsza, array($nazwa));
    //     pg_close($conn);
    //     header("Location: ./wprowadz_skok.php?tura=pierwsza&czy_odrazu=tak&czy_pierwszy_skok_w_serii=tak");
    //     exit();
    // }
}

// Dodajemy zawodnika do tablicy zawodników
$sql_dodaj_zawodnika = "INSERT INTO zawodnik (id_kraju, imie, nazwisko) VALUES ((SELECT id_kraju FROM kraj WHERE nazwa_kraju = $1), $2, $3)";
$result = pg_query_params($conn, $sql_dodaj_zawodnika, array($kraj_zawodnika, $imie, $nazwisko));

// Dodajemy zawodnika do tablicy zgłoszeń
$sql_dodaj_zgloszenie = "INSERT INTO zgloszenie (id_konkursu, id_zawodnika) VALUES ((SELECT id_konkursu FROM konkurs WHERE nazwa_konkursu = $4), (SELECT id_zawodnika FROM zawodnik WHERE imie = $1 AND nazwisko = $2 AND id_kraju = $3))";
$result = pg_query_params($conn, $sql_dodaj_zgloszenie, array($imie, $nazwisko, $id_kraju_zawodnika, $nazwa));

if (isset($_POST['koniec_zawodnikow'])) {
    // Ustaw czy_zgloszenia_otwarte na false
    $sql_czy_zgloszenia_otwarte = "UPDATE konkurs SET czy_zgloszenia_otwarte = false WHERE nazwa_konkursu = $1";
    $result = pg_query_params($conn, $sql_czy_zgloszenia_otwarte, array($nazwa));
    // Obliczamy liczbę zawodników w konkursie
    $sql_liczba_zawodnikow = "SELECT id_zgloszenia FROM zgloszenie WHERE id_konkursu = (SELECT id_konkursu FROM konkurs WHERE nazwa_konkursu = $1) AND id_zawodnika = (SELECT id_zawodnika FROM zawodnik WHERE imie = $2 AND nazwisko = $3 AND id_kraju = $4)";
    $result = pg_query_params($conn, $sql_liczba_zawodnikow, array($nazwa, $imie, $nazwisko, $id_kraju_zawodnika));
    $liczba_zawodnikow = pg_num_rows($result);
    if ($liczba_zawodnikow > 50) {
        // Ustaw czy_kwalfikacje_otwarte na true
        $sql_czy_kwalfikacje_otwarte = "UPDATE konkurs SET czy_podczas_kwalfikacji = 't', czy_zgloszenia_otwarte = 'f' WHERE nazwa_konkursu = $1";
        $result = pg_query_params($conn, $sql_czy_kwalfikacje_otwarte, array($nazwa));
        pg_close($conn);
        header("Location: ./wprowadz_skok.php");
        exit();
    } else {
        // Ustaw czy_podczas_serii_pierwszej na true
        $sql_seria_pierwsza = "UPDATE konkurs SET czy_podczas_serii_pierwszej = 't', czy_zgloszenia_otwarte = 'f' WHERE nazwa_konkursu = $1";
        $result = pg_query_params($conn, $sql_seria_pierwsza, array($nazwa));
        pg_close($conn);
        header("Location: ./wprowadz_skok.php");
        exit();
    }
} else if (isset($_POST['dalej_zawodnikow'])) {
    pg_close($conn);
    header("Location: ./wprowadz_zawodnika.php?komunikat=wprowadzkolejnegozawodnika");
    exit();
}