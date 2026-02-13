<?php
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
    header("Location:../index.php?view=compte");
    die("");
}

if (!isset($_SESSION["idUser"])) {
    header("Location:../index.php?view=login");
    die("");
}

$nom = $_SESSION["nom"];
$prenom = $_SESSION["prenom"];
$idUser = $_SESSION["idUser"];


$estAdmin = isAdmin($idUser);
$estSuperAdmin = isSuperAdmin($idUser);
?>

<script>

    $(document).ready(function () {
        $("#settings").on("click", function () {
            //$("#param").toggleClass("hidden");
            showSection("param");
        });

        $("#dashboard").on("click", function () {
            //$("#tabbord").toggleClass("hidden");
            showSection("tabbord");
        });

        $("#calendar").on("click", function () {
            //$("#calendrier").toggleClass("hidden");
            showSection("calendrier");
        });

        $("#stat").on("click", function () {
            //$("#statistique").toggleClass("hidden");
            showSection("statistique");
        });

        function showSection(id) {
            //Ferme toutes les sections
            $(".section").addClass("hidden");

            //Ouvre seulement celle clickée
            $("#" + id).removeClass("hidden");
        }
    })

</script>

<!-- Bannière -->
<div class="container mx-auto p-6 max-w-4xl">
    <div class="bg-white shadow-lg rounded-3xl p-8 mb-8 flex justify-between items-center border border-gray-100">
        <div id="dashboard">
            <button class="transition-colors hover:text-indigo-600"> Tableau de Bord </button>
        </div>
        <div id="calendar">
            <button class="transition-colors hover:text-indigo-600"> Calendrier </button>
        </div>
        <div id="stat">
            <button class="transition-colors hover:text-indigo-600"> Statistiques </button>
        </div>
        <div id="settings">
            <button class="transition-colors hover:text-indigo-600"> Paramètres </button>
        </div>
    </div>
</div>



<!-- Tableau de Bord -->
<div class="section container mx-auto p-6 max-w-4xl" id="tabbord">
    <div class="bg-white shadow-lg rounded-3xl p-8 mb-8 flex justify-between items-center border border-gray-100">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"><?php echo "Bonjour $prenom $nom"; ?></h1>
        </div>
    </div>
</div>


<!-- Calendrier -->
<div class="section hidden container mx-auto p-6 max-w-4xl" id="calendrier">
    <div class="bg-white shadow-lg rounded-3xl p-8 mb-8 flex justify-between items-center border border-gray-100">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"> Je sais pas ce qu'il faut y mettre pour l'instant </h1>
        </div>
    </div>
</div>


<!-- Statistiques -->
<!-- Eulalie tu dois mettre ici les stat stp -->
<div class="section hidden container mx-auto p-6 max-w-4xl" id="statistique">
    <div class="bg-white shadow-lg rounded-3xl p-8 mb-8 flex justify-between items-center border border-gray-100">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"> Ici c'est pour Eulalie </h1>
        </div>
    </div>
</div>


<!-- Paramètres -->
<div class="section hidden container mx-auto p-6 max-w-4xl" id="param">
    <div class="bg-white shadow-lg rounded-3xl p-8 mb-8 flex justify-between items-center border border-gray-100">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"><?php echo "$prenom $nom"; ?></h1>
            <p class="text-indigo-600 font-medium">
                <?php echo ($estSuperAdmin) ? "Super Administrateur du Fablab" : (($estAdmin) ? "Administrateur du Fablab" : "Étudiant"); ?>
            </p>
        </div>
        <a href="controleur.php?action=Logout"
            class="bg-red-500 text-white px-6 py-2 rounded-3xl hover:bg-red-600 transition-all shadow-sm active:scale-95">
            Déconnexion
        </a>
    </div>
</div>