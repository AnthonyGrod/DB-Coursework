<?php

include_once './../includes/dbh-inc.php';
include_once './func-konkurs.php';

$nazwa = $_SESSION['nazwa_konkursu']; // nazwa konkursu dla ktorego dodajemy kwote
$kraj = $_SESSION['nazwa_kraj']; // nazwa kraju dla ktorego tworzony jest konkurs
$kwota = $_POST['kwota']; // kwota
$kraj_do_dodania = $_POST['kraj']; // kraj dla ktorego dodajemy kwote

$sql_konkurs_id = "SELECT id_konkursu FROM Konkurs WHERE nazwa_konkursu = $1";
$query = pg_query_params($conn, $sql_konkurs_id, array($nazwa));
$result = pg_fetch_all($query);
$id_konkursu = $result[0]["id_konkursu"]; // pobieramy id konkursu

if (isset($_POST['koniec_bez_kwoty'])) {
    // Ustawiamy, że można już dodawać zawodników
    $sql3 = "UPDATE konkurs SET czy_zgloszenia_otwarte = 't' WHERE id_konkursu = '$id_konkursu'";
    $result3 = pg_query($conn, $sql3);
    pg_close($conn);

    header("Location: ./wprowadz_zawodnika.php");
    exit();
  }

if (emptyInput($nazwa) || emptyInput($kraj) || emptyInput($kwota) || emptyInput($kraj_do_dodania)) {
    header("location: ./wprowadz_kwote.php?komunikat=emptyinput");
    exit();
}

if (!is_numeric($kwota) || !is_string($kraj) || !is_string($nazwa) || !is_string($kraj_do_dodania)) {
    header("location: ./wprowadz_kwote.php?komunikat=wronginput");
    exit();
}

if ($_GET['komunikat'] == "emptyinput") {
    echo "<p>Wypełnij wszystkie pola!</p>";
}

$sql_kraj_id = "SELECT id_kraju FROM Kraj WHERE nazwa_kraju = $1";
$result = pg_query_params($conn, $sql_kraj_id, array($kraj_do_dodania));
$resultCheck = pg_num_rows($result);
if ($resultCheck == 0) { // dodajemy kraj jeśli nie istnieje w bazie
    $sql = "INSERT INTO kraj (nazwa_kraju) VALUES ($1)";
    pg_query_params($conn, $sql, array($kraj_do_dodania));
}

$query = pg_query_params($conn, $sql_kraj_id, array($kraj_do_dodania));
$result = pg_fetch_all($query);
$id_kraju = $result[0]["id_kraju"]; // pobieramy id kraju

$sql = "SELECT * FROM kwota WHERE id_kraju = '$id_kraju' AND id_konkursu = '$id_konkursu'"; // sprawdzamy, czy dla danego kraju zostala juz wprowadzona kwota w tym konkursie
$result = pg_query($conn, $sql);
$resultCheck = pg_num_rows($result);

if ($resultCheck != 0 && isset($_POST['koniec'])) {
    pg_close($conn);
    header("Location: ./wprowadz_zawodnika.php?komunikat=kwotaistnieje");
    exit();
} else if ($resultCheck != 0 && isset($_POST['dalej'])) {
    pg_close($conn);
    header("Location: ./wprowadz_kwote.php?komunikat=kwotaistnieje");
    exit();
}

$sql2 = "INSERT INTO kwota (id_kraju, id_konkursu, kwota) VALUES ($1, $2, $3)";
pg_query_params($conn, $sql2, array($id_kraju, $id_konkursu, $kwota));


if (isset($_POST['koniec'])) {
    // Ustawiamy, że można już dodawać zawodników
    $sql3 = "UPDATE konkurs SET czy_zgloszenia_otwarte = 't' WHERE id_konkursu = '$id_konkursu'";
    $result3 = pg_query($conn, $sql3);
    pg_close($conn);

    header("Location: ./wprowadz_zawodnika.php");
    exit();
  } else {
    pg_close($conn);
    header("Location: ./wprowadz_kwote.php?komunikat=wprowadzkolejnykraj");
    exit();
  }

?>