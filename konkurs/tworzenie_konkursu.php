<?php
$kraj = $_POST["kraj"];
$termin = $_POST["termin"];
$nazwa = $_POST["nazwa_konkursu"];
$kwota = $_POST["kwota"];

require_once './../includes/dbh-inc.php';
require_once './../konkurs/func-konkurs.php';

if (emptyInput($nazwa) || emptyInput($termin) || emptyInput($kraj) || emptyInput($kwota)) {
    header("Location: ../org.php?komunikat=emptyinput");
    exit();
}

if (!is_string($kraj) || !is_string($nazwa) || !is_numeric($kwota)) {
    header("Location: ../org.php?komunikat=wronginput");
    exit();
}

$_SESSION['nazwa_konkursu'] = $nazwa;
$_SESSION['nazwa_kraj'] = $kraj;

if ($conn === false) {
    echo 'Connection status not ok';
    return false;
}

// patrzymy, czy kraj organizatora już istnieje w bazie
$sql = "SELECT * FROM kraj WHERE nazwa_kraju = '$kraj'";
$result = pg_query($conn, $sql);
$resultCheck = pg_num_rows($result);
if ($resultCheck == 0) { // to znaczy, że nie istnieje wiec dodajemy go do bazy
    $sql_kraj = "INSERT INTO Kraj (nazwa_kraju) VALUES ($1)";
    pg_query_params($conn, $sql_kraj, array($kraj)); // dodawanie kraju organizatora do tabeli krajów
}

// Patrzymy, czy konkurs o takiej nazwie już istnieje
$sql = "SELECT czy_konkurs_istnieje('$nazwa')";
$result = pg_query($conn, $sql);
$resultCheck = pg_num_rows($result);
if ($resultCheck == 0) {
    header("Location: ./../org.php?komunikat=konkursistnieje");
    exit();
}

$sql_num_rows_before = "SELECT id_konkurs FROM konkurs;";
$result = pg_query($conn, $sql);
$num_before = pg_num_rows($result);

$sql = "INSERT INTO Konkurs (nazwa_konkursu, termin_zapisow, czy_zgloszenia_otwarte, czy_podczas_kwalfikacji, 
czy_podczas_serii_pierwszej, czy_podczas_serii_drugiej, czy_konkurs_zakonczony, kraj_organizatora) VALUES ($1, $2, $3, $4, $5, $6, $7, $8)";
pg_query_params($conn, $sql, array($nazwa, $termin, 0, 0, 0, 0, 0, $kraj)); // dodawanie konkursu


$sql_konkurs_id = "SELECT id_konkursu FROM Konkurs WHERE nazwa_konkursu = $1";
$query = pg_query_params($conn, $sql_konkurs_id, array($nazwa));
$result = pg_fetch_all($query);
$id_konkursu = $result[0]["id_konkursu"]; // pobieramy id konkursu, który właśnie dodaliśmy

if ($id_konkursu == false) { // To oznacza, że nasz trigger zadziałał
    header("Location: ./../org.php?komunikat=zlanazwa");
    exit();
}

$sql_kraj_id = "SELECT id_kraju FROM Kraj WHERE nazwa_kraju = $1";
$query = pg_query_params($conn, $sql_kraj_id, array($kraj));
$result = pg_fetch_all($query);
$id_kraju = $result[0]["id_kraju"]; // pobieramy id kraju, który właśnie dodaliśmy (lub nie jeśli już istniał)

$sql2 = "INSERT INTO Kwota (id_kraju, id_konkursu, kwota) VALUES ($1, $2, $3)"; // dodawanie kwoty organizatora
if ($sql2 === false) {
    echo 'Query error';
    return false;
}
pg_query_params($conn, $sql2, array($id_kraju, $id_konkursu, $kwota));
pg_close($conn);
header("Location: ./wprowadz_kwote.php");

?>