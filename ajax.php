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

    default:
        http_response_code(400);
        echo json_encode(["error" => "Action inconnue"]);
}