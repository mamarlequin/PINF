<?php

include_once("maLibSQL.pdo.php");

function verifUserbdd($nom, $prenom, $motdepasse)
{
	global $BDD_host;
	global $BDD_base;
	global $BDD_user;
	global $BDD_password;

	try {
		$dbh = new PDO("mysql:host=$BDD_host;dbname=$BDD_base", $BDD_user, $BDD_password);
		$dbh->exec("SET CHARACTER SET utf8");

		$sql = "SELECT id, motDePasse 
                FROM Utilisateur 
                WHERE nom = ? AND prenom = ?";

		$stmt = $dbh->prepare($sql);
		$stmt->execute([$nom, $prenom]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);


		if ($result && $motdepasse === $result['motDePasse']) {
			return $result['id'];
		} else {
			return false;
		}
	} catch (PDOException $e) {
		die("<font color=\"red\">verifUserBddNomPrenom: Erreur de connexion : " . $e->getMessage() . "</font>");
	}
}

function isAdmin($idUser)
{
	// Vérifie si l'utilisateur est un administrateur
	global $BDD_host;
	global $BDD_base;
	global $BDD_user;
	global $BDD_password;

	try {
		$dbh = new PDO("mysql:host=$BDD_host;dbname=$BDD_base", $BDD_user, $BDD_password);
		$dbh->exec("SET CHARACTER SET utf8");

		// Utilisation d'une requête préparée pour éviter les injections SQL
		$sql = "SELECT id FROM Utilisateur WHERE id = ? AND (role = '1' OR role = '2')";
		$stmt = $dbh->prepare($sql);
		$stmt->execute([$idUser]);

		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result ? $result['id'] : false;
	} catch (PDOException $e) {
		die("<font color=\"red\">isAdmin: Erreur de connexion : " . $e->getMessage() . "</font>");
	}
}

function isSuperAdmin($idUser)
{
	
	global $BDD_host;
	global $BDD_base;
	global $BDD_user;
	global $BDD_password;

	try {
		$dbh = new PDO("mysql:host=$BDD_host;dbname=$BDD_base", $BDD_user, $BDD_password);
		$dbh->exec("SET CHARACTER SET utf8");

		
		$sql = "SELECT id FROM Utilisateur WHERE id = ? AND (role = '2')";
		$stmt = $dbh->prepare($sql);
		$stmt->execute([$idUser]);

		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result ? $result['id'] : false;
	} catch (PDOException $e) {
		die("<font color=\"red\">isSuperAdmin: Erreur de connexion : " . $e->getMessage() . "</font>");
	}
}

function lister_machine()
{
	$SQL = "SELECT * FROM Equipement";
	return parcoursRs(SQLSelect($SQL));
}

function creer_equip($nom, $type, $description, $risque)
{
	if (isset($risque)) {
		$risque = addslashes($risque);
	} else {
		$risque = null;
	}
	$SQL = "INSERT INTO Equipement (nom, type, enMaintenance, description, risque) VALUES ('$nom', '$type', 0, '$description', '$risque')";
	SQLInsert($SQL);
}

function supp_equip($id)
{
	$SQL = "DELETE FROM Equipement WHERE id = '$id'";
	SQLDelete($SQL);
}


function recherche_machine($mot)
{
	$SQL = "SELECT * FROM Equipement WHERE nom NOT LIKE '%" . $mot . "%'";
	return parcoursRs(SQLSelect($SQL));
}

function dispa($mot)
{
	$SQL = "SELECT * FROM Equipement WHERE nom LIKE '%" . $mot . "%'";
	return parcoursRs(SQLSelect($SQL));
}

function listercom($id)
{
	$SQL = "SELECT * FROM Commentaire JOIN Equipement 
	ON Commentaire.idEquipement = Equipement.id 
	JOIN Utilisateur
	ON Commentaire.idUser = Utilisateur.id
	WHERE Equipement.id = '$id'";
	return parcoursRs(SQLSelect($SQL));
}


function lister_dispo($debut, $fin)
{
	$sql = "SELECT c.idAdmin, c.dateDebut, c.dateFin, u.prenom FROM Creneau c JOIN Utilisateur u ON c.idAdmin = u.id WHERE c.dateDebut < '$fin' AND c.dateFin > '$debut' ORDER BY c.dateDebut ASC";

	$resultats = parcoursRs(SQLSelect($sql));

	$planningAdmin = [];

	foreach ($resultats as $ligne) {
		$dateCle = date('Y-m-d', strtotime($ligne['dateDebut']));

		$creneau = [
			"debut"   => date('H:i', strtotime($ligne['dateDebut'])),
			"fin"     => date('H:i', strtotime($ligne['dateFin'])),
			"idAdmin" => (int)$ligne['idAdmin'],
			"prenom"  => $ligne['prenom'] // Ajout du prénom récupéré par la jointure
		];
		$planningAdmin[$dateCle][] = $creneau;
	}

	return $planningAdmin;
}

function lister_res($debut, $fin)
{
	$SQL = "SELECT id, idEquipement, dateDebut, dateFin, idUser 
	FROM Reservation 
	WHERE (dateDebut <= '$fin' OR dateFin >= '$debut')
	ORDER BY dateDebut ASC;";
	$resultats = parcoursRs(SQLSelect($SQL));

	$planning = array();

	foreach ($resultats as $res) {
		$jour = date('Y-m-d', strtotime($res['dateDebut']));

		$idEq = $res['idEquipement'];

		$heureDebut = date('H:i', strtotime($res['dateDebut']));
		$heureFin = date('H:i', strtotime($res['dateFin']));

		if (!isset($planning[$idEq])) {
			$planning[$idEq] = array();
		}

		$planning[$idEq][] = array(
			"id" => $res['id'],
			"dateDebut" => $res['dateDebut'],
			"dateFin" => $res['dateFin'],
			"debut" => $heureDebut,
			"fin" => $heureFin,
			"idUser" => $res['idUser']
		);
	}
	return $planning;
}



function lister_emprunts($debut, $fin)
{
	$SQL = "SELECT id, idUser, idEquipement, dateDebut, dateRenduTheorique, dateRenduReel
            FROM Emprunt
			WHERE (dateDebut <= '$fin' AND dateRenduTheorique >= '$debut')
            ORDER BY dateDebut DESC";

	$resultats = parcoursRs(SQLSelect($SQL));
	$planning = [];

	foreach ($resultats as $ligne) {
		$idEq = $ligne['idEquipement'];

		$dateFin = ($ligne['dateRenduReel'] !== null)
			? $ligne['dateRenduReel']
			: $ligne['dateRenduTheorique'];

		if (!isset($planning[$idEq])) {
			$planning[$idEq] = array();
		}

		$planning[$idEq][] = array(
			"id" => $ligne['id'],
			"dateDebut" => $ligne['dateDebut'],
			"dateFin" => $dateFin,
			"heureDebut" => date('H:i', strtotime($ligne['dateDebut'])),
			"heureFin" => date('H:i', strtotime($dateFin)),
			"idUser" => $ligne['idUser'],
			"statut" => ($ligne['dateRenduReel'] !== null) ? "rendu" : "en cours"
		);
	}

	return $planning;
}


function set_maintenance($id, $etatActuel)
{
	$nouvelEtat = ($etatActuel == 0) ? 1 : 0;
	$SQL = "UPDATE Equipement SET enMaintenance = '$nouvelEtat' WHERE id = '$id'";
	return SQLUpdate($SQL);
}

function ajouter_dispo($idAdmin, $dateDebut, $dateFin)
{
	$SQL = "INSERT INTO Creneau (idAdmin, dateDebut, dateFin) 
            VALUES ('$idAdmin', '$dateDebut', '$dateFin')";
	return SQLInsert($SQL);
}
function lister_mes_dispos($idAdmin)
{
	$SQL = "SELECT * FROM Creneau WHERE idAdmin = '$idAdmin' ORDER BY dateDebut DESC";
	return parcoursRs(SQLSelect($SQL));
}

function supprimer_dispo($idCreneau, $idAdmin)
{
	$SQL = "DELETE FROM Creneau WHERE id = '$idCreneau' AND idAdmin = '$idAdmin'";
	return SQLDelete($SQL);
}

function modifier_dispo($idCreneau, $dateDebut, $dateFin)
{
	$SQL = "UPDATE Creneau 
            SET dateDebut = '$dateDebut', dateFin = '$dateFin' 
            WHERE id = '$idCreneau'";
	return SQLUpdate($SQL);
}

function creer_utilisateur($nom, $prenom, $email, $role)
{

	$mdp = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);


	$SQL = "INSERT INTO Utilisateur (nom, prenom, adresseMail, motDePasse, role, promotion) 
            VALUES ('$nom', '$prenom', '$email', '$mdp', '$role', 2026)";

	$id = SQLInsert($SQL);


	if ($id) return $mdp;
	return false;
}

function ajouter_commentaire($idEquip, $idUser, $texte)
{
	$texte = addslashes($texte);

	$SQL = "INSERT INTO Commentaire (idEquipement, idUser, contenu, resolu, idReservation) 
            VALUES ('$idEquip', '$idUser', '$texte', 0, NULL)";

	return SQLInsert($SQL);
}
function lister_com($idMachine)
{
	$idMachine = (int)$idMachine;

	$SQL = "
    SELECT 
        Utilisateur.prenom,
        Utilisateur.nom,
        Commentaire.*,
        Reservation.dateDebut
    FROM Commentaire
    JOIN Utilisateur ON Commentaire.idUser = Utilisateur.id
    LEFT JOIN Reservation ON Commentaire.idReservation = Reservation.id
    WHERE Commentaire.idEquipement = $idMachine
    ORDER BY Commentaire.id DESC
    ";

	return parcoursRs(SQLSelect($SQL));
}

function marquer_resolu($id)
{
	$SQL = "UPDATE Commentaire SET resolu=1 WHERE id=$id";
	return SQLUpdate($SQL);
}

function supprimer_commentaire($idCom)
{
	$idCom = (int)$idCom;
	$SQL = "DELETE FROM Commentaire WHERE id = $idCom";
	return SQLDelete($SQL);
}

function marquer_non_resolu($id)
{
	$SQL = "UPDATE Commentaire SET resolu=0 WHERE id=$id";
	return SQLUpdate($SQL);
}
function getUtilisateursGestion()
{
    $monId = intval($_SESSION["idUser"]);
    $SQL = "SELECT id, nom, prenom, role FROM Utilisateur WHERE id != $monId";
    return SQLSelect($SQL);
}

function update_role($idUser, $nouveauRole)
{
	$idUser = intval($idUser);
	$nouveauRole = intval($nouveauRole);
	$SQL = "UPDATE Utilisateur SET role = $nouveauRole WHERE id = $idUser";
	return SQLUpdate($SQL);
}
function deleguerSuperAdmin($idCible, $dateFin)
{
    $idCible = intval($idCible);
    $monId = intval($_SESSION["idUser"]);
    
   
    $dateFin = addslashes($dateFin);

  
    $sql1 = "UPDATE Utilisateur SET role = 2, dateFinRole = '$dateFin' WHERE id = $idCible";
    SQLUpdate($sql1);

    
    $sql2 = "UPDATE Utilisateur SET role = 1 WHERE id = $monId";
    SQLUpdate($sql2);

    $_SESSION["role"] = 1;
}