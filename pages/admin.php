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

$utilisateurs = parcoursRs(getUtilisateursGestion());

if (!$utilisateurs) {
    $utilisateurs = array();
}
$machines_nb = lister_machine();
$reserv_nb = lister_reserv();

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

<div class="flex items-center mb-6">

    <input
        id="rechercheUser"
        type="text"
        name="recherche"
        placeholder="Recherchez un utilisateur..."
        class="h-10 px-4 py-2 border border-gray-300 !rounded-l-full focus:outline-none focus:ring-2 focus:ring-gray-300">

    <button
        class="h-10 px-4 py-2 border border-gray-300 border-l-0 rounded-r-full hover:bg-indigo-600 !text-white ">
        <svg
            class="w-4 h-4 text-gray-600"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2" />
            <line x1="16.65" y1="16.65" x2="22" y2="22"
                stroke="currentColor" stroke-width="2" />
        </svg>
    </button>
</div>
<div class="max-w-6xl mx-auto mt-10 p-6 bg-white rounded-2xl shadow-sm border">
    <h1 class="text-2xl font-bold text-indigo-900 mb-6">Statistiques</h1>

    <table class="w-full text-left">
        <thead>
            <tr class="border-b text-slate-500">
                <th class="py-3 px-2">Utilisateur</th>
                <th class="py-3 px-2">Rôle actuel</th>
                <th class="py-3 px-2 text-center">Voir ses statistiques</th>
            </tr>
        </thead>
        <tbody>
            <?php if (is_array($utilisateurs) && count($utilisateurs) > 0): ?>
                <?php foreach($utilisateurs as $u): ?>
                    
                    <tr id="<?= $u["id"] ?>" class="border-b hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-2 font-medium"><?= $u['prenom'] ?> <?= $u['nom'] ?></td>
                        <td class="py-4 px-2">
                            <span class="px-2 py-1 rounded-full text-xs <?= $u['role'] == 1 ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' ?>">
                                <?= $u['role'] == 1 ? 'Admin' : 'Étudiant' ?>
                            </span>
                        </td>
                        
                        <td class="py-4 px-2">
                            <div class="flex gap-2 justify-center">
                                        <button id="<?= $u['id'] ?>" class="text-xs bg-indigo-600 text-white px-3 py-1.5 rounded hover:bg-indigo-700"onclick='afficher_stat(<?= json_encode($machines_nb, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>, <?= json_encode($reserv_nb, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>, <?= json_encode($u, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>)' ?>
                                            Voir ses statistiques
                                        </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="py-10 text-center text-slate-400">Aucun utilisateur trouvé.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<BR>
<div class="groupe1 bg-white items-center" id="stat-" style='display:none;'>
	<canvas id="myChart" width="500" height="300"></canvas>
</div>
<BR>

<script>

function afficher_stat(machines, reservations, user){

    const statDiv = $("#stat-");

    statDiv.slideToggle(500, function() {

        var myContext = document.getElementById("myChart");

        if(window.myChartInstance) {
            window.myChartInstance.destroy();
        }

        let labels = [];
        let datas = [];
        var barColors = [
  "rgba(0,0,255,1.0)",
  "rgba(0,0,255,0.8)",
  "rgba(0,0,255,0.6)",
  "rgba(0,0,255,0.4)",
  "rgba(0,0,255,0.2)",
];

        if (Array.isArray(machines) && Array.isArray(reservations)) {

            machines.forEach(machine => {

                labels.push(machine.nom);

                const count = reservations.filter(reserv =>
                    reserv.idEquipement == machine.id &&
                    reserv.idUser == user.id  
                ).length;
                console.log(count);
                datas.push(count);
                console.log("USER :", user);
                console.log("RESERVATION SAMPLE :", reservations[0]);

            });

        } else {
            console.log("Données invalides");
        }

        const myChartConfig = {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: "Nombre de réservations",
                    data: datas,
                    backgroundColor: barColors,
                    borderColor: 'rgb(97, 75, 192)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        };

        window.myChartInstance = new Chart(myContext, myChartConfig);
    });
}


      $(document).ready(function() {
        $("#rechercheUser").on("keyup", function() {
            var titre = $(this).val() || "";

            $.ajax({
                type: "GET",
                url: "ajax.php",
                data: {
                    "action": "disparaitre_user",
                    "mot": titre
                },
                dataType: "json",
                success: function(oRep) {
                    oRep.forEach(element => {
                        $("#" + element.id).css("display", "none");
                        console.log("apparait : : ");
                        console.log(element.id);
                    });
                },
                error: function() {
                    console.log("Erreur lors de la récupération des machines");
                },
            });


            $.ajax({
                type: "GET",
                url: "ajax.php",
                data: {
                    "action": "search_user",
                    "mot": titre
                },
                dataType: "json",
                success: function(oRep) {
                    oRep.forEach(element => {
                        $("#" + element.id).css("display", "table-row");
                        console.log("disparait : ");
                        console.log(element.id);
                    });
                },
                error: function() {
                    console.log("Erreur lors de la récupération des machines");
                },
            });
        })
    });
</script>