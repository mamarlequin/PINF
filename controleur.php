<?php
session_start();

	include_once "libs/maLibUtils.php";
	include_once "libs/modele.php"; 
	include_once "libs/maLibSecurisation.php"; 
	// cf. injection de dépendances 


	$qs = "";
	$dataQS = array(); 
	
	// voir les entetes HTTP venant du client : 
	// tprint($_SERVER);
	// die("");

	if ($action = valider("action"))
	{
		ob_start ();
		echo "Action = '$action' <br />";
		// ATTENTION : le codage des caractères peut poser PB si on utilise des actions comportant des accents... 
		// A EVITER si on ne maitrise pas ce type de problématiques

		// Un paramètre action a été soumis, on fait le boulot...
		switch($action)
		{
			
			// Connexion //////////////////////////////////////////////////
case 'Connexion':
    // Vérifie la présence des champs nom, prenom et motdepasse
    if ($nom = valider("nom"))
    if ($prenom = valider("prenom"))
    if ($motdepasse = valider("motdepasse"))
    {
        // Vérifie l'utilisateur (fonction à adapter dans ta lib de sécurisation)
		$tab = verifUser($nom, $prenom, $motdepasse);
        if ($tab) {

            // Si on veut se souvenir de la personne
            if (valider("remember")) {
                // Génération d'un token sécurisé pour le cookie
                $token = bin2hex(random_bytes(32)); // Génère un token aléatoire
                $expiry = time() + 60 * 60 * 24 * 30; // 30 jours

                // Stockage du token en base de données (à implémenter)
                // storeRememberToken($_SESSION["idUser"], $token, $expiry);

                // Stockage dans les cookies
                setcookie("nom", $nom, $expiry, "/", "", false, true); // HttpOnly
                setcookie("prenom", $prenom, $expiry, "/", "", false, true);
                setcookie("remember_token", $token, $expiry, "/", "", false, true);
                setcookie("remember", true, $expiry, "/", "", false, false);
            } else {
                // Suppression des cookies si "se souvenir" non coché
                setcookie("nom", "", time() - 3600, "/", "", false, true);
                setcookie("prenom", "", time() - 3600, "/", "", false, true);
                setcookie("remember_token", "", time() - 3600, "/", "", false, true);
                setcookie("remember", false, time() - 3600, "/", "", false, false);

                // Suppression du token en base (si tu implémentes la table remember)
                // deleteRememberToken($_SESSION["idUser"]);
            }

            // Redirection vers la vue d'accueil
            $qs = array(
                "view" => "main",
                "msg"  => "Connexion réussie, bienvenue $prenom $nom !"
            );
        } else {
            // Mauvaise combinaison nom/prénom/mot de passe
            $qs = array(
                "view" => "login",
                "msg"  => "Erreur de connexion, veuillez réessayer"
            );
        }
    }
break;


			case 'Logout' :
			case 'logout' :
				session_destroy();
				$qs = array("view" => "login",
				            "msg" => "Déconnexion réussie");
			break;
			
			case 'Créer le nouvel équipement' : 
				$nom = valider("nom");
				$type = valider("type");
				$description = valider("description");
				$risque = valider("risques");
				if ($nom && $type && $description && $risque && isAdmin($_SESSION["idUser"])){
					creer_equip($nom, $type, $description, $risque);
					$qs = array("view"=> "machines", "msg" => "Création réussie");
				}
				else $qs = array("view"=> "machines", "msg" => "Création échouée");
				break;

			case '-' : 
				if ($id = valider("id")){
					supp_equip($id);
					$qs = array("view"=> "machines", "msg" => "Suppression réussie");
				}
				else $qs = array("view"=> "machines", "msg" => "Suppression échouée");
		}
	}

	// On redirige toujours vers la page index, mais on ne connait pas le répertoire de base
	// On l'extrait donc du chemin du script courant : $_SERVER["PHP_SELF"]
	// Par exemple, si $_SERVER["PHP_SELF"] vaut /chat/data.php, dirname($_SERVER["PHP_SELF"]) contient /chat

	$urlBase = dirname($_SERVER["PHP_SELF"]) . "/index.php";
	// On redirige vers la page index avec les bons arguments
	
	if ($qs == "") {
		// On renvoie vers la page précédente en se servant de HTTP_REFERER
		// attention : il peut y avoir des champs en + de view...
		$qs = parse_url($_SERVER["HTTP_REFERER"]. "&cle=val", PHP_URL_QUERY);
		$tabQS = explode('&', $qs);
		array_map('parseDataQS', $tabQS);
		$qs = "?view=" . $dataQS["view"];
	}

	rediriger($urlBase, $qs);

	// On écrit seulement après cette entête
	ob_end_flush();

	function parseDataQS($qs) {
		global $dataQS; 
		$t = explode('=',$qs);
		$dataQS[$t[0]]=$t[1]; 
	}
	
?>