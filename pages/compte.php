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
$machine = lister_machine();
$reserv = lister_reserv();
$user = lister_user($idUser);
$reserv_user = lister_reserv_user($idUser) ?: [];
?>

<script>
    const machines = <?php echo json_encode($machine); ?>;
    const reservations = <?php echo json_encode($reserv); ?>;
    const user = <?php echo json_encode($user); ?>;

    function afficher_form_com(id) {
        $("#add-com-" + id).slideToggle(300);
    }


    $(document).ready(function () {
        $("#settings").on("click", function () {
            //$("#param").toggleClass("hidden");
            showSection("param");
        });

        $("#dashboard").on("click", function () {
            //$("#tabbord").toggleClass("hidden");
            showSection("tabbord");
            updateTime();
        });

        $("#calendar").on("click", function () {
            //$("#calendrier").toggleClass("hidden");
            showSection("calendrier");
        });

        $("#stat").on("click", function () {

            showSection("statistique");

            var myContext = document.getElementById("myChart");

            if (window.myChartInstance) {
                window.myChartInstance.destroy();
            }

            let labels = [];
            let datas = [];

            var barColors = 
[
"rgba(79,70,229,1.0)",    // Indigo principal (#4f46e5)
"rgba(67,97,238,1.0)",    // Bleu indigo
"rgba(72,149,239,1.0)",   // Bleu clair moderne
"rgba(56,189,248,1.0)",   // Cyan doux
"rgba(94,96,206,1.0)",    // Indigo froid
"rgba(116,87,219,1.0)",   // Violet indigo
"rgba(99,102,241,1.0)",   // Indigo lumineux
"rgba(59,130,246,1.0)",   // Bleu équilibré
"rgba(37,99,235,1.0)",    // Bleu plus profond
"rgba(76,110,245,1.0)",   // Indigo moderne
"rgba(90,103,216,1.0)",   // Indigo doux
"rgba(64,123,255,1.0)"    // Bleu vibrant mais cohérent
];

            machines.forEach(machine => {

                labels.push(machine.nom);

                const count = reservations.filter(reservation =>
                    reservation.idEquipement == machine.id &&
                    reservation.idUser == user[0].id

                ).length;

                console.log(user);
                console.log(machine)
                console.log(reservations);
                datas.push(count);

            });
            console.log(datas)

            const myChartConfig = {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Nombre de réservations",
                        data: datas,
                        backgroundColor: barColors
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            };

            window.myChartInstance = new Chart(myContext, myChartConfig);

        });

        function showSection(id) {
            $(".section").addClass("hidden");
            $("#" + id).removeClass("hidden");
        };


        function updateTime() {
            var date = new Date();
            const options = date.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }).toUpperCase();
            $('#time').html(options);
        };
        updateTime();

    });

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
        <h1 class="text-3xl font-bold text-gray-800"><?php echo "Bonjour $prenom $nom"; ?></h1>
        <!-- La date du jour s'affiche ici -->
        <div id="time" class="bg-indigo-600 shadow-sm rounded-3xl text-white px-5 py-2 transition-all"></div> 
    </div>
</div>


<!-- Calendrier -->
<div class="section hidden container mx-auto p-6 max-w-4xl" id="calendrier">

<button class="block mx-auto bg-indigo-200 text-gray-700 px-5 py-2 rounded-xl hover:bg-indigo-400 transition-all font-medium mb-8">
    Voir d'anciennes réservations
</button>

<?php if (empty($reserv_user)) { ?>
    <p class="text-slate-500 italic">Aucune réservation en cours.</p>
<?php } ?>

<?php foreach ($reserv_user as $res) { ?>

<div class="bg-white border border-slate-200 rounded-2xl p-6 mb-6 shadow-sm hover:shadow-md transition-shadow">

    <div class="flex justify-between items-start mb-4">

        <div>
            <h3 class="text-2xl font-bold text-indigo-600 mb-3">
                <?= $res["nom"] ?>
            </h3>

            <div class="flex flex-wrap gap-3 mb-2">

                <!-- Date de début -->
                <div class="flex items-center gap-2 bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full font-medium shadow-sm">
                    <!-- Icône calendrier moderne -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Début: <?= date("d/m/Y H:i", strtotime($res["dateDebut"])) ?>
                </div>

                <!-- Date de fin -->
                <div class="flex items-center gap-2 bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full font-medium shadow-sm">
                    <!-- Icône horloge -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Fin: <?= date("d/m/Y H:i", strtotime($res["dateFin"])) ?>
                </div>

            </div>
        </div>

        <span class="text-sm font-medium px-3 py-1 rounded-full bg-indigo-100 text-indigo-700">
            Réservé
        </span>

    </div>

    <div class="flex justify-end gap-3 border-t border-slate-100 pt-4 flex-wrap">

            <button onclick='afficher_form_com(<?= $res["idReserv"] ?>)'
            class="bg-indigo-600 text-white px-5 py-2 rounded-xl hover:bg-indigo-600 transition-all font-medium">
                + Signaler un problème
            </button>
        </form>

        <form action="controleur.php" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réservation ?');">
            <input type="hidden" name="id" value="<?= $res["idReserv"] ?>">

            <button type="submit" name="action" value="supprimer"
            class="bg-red-500 text-white px-5 py-2 rounded-xl hover:bg-red-600 transition-all font-medium">
                Supprimer la réservation
            </button>
        </form>

    </div>
    <div id='add-com-<?= $res["idReserv"] ?>' style='display:none;' class="mt-4 p-4 bg-slate-50 rounded-xl border border-dashed border-slate-300">
            <form action="controleur.php" method="POST" class="flex flex-col gap-3">
                <input type="hidden" name="id_reserv" value="<?= $res["idReserv"] ?>">
                <input type="hidden" name="id_equip" value="<?= $res["idEquip"] ?>">
                <textarea name="texte" placeholder="Décrivez le problème ou l'information importante..." class="w-full p-3 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none resize-none" rows="2" required></textarea>
                <div class="flex justify-end">
                    <button type="submit" name="action" value="Ajouter Commentaire eleve" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-indigo-700 transition-all">
                        Envoyer
                    </button>
                </div>
            </form>
        </div>

</div>

<?php } ?>

</div>


<!-- Statistiques -->
<div class="section hidden container mx-auto p-6 max-w-4xl items-center" id="statistique">
    <div class="bg-white shadow-lg rounded-3xl p-8 mb-8 flex  items-center border border-gray-100">
        <div>
            <div class="text-3xl font-bold text-gray-800 !items-center"> <canvas id="myChart" width="500"
                    height="300"></canvas></div>
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