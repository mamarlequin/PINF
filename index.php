<?php
    session_start();

	include_once "libs/maLibUtils.php";
	include_once "libs/maLibBootstrap.php";
	include_once "libs/modele.php";
	include_once "libs/maLibForms.php";

    // on récupère le paramètre view éventuel 
	$view = valider("view");

    // S'il est vide, on charge la vue d'accueil par défaut
	if (!$view) $view = "main";

    include("./pages/header.php");

    // En fonction de la vue à afficher, on appelle tel ou tel pages
	switch($view)
	{		
		case "main" : 
			include("./pages/main.php");
		break;

		default : // si le template correspondant à l'argument existe, on l'affiche
			if (file_exists("pages/$view.php"))
				include("pages/$view.php");
	}
?>