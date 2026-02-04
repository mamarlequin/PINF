<?php
session_start();
include_once "libs/maLibSQL.pdo.php";
include_once "libs/modele.php"; 
include_once "libs/maLibUtils.php"; 

header('Content-Type: application/json');

$action = valider("action");

switch($action) {
    case "lister_machines":
        echo json_encode(lister_machine()); 
        break;

    case "search":
        $mot = valider("mot");
        echo json_encode(recherche_machine($mot)); 
        break;

    case "disparaitre":
        $mot = valider("mot");
        echo json_encode(dispa($mot));
        break;

    case "afficher_com":
        $id = valider("id");
        echo json_encode(listercom($id));
    default:
        http_response_code(400);
        echo json_encode(["error" => "Action inconnue"]);
}