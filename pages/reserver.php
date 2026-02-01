<?php
// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
	header("Location:../index.php?view=reserver");
	die("");
}

// // Définit le fuseau horaire par défaut à utiliser.
// date_default_timezone_set('UTC');

// // Affichage de quelque chose comme : Monday 8th of August 2005 03:12:46 PM
// echo date('l jS \of F Y h:i:s A',$_SERVER['REQUEST_TIME']). "\n";

?>
<!-- <div class="header"><input type="date"></div> -->

<div class="wrapper">
    <header>
        <p class="date"></p>
        <div class="icons">
            <span id="prev"><</span>
            <span id="next">></span>
        </div>
    </header>
    <div class="calendrier">
        <ul class="semaines">
            <li>Lundi</li>
            <li>Mardi</li>
            <li>Mercredi</li>
            <li>Jeudi</li>
            <li>Vendredi</li>
            <li>Samedi</li>
            <li>Dimanche</li>
            <ul class="jours"></ul>
        </ul>
    </div>
</div>

