<?php
include_once './includes/dbh-inc.php';
include_once './header.php'
?>

<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = pg_query($conn, "SELECT id_konkursu, nazwa_konkursu, kraj_organizatora FROM konkurs WHERE id_konkursu = $id");
    $row = pg_fetch_array($query);
    echo "<h3> Konkurs ".$row['nazwa_konkursu']." z kraju ".$row['kraj_organizatora'].".</h3>";

    echo "<br><br>";
    echo "<h3> Wyniki: </h3>";
    $sql = "SELECT A.imie, A.nazwisko, C.punkty FROM 
            (SELECT id_zawodnika, imie, nazwisko FROM zawodnik) A 
            FULL OUTER JOIN
            (SELECT id_zgloszenia, id_zawodnika FROM zgloszenie) B on A.id_zawodnika = B.id_zawodnika
            FULL OUTER JOIN
            (SELECT id_zgloszenia, punkty FROM skok) C on B.id_zgloszenia = C.id_zgloszenia
            WHERE B.id_zgloszenia IN (SELECT id_zgloszenia FROM zgloszenie WHERE id_konkursu = $id)";
    $query = pg_query($conn, $sql);
    while ($row = pg_fetch_array($query)) {
        if ($row['punkty'] == NULL) {
            echo "Zawodnik ".$row['imie']." ".$row['nazwisko']." nie zdobył jeszcze punktów.<br><br>\n";
        } else {
            echo "Zawodnik ".$row['imie']." ".$row['nazwisko']." zdobył ".$row['punkty']." punktów.<br><br>\n"; 
        }
    }


}