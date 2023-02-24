<?php
include_once './includes/dbh-inc.php';
include_once './header.php'
?>

<?php
if (isset($_GET['id'])) {
    // Wypisz wszystkie skoki zawodnika o danym id
    $id = $_GET['id'];
    $sql = "SELECT A.imie, A.nazwisko, C.punkty, D.nazwa_konkursu FROM 
            (SELECT id_zawodnika, imie, nazwisko FROM zawodnik WHERE id_zawodnika = $id) A 
            FULL OUTER JOIN
            (SELECT id_zgloszenia, id_zawodnika, id_konkursu FROM zgloszenie) B on A.id_zawodnika = B.id_zawodnika
            FULL OUTER JOIN
            (SELECT id_zgloszenia, punkty FROM skok) C on B.id_zgloszenia = C.id_zgloszenia
            FULL OUTER JOIN
            (SELECT id_konkursu, nazwa_konkursu FROM konkurs) D on B.id_konkursu = D.id_konkursu
            WHERE B.id_zgloszenia IN (SELECT id_zgloszenia FROM zgloszenie WHERE id_zawodnika = $id)";

    $query = pg_query($conn, $sql);
    while ($row = pg_fetch_array($query)) {
        echo "Zawodnik ".$row['imie']." ".$row['nazwisko']." zdobył ".$row['punkty']." punktów w konkursie ".$row['nazwa_konkursu'].".<br><br>\n";
    }
}
?>