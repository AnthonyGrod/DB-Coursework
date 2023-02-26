<?php
    include_once './../header.php';
?>

<?php
$nazwa_konkursu = $_POST["nazwa_konkursu"];
$conn = pg_connect("host=lkdb dbname=bd user=ag438477 password=123");
// Patrzymy, czy konkurs o takiej nazwie już istnieje
$sql = "SELECT czy_konkurs_istnieje('$nazwa_konkursu')";
$result = pg_query($conn, $sql);
$resultCheck = pg_num_rows($result);
if ($resultCheck == 0) {
    pg_close($conn);
    header("Location: ./../org.php?komunikat=konkursnieistnieje");
    exit();
}


$_SESSION["nazwa_konkursu"] = $nazwa_konkursu;

header("location: ./wprowadz_kwote.php");
?>


<?php
    include_once './../footer.php';
?>
