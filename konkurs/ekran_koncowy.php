<?php
    include_once './../header.php';
?>

<?php
$nazwa_konkursu = $_SESSION["nazwa_konkursu"];
?>

<h3 align=center> Gratulacje! Dotrwałaś/eś do końca konkursu <?php echo $nazwa_konkursu?></h3>
<p align=center> Wyniki tego jak i ewentualnych innych konkursów można podejrzeć na <a href="./../widz.php">stronie konkursowej</a></p>

<?php
    include_once './../footer.php';
?>