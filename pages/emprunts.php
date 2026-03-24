<?php
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
    header("Location:../index.php?view=emprunts");
    die("");
}

if (!isset($_SESSION["idUser"])) {
    header("Location:../index.php?view=login");
    die("");
}

?>