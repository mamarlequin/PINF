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
		$sql = "SELECT id FROM user WHERE id = ? AND admin = '1'";
		$stmt = $dbh->prepare($sql);
		$stmt->execute([$idUser]);

		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result ? $result['id'] : false;
	} catch (PDOException $e) {
		die("<font color=\"red\">isAdmin: Erreur de connexion : " . $e->getMessage() . "</font>");
	}
}

function inscription($nom, $prenom, $passe, $promo)
{
	$SQL = "INSERT INTO user (nom, prenom, promo, admin, mdp) VALUES ('$nom', '$prenom', '$promo', '0', '$passe')";
	SQLInsert($SQL);
}



?>