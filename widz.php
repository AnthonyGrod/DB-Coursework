<?php
include_once 'header.php';
include_once './includes/dbh-inc.php';
?>

<h3> Możliwe konkursy do obejrzenia to: </h3>
<?php
$query = pg_query($conn, "SELECT id_konkursu, nazwa_konkursu, kraj_organizatora FROM konkurs ORDER BY nazwa_konkursu ASC");
while ($row = pg_fetch_array($query)) {
    echo "Obejrzyj konkurs ".$row['nazwa_konkursu']." z kraju ".$row['kraj_organizatora'].": <a href=\"wyswietl_konkurs.php?id=".$row['id_konkursu']."\">". $row[1] ."</a><br>\n";
}
?>
<br><br>

<h3> Możliwi zawodnicy do obejrzenia to: </h3>
<?php
$query = pg_query($conn, "SELECT id_zawodnika, imie, nazwisko, id_kraju FROM zawodnik ORDER BY imie ASC");
$click = "Przejdz do zawodnika";
while ($row = pg_fetch_array($query)) {
    echo "Obejrzyj zawodnika ".$row['imie']." ".$row['nazwisko'].": <a href=\"wyswietl_zawodnika.php?id=".$row['id_zawodnika']."\">". $click ."</a><br><br>\n";
}
?>

<?php
include_once 'footer.php';
?>