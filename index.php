<?php
session_start();

include_once "libs/maLibUtils.php";
include_once "libs/maLibBootstrap.php";
include_once "libs/modele.php";
include_once "libs/maLibForms.php";


$sql = "SELECT id FROM Utilisateur WHERE role = 2 AND dateFinRole < NOW()";
$expire = SQLSelect($sql);


if (is_array($expire) && count($expire) > 0) {
	foreach ($expire as $user) {
		$idTemp = $user['id'];


		SQLUpdate("UPDATE Utilisateur SET role = 1, dateFinRole = NULL WHERE id = $idTemp");


		SQLUpdate("UPDATE Utilisateur SET role = 2 WHERE role = 1 AND id != $idTemp AND dateFinRole IS NULL LIMIT 1");


		$msg = "Votre période de délégation est terminée. Vous êtes redevenu Admin.";
		SQLInsert("INSERT INTO Notification (idUser, contenu) VALUES ($idTemp, '$msg')");
	}
}

// on récupère le paramètre view éventuel 
$view = valider("view");

// S'il est vide, on charge la vue d'accueil par défaut
if (!$view) $view = "main";

include("./pages/header.php");

// En fonction de la vue à afficher, on appelle tel ou tel pages
switch ($view) {
	case "main":
		include("./pages/main.php");
		break;

	case "superadmin":
		include("./pages/superadmin.php");
		break;

	default: // si le template correspondant à l'argument existe, on l'affiche
		if (file_exists("pages/$view.php"))
			include("pages/$view.php");
}
