<?php


if (basename($_SERVER["PHP_SELF"]) != "index.php") {
    header("Location:../index.php?view=admin");
    die("");
}

include_once("libs/maLibSecurisation.php");
include_once("libs/modele.php");

securiser("login");

if(!isset($_SESSION["idUser"])){
    header("Location:../index.php?view=login");
    die("");
}

if (!isAdmin($_SESSION["idUser"])) {
    header("Location:index.php?view=main&msg=AccesRefuse");
    die("");
}

?>

<div class="bg-white shadow-md rounded-lg p-6 mb-8 mt-10 border-l-4 border-indigo-500 max-w-5xl mx-auto">
    <h2 class="text-xl font-semibold mb-4 text-indigo-700">Indiquer mes disponibilités</h2>
    <form action="controleur.php" method="POST" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700">Début</label>
            <input type="datetime-local" name="debut" class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Fin</label>
            <input type="datetime-local" name="fin" class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
        </div>
        <input type="submit" name="action" value="Enregistrer Dispo" class="bg-green-600 text-white px-6 py-2 rounded-3xl hover:bg-green-700 transition-all cursor-pointer">
    </form>
</div>
<div class="bg-white shadow-xl rounded-3xl p-8 border border-gray-100 max-w-5xl mx-auto mt-10">
    <h2 class="text-2xl font-bold text-indigo-600 mb-6 flex items-center">
        Ajouter un membre au Fablab
    </h2>

    <form action="controleur.php" method="POST" class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <input type="text" name="nom" placeholder="Nom" class="w-full border-gray-200 rounded-2xl p-3 focus:ring-indigo-500" required>
            <input type="text" name="prenom" placeholder="Prénom" class="w-full border-gray-200 rounded-2xl p-3 focus:ring-indigo-500" required>
        </div>

        <input type="email" name="email" placeholder="Adresse email (pour l'envoi du MDP)" class="w-full border-gray-200 rounded-2xl p-3 focus:ring-indigo-500" required>


        <button type="submit" name="action" value="Créer Utilisateur"
            class="w-full bg-indigo-600 text-white font-bold py-4 rounded-2xl hover:bg-indigo-700 transition-all shadow-lg active:scale-95">
            Créer le compte et envoyer les accès
        </button>
    </form>
</div>


