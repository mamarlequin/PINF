<?php
session_start();

include_once "libs/maLibUtils.php";
include_once "libs/maLibBootstrap.php";
include_once "libs/modele.php";
include_once "libs/maLibForms.php";

$sql = "SELECT id FROM Utilisateur WHERE role = 2 AND dateFinRole IS NOT NULL AND dateFinRole < NOW()";
$expire = SQLSelect($sql);


$expireArray = is_object($expire) ? $expire->fetchAll(PDO::FETCH_ASSOC) : $expire;
if (is_array($expireArray) && count($expireArray) > 0) {
    foreach ($expireArray as $user) {
        $idTemp = $user['id'];
        SQLUpdate("UPDATE Utilisateur SET role = 1, dateFinRole = NULL WHERE id = $idTemp");
        
        
        SQLUpdate("UPDATE Utilisateur SET role = 2 WHERE (id = 1 OR role = 1) AND dateFinRole IS NULL ORDER BY id ASC LIMIT 1");

        $msg = "Votre période de délégation est terminée.";
        SQLInsert("INSERT INTO Notification (idUser, contenu) VALUES ($idTemp, '$msg')");
    }
}


if (isset($_SESSION["idUser"])) {
    $roleActuel = SQLGetChamp("SELECT role FROM Utilisateur WHERE id=" . intval($_SESSION["idUser"]));
    $_SESSION["role"] = $roleActuel;
}


$view = valider("view");
if (!$view) $view = "main";

include("./pages/header.php");

switch ($view) {
    case "main":
        include("./pages/main.php");
        break;

    case "superadmin":
        include("./pages/superadmin.php");
        break;

    default:
        if (file_exists("pages/$view.php"))
            include("pages/$view.php");
}
?>