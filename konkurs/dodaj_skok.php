<?php
include_once "./func-konkurs.php";

session_start();
$conn = pg_connect("host=lkdb dbname=bd user=ag438477 password=123");
// Sprawdzenie poprawnosci polaczenia z baza danych zawody.
if (!$conn) {
    echo "Wystąpił błąd podczas łączneia się z bazą.\n";
    exit;
}
?>
<?php
$nazwa_konkursu = $_SESSION["nazwa_konkursu"];
$imie = $_POST["imie"];
$nazwisko = $_POST["nazwisko"];
$kraj = $_POST["kraj"];
$odleglosc = $_POST["odleglosc"];
$punkty = $_POST["punkty"];
if ($odleglosc == -1) {
    $czy_zdyskwalfikowany = 1;
} else {
    $czy_zdyskwalfikowany = 0;
}
$id_zgloszenia = $_POST["id_zgloszenia"];
$id_konkursu = get_id_konkursu($conn, $nazwa_konkursu);

if (emptyInput($odleglosc) || emptyInput($punkty)) {
    header("location: ./wprowadz_skok.php?komunikat=puste");
    exit();
}

if (!is_numeric($_POST["odleglosc"]) || !is_numeric($_POST["punkty"])) {
    header("Location: ../konkurs/wprowadz_skok.php?komunikat=nieprawidlowedane");
    exit();
}

// Ustalamy jaka jest teraz seria
$seria = jaka_seria($conn, $id_konkursu);
// Ustalamy, jakie jest id_zawodnika
if ($seria == "KWALFIKACYJNA" || ($seria == "PIERWSZA" && czy_pierwsza_odrazu($conn, $id_konkursu))) {
    // Dodajemy skok do bazy danych
    if ($seria == "KWALFIKACYJNA") {
        if ($czy_zdyskwalfikowany == 1) {
            $sql = "INSERT INTO skok (odleglosc, punkty, czy_seria_kwalfikacyjna, czy_seria_pierwsza, czy_seria_druga, id_zgloszenia,
            czy_zdyskwalifikowany) VALUES ($odleglosc, $punkty, 't', 'f', 'f', $id_zgloszenia, 't');";
        } else {
            $sql = "INSERT INTO skok (odleglosc, punkty, czy_seria_kwalfikacyjna, czy_seria_pierwsza, czy_seria_druga, id_zgloszenia,
            czy_zdyskwalifikowany) VALUES ($odleglosc, $punkty, 't', 'f', 'f', $id_zgloszenia, 'f');";
        }
        
    } else {
        if ($czy_zdyskwalfikowany == 1) {
            $sql = "INSERT INTO skok (odleglosc, punkty, czy_seria_kwalfikacyjna, czy_seria_pierwsza, czy_seria_druga, id_zgloszenia,
            czy_zdyskwalifikowany) VALUES ($odleglosc, $punkty, 'f', 't', 'f', $id_zgloszenia, 't');";
        } else {
            $sql = "INSERT INTO skok (odleglosc, punkty, czy_seria_kwalfikacyjna, czy_seria_pierwsza, czy_seria_druga, id_zgloszenia,
            czy_zdyskwalifikowany) VALUES ($odleglosc, $punkty, 'f', 't', 'f', $id_zgloszenia, 'f');";
        }

    }
    $result = pg_query($conn, $sql);
} else if ($seria == "PIERWSZA" && !czy_pierwsza_odrazu($conn, $id_konkursu)) {
    // Pobieramy numer startowy zawodnika z serii kwalfikacyjnej
    $numer_startowy = numer_startowy_z_danej_serii($conn, $id_zgloszenia, 1, 0, 0, $id_konkursu);
    // Dodajemy skok do bazy danych
    if ($czy_zdyskwalfikowany == 1) {
        $sql = "INSERT INTO skok (odleglosc, punkty, czy_seria_kwalfikacyjna, czy_seria_pierwsza, czy_seria_druga, id_zgloszenia,
        czy_zdyskwalifikowany, numer_startowy) VALUES ($odleglosc, $punkty, 'f', 't', 'f', $id_zgloszenia, 't', $numer_startowy);";
    } else {
        $sql = "INSERT INTO skok (odleglosc, punkty, czy_seria_kwalfikacyjna, czy_seria_pierwsza, czy_seria_druga, id_zgloszenia,
        czy_zdyskwalifikowany, numer_startowy) VALUES ($odleglosc, $punkty, 'f', 't', 'f', $id_zgloszenia, 'f', $numer_startowy);";
    }
    $result = pg_query($conn, $sql);
} else if ($seria == "DRUGA") {
    // Pobieramy numer startowy zawodnika z serii pierwszej
    $numer_startowy = numer_startowy_z_danej_serii($conn, $id_zgloszenia, 0, 1, 0, $id_konkursu);
    // Dodajemy skok do bazy danych
    if ($czy_zdyskwalfikowany == 1) {
        $sql = "INSERT INTO skok (odleglosc, punkty, czy_seria_kwalfikacyjna, czy_seria_pierwsza, czy_seria_druga, id_zgloszenia,
        czy_zdyskwalifikowany, numer_startowy) VALUES ($odleglosc, $punkty, 'f', 'f', 't', $id_zgloszenia, 't', $numer_startowy);";
    } else {
        $sql = "INSERT INTO skok (odleglosc, punkty, czy_seria_kwalfikacyjna, czy_seria_pierwsza, czy_seria_druga, id_zgloszenia,
        czy_zdyskwalifikowany, numer_startowy) VALUES ($odleglosc, $punkty, 'f', 'f', 't', $id_zgloszenia, 'f', $numer_startowy);";
    }
    $result = pg_query($conn, $sql);
}

header("location: ./wprowadz_skok.php");
exit();
?>
