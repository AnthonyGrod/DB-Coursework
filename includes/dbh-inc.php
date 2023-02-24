<?php
// if(!isset($_COOKIE["PHPSESSID"]))
// {
  session_start();
// }
// // Zapisanie loginu i hasla w ciasteczku sesyjnym.
// $_SESSION['LOGIN'] = $_POST['nazwa'];
// $_SESSION['PASS'] = $_POST['pasw'];

// Utworzenie polaczenia z baza danych zawodnicy.
$conn = pg_connect("host=lkdb dbname=bd user=ag438477 password=030757Jg");
// Sprawdzenie poprawnosci polaczenia z baza danych zawody.
if (!$conn) {
    echo "Wystąpił błąd podczas łączneia się z bazą.\n";
    exit;
}