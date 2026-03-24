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

<div class="container mx-auto p-6 max-w-4xl">
  <div class="bg-white shadow-lg rounded-3xl p-8 mb-8 border border-gray-100 flex flex-wrap justify-center gap-6 items-center">

    <div id="dashboard">
      <button class="px-6 py-2 rounded-2xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium transition-colors">
        Disponibilités
      </button>
    </div>

    <div id="calendar">
      <button class="px-6 py-2 rounded-2xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium transition-colors">
        Ajouter un membre
      </button>
    </div>

    <div id="stat">
      <button class="px-6 py-2 rounded-2xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium transition-colors">
        Statistiques
      </button>
    </div>

  </div>
</div>
<div class="section bg-white rounded-2xl shadow-sm border max-w-6xl mx-auto mt-10 p-6" id="disponibilités">
    
    <h1 class="text-2xl font-bold text-indigo-900 mb-6">
        Indiquer mes disponibilités
    </h1>

    <form action="controleur.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
        
        <div>
            <label class="block text-sm text-slate-500 mb-2">Début</label>
            <input type="datetime-local" name="debut"
                class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                required>
        </div>

        <div>
            <label class="block text-sm text-slate-500 mb-2">Fin</label>
            <input type="datetime-local" name="fin"
                class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                required>
        </div>

        <div>
            <input type="submit" name="action" value="Enregistrer"
                class="w-full bg-indigo-600 text-white font-semibold py-3 rounded-xl hover:bg-indigo-700 transition">
        </div>

    </form>
</div>
<div class="section hidden bg-white rounded-2xl shadow-sm border max-w-6xl mx-auto mt-10 mb-20 p-8" id="Ajouter">
    
    <h1 class="text-2xl font-bold text-indigo-900 mb-8">
        Ajouter un membre au Fablab
    </h1>

    <form action="controleur.php" method="POST" class="space-y-8">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <label class="block text-sm text-slate-500 mb-2">Nom</label>
                <input type="text" name="nom"
                    class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    required>
            </div>

            <div>
                <label class="block text-sm text-slate-500 mb-2">Prénom</label>
                <input type="text" name="prenom"
                    class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    required>
            </div>
        </div>

        <div>
            <label class="block text-sm text-slate-500 mb-2">Adresse email</label>
            <input type="email" name="email"
                class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                required>
        </div>

        <div class="pt-4">
            <button type="submit" name="action" value="Créer Utilisateur"
                class="w-full bg-indigo-600 text-white font-semibold py-3 rounded-xl hover:bg-indigo-700 transition">
                Créer le compte
            </button>
        </div>

    </form>
</div>

<div id="statistique" class="section hidden">

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
</div>

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

        $("#stat").on("click", function () {
            //$("#param").toggleClass("hidden");
            showSection("statistique");
        });

        $("#dashboard").on("click", function () {
            //$("#tabbord").toggleClass("hidden");
            showSection("disponibilités");
        });

        $("#calendar").on("click", function () {
            //$("#calendrier").toggleClass("hidden");
            showSection("Ajouter");
        });

        function showSection(id) {
        $(".section").addClass("hidden");
        $("#" + id).removeClass("hidden");
    }


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