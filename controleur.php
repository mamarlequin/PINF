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

if ($action = valider("action")) {
	ob_start();
	echo "Action = '$action' <br />";
	// ATTENTION : le codage des caractères peut poser PB si on utilise des actions comportant des accents... 
	// A EVITER si on ne maitrise pas ce type de problématiques

	// Un paramètre action a été soumis, on fait le boulot...
	switch ($action) {

		// Connexion //////////////////////////////////////////////////
		case 'Connexion':
			// Vérifie la présence des champs nom, prenom et motdepasse
			if ($nom = valider("nom"))
				if ($prenom = valider("prenom"))
					if ($motdepasse = valider("motdepasse")) {
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


		case 'Logout':
		case 'logout':
			session_destroy();
			$qs = array(
				"view" => "login",
				"msg" => "Déconnexion réussie"
			);
			break;

		case 'Créer le nouvel équipement':
			$nom = valider("nom");
			$type = valider("type");
			$description = valider("description");
			$risque = valider("risques");
			if ($nom && $type && $description && isAdmin($_SESSION["idUser"])) {
				creer_equip($nom, $type, $description, $risque);
				$qs = array("view" => "machines", "msg" => "Création réussie");
			} else $qs = array("view" => "machines", "msg" => "Création échouée");
			break;

		case 'Supprimer Dispo':
			$idCreneau = valider("id_creneau");
			$idUser = $_SESSION["idUser"];

			if ($idCreneau && $idUser) {
				include_once("libs/modele.php");
				supprimer_dispo($idCreneau, $idUser);
				$msg = "Créneau supprimé avec succès.";
			} else {
				$msg = "Erreur : ID de créneau introuvable.";
			}

			header("Location: index.php?view=compte&msg=" . urlencode($msg));
			break;

		case 'Enregistrer Dispo':
			$debut = valider("debut");
			$fin = valider("fin");
			if ($debut && $fin && isAdmin($_SESSION["idUser"])) {
				ajouter_dispo($_SESSION["idUser"], $debut, $fin);
				$qs = array("view" => "admin", "msg" => "Disponibilité enregistrée !");
			}
			break;

		case 'Créer Utilisateur':
			$nom = valider("nom");
			$prenom = valider("prenom");
			$email = valider("email");
			$role = valider("role");

			if ($nom && $prenom && $email) {
				include_once("libs/modele.php");


				$mdp = creer_utilisateur($nom, $prenom, $email, $role);

				if ($mdp) {
					
					include_once("libs/maLibMail.php");
					if (envoyerMailMdp($email, $prenom, $mdp)) {
						$msg = "Succès : Compte créé et mail envoyé à $email !";
					} else {
						$msg = "Compte créé, mais l'envoi du mail a échoué";
					}
				} else {
					$msg = "Erreur lors de la création en base de données.";
				}
			} else {
				$msg = "Veuillez remplir tous les champs.";
			}
			$qs = array("view" => "admin", "msg" => $msg);
			break;

		case 'Changer Maintenance':
			$id = valider("id_equip");
			$etat = valider("etat_actuel");

			if ($id !== false && $etat !== false) {
				include_once("libs/modele.php");
				set_maintenance($id, $etat);
				$msg = "Statut de la machine mis à jour.";
			}
			header("Location: index.php?view=admin&msg=" . urlencode($msg));
			break;

		case 'Ajouter Commentaire':
			$idEquip = valider("id_equip");
			$texte = valider("texte");
			$idUser = $_SESSION["idUser"];

			if ($idEquip && $texte) {
				include_once("libs/modele.php");
				ajouter_commentaire($idEquip, $idUser, $texte);
			}
			header("Location: index.php?view=machines");
			break;

		case 'Modifier Dispo':
			$idCreneau = valider("id_creneau");
			$date = valider("date_jour");
			$hD = valider("heure_debut");
			$hF = valider("heure_fin");

			if ($idCreneau && $date && $hD && $hF) {
				$debut = $date . " " . $hD . ":00";
				$fin = $date . " " . $hF . ":00";

				include_once("libs/modele.php");
				modifier_dispo($idCreneau, $debut, $fin);
			}
			header("Location: index.php?view=compte");
			break;
		case '-':
			if ($id = valider("id")) {
				supp_equip($id);
				$qs = array("view" => "machines", "msg" => "Suppression réussie");
			} else $qs = array("view" => "machines", "msg" => "Suppression échouée");
	}
}

// On redirige toujours vers la page index, mais on ne connait pas le répertoire de base
// On l'extrait donc du chemin du script courant : $_SERVER["PHP_SELF"]
// Par exemple, si $_SERVER["PHP_SELF"] vaut /chat/data.php, dirname($_SERVER["PHP_SELF"]) contient /chat

$urlBase = dirname($_SERVER["PHP_SELF"]) . "/index.php";
// On redirige vers la page index avec les bons arguments

if (empty($qs)) {
    if (isset($_SERVER["HTTP_REFERER"])) {
        $referer_queries = parse_url($_SERVER["HTTP_REFERER"], PHP_URL_QUERY);
        parse_str($referer_queries, $output);
        $view = isset($output['view']) ? $output['view'] : "main";
    } else {
        $view = "main";
    }
    $qs = array("view" => $view);
}

rediriger($urlBase, $qs);

// On écrit seulement après cette entête
ob_end_flush();

function parseDataQS($qs)
{
	global $dataQS;
	$t = explode('=', $qs);
	$dataQS[$t[0]] = $t[1];
}
