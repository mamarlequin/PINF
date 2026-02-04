<?php
// Redirection si la page est appelée directement
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
    header("Location:../index.php?view=login");
    die("");
}

// Chargement éventuel des cookies
$login = valider("nom", "COOKIE");
$prenom = valider("prenom", "COOKIE");
$passe = valider("motdepasse", "COOKIE");
$checked = valider("remember", "COOKIE") ? "checked" : "";
?>


<div class='form-card'>
    <?php 
    mkForm("controleur.php", "get", "form-connexion");
       echo "<h1 class='text-center'>Connexion</h1>";
       echo "Nom de famille";
        mkInput("text", "nom", $login, "required");
        echo "Prénom";
        mkInput("text", "prenom", $prenom, "required");
        echo "Mot de passe";
        mkInput("password", "motdepasse", $passe, "required"); ;
        echo "Se souvenir de moi  " ;
        mkInput("checkbox", "remember", "1", $checked);
        mkInput("submit", "action", "Connexion");


    endForm();
    ?>
</div>
