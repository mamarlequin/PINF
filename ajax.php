<?php
session_start();
include_once "libs/maLibSQL.pdo.php";
include_once "libs/modele.php"; 
include_once "libs/maLibUtils.php"; 

header('Content-Type: application/json');

$action = valider("action");

switch($action) {
    case "charger_donnees_semaine":
        $offset = (int)valider("offset"); 
        
        $lundi = new DateTime('monday this week');
        $lundi->modify("$offset weeks");
        $debutSemaine = $lundi->format('Y-m-d 00:00:00');
        
        $dimanche = clone $lundi;
        $dimanche->modify('+6 days');
        $finSemaine = $dimanche->format('Y-m-d 23:59:59');
        echo json_encode([
            "machines" => lister_machine(),
            "planningAdmin" => lister_dispo($debutSemaine, $finSemaine),
            "planningReservations" => lister_res($debutSemaine, $finSemaine),
            "planningEmprunts" => lister_emprunts($debutSemaine, $finSemaine),
            "lundi" => $lundi->format('Y-m-d')
        ]);
        break;

    case "search":
        $mot = valider("mot");
        echo json_encode(recherche_machine($mot)); 
        break;

    case "disparaitre":
        $mot = valider("mot");
        echo json_encode(dispa($mot));
        break;

    case "filtrerDispo":
        $date = valider("date");
        echo json_encode(recherche_dispo_filtre($_SESSION["idUser"], $date)); 
        break;

    case "filtrerDispodispa":
        $date = valider("date");
        echo json_encode(recherche_dispo_filtre_dispa($_SESSION["idUser"], $date)); 
        break;

    case "filtre_mac":
        $date = valider("date");
        $machines = valider("machines");
        echo json_encode(recherche_machine_filtre($_SESSION["idUser"], $date, $machines)); 
        break;
    
    case "filtre_mac_invers":
        $date = valider("date");
        $machines = valider("machines");

        echo json_encode(dispa_filtre($_SESSION["idUser"], $date, $machines));
        break;

    case "afficher_com":
        $id = valider("id");
        echo json_encode(listercom($id));

    case "search_user":
        $mot = valider("mot");
        echo json_encode(recherche_user($mot)); 
        break;

    case "disparaitre_user":
        $mot = valider("mot");
        echo json_encode(disparait_user($mot));
        break;

        
    default:
        http_response_code(400);
        echo json_encode(["error" => "Action inconnue"]);
}

