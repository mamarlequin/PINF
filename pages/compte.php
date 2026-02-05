<?php
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
    header("Location:../index.php?view=compte");
    die("");
}

if(!isset($_SESSION["idUser"])){
    header("Location:../index.php?view=login");
    die("");
}

$nom = $_SESSION["nom"];
$prenom = $_SESSION["prenom"];
$idUser = $_SESSION["idUser"];


$estAdmin = isAdmin($idUser);
?>

<div class="container mx-auto p-6 max-w-4xl">
    <div class="bg-white shadow-lg rounded-3xl p-8 mb-8 flex justify-between items-center border border-gray-100">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"><?php echo "$prenom $nom"; ?></h1>
            <p class="text-indigo-600 font-medium">
                <?php echo ($estAdmin) ? "Administrateur du Fablab" : "Étudiant"; ?>
            </p>
        </div>
        <a href="controleur.php?action=Logout"
            class="bg-red-500 text-white px-6 py-2 rounded-3xl hover:bg-red-600 transition-all shadow-sm active:scale-95">
            Déconnexion
        </a>
    </div>
</div>