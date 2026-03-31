<?php
/////////////////////////////DECLARATIONS/////////////////////////////////////////////

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
$dispo = dispo($idUser) ?: [];
$reserv_ancien_user = lister_reserv_user_ancienne($idUser) ?: [];
$emprunt_ancien_user = lister_emprunt_user_ancienne($idUser) ?: [];
$emprunts = lister_emprunt($idUser) ?: [];
$outils = lister_outil($idUser) ?: [];

?>

<script>
    //////////////////////////JS fonctions/////////////////////////////////
    const machines = <?php echo json_encode($machine); ?>;
    const reservations = <?php echo json_encode($reserv); ?>;
    const user = <?php echo json_encode($user); ?>;

    function afficher_form_com(id) {//commentaires
        $("#add-com-" + id).slideToggle(300);
    }

    function Afficher_ancien_emrpunt(){
    $("#ancien_empr").slideToggle(300);
}

    function Afficher_ancien_reserv(){//anciennes réservations
        $("#ancien_reser").slideToggle(300);
    }

 function modif_dispoo(id){ /// modifier les dispos affichage

    let debut = $("#DEBUTDATE" + id).text();
    let fin = $("#FINDATE" + id).text();

    let debutFormat = formatDate(debut);
    let finFormat = formatDate(fin);

    let heuredebut = formatHeure(debut);
    let heurefin = formatHeure(fin);

    $("#DEBUTDATE" + id).hide();
    $("#FINDATE" + id).hide();
    $("#button" + id).hide();

    $("#datedebut" + id).val(debutFormat).removeClass("hidden");
    $("#datefin" + id).val(finFormat).removeClass("hidden");

    $("#heuredebut" + id).val(heuredebut).removeClass("hidden");
    $("#heurefin" + id).val(heurefin).removeClass("hidden");

    $("#enreg" + id).removeClass("hidden");
}

function formatHeure(dateStr){
    return dateStr.split(" ")[1];
}

function formatDate(dateStr){
    let parts = dateStr.split(" ")[0].split("/");
    return parts[2] + "-" + parts[1] + "-" + parts[0];
}

    $(document).ready(function () {//affichage menu
        $("#settings").on("click", function () {
            //$("#param").toggleClass("hidden");
            showSection("param");
        });

        $("#dashboard").on("click", function () {
            //$("#tabbord").toggleClass("hidden");
            showSection("tabbord");
            updateTime();
        });

        $("#emprunts").on("click", function () {
            //$("#tabbord").toggleClass("hidden");
            showSection("MesEmprunts");
            updateTime();
        });

        $("#admin").on("click", function () {
            //$("#tabbord").toggleClass("hidden");
            showSection("administration");
            updateTime();
        });

        $("#calendar").on("click", function () {
            //$("#calendrier").toggleClass("hidden");
            showSection("calendrier");
        });

        $("#stat").on("click", function () { // générations statisqtiques

            showSection("statistique");

            var myContext = document.getElementById("myChart");

            if (window.myChartInstance) {
                window.myChartInstance.destroy();
            }

            let labels = [];
            let datas = [];

            var barColors = 
[//couleurs stats
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
<div class="container mx-auto p-4 max-w-4xl">
  <div class="bg-white shadow-lg rounded-3xl p-4 mb-6 border border-gray-100 flex flex-wrap justify-center gap-3 items-center">

    <!-- Menu -->
    <div id="menu" class="flex flex-wrap justify-center gap-3 w-full">

      <div id="dashboard">
        <button class="px-6 py-2 rounded-2xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium transition-colors">
          Tableau de Bord
        </button>
      </div>

      <?php if(isAdmin($idUser)){ ?>
      <div id="admin">
        <button class="px-6 py-2 rounded-2xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium transition-colors">
          Mes disponibilités
        </button>
      </div>

      <div id="emprunts">
        <button class="px-6 py-2 rounded-2xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium transition-colors">
          Mes emprunts
        </button>
      </div>
      <?php } ?>

      <div id="calendar">
        <button class="px-6 py-2 rounded-2xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium transition-colors">
          Mes réservations
        </button>
      </div>

      <div id="stat">
        <button class="px-6 py-2 rounded-2xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium transition-colors">
          Statistiques
        </button>
      </div>

      <div id="settings">
        <button class="px-6 py-2 rounded-2xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium transition-colors">
          Paramètres
        </button>
      </div>

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


<!-- disponibilitées -->
<div class="section hidden container mx-auto p-6 max-w-4xl" id="administration">
            <!-- barre de recherche -->
            <div class="flex items-center mb-2">
        <input
            id="rechercheDispo"
            type="date"
            name="recherche"
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
    <!-- si aucune reservations : -->
            <?php if (empty($dispo)) { ?>
            <p class="text-slate-500 italic">Aucune réservation en cours.</p>
        <?php } ?>
    <!-- affichage réservations -->
        <?php foreach ($dispo as $res) { ?>
    <form action="controleur.php" method="POST" id="<?= $res["id"] ?>" class="reservation">
        <div class="bg-white border border-slate-200 rounded-2xl p-6 mb-6 shadow-sm hover:shadow-md transition-shadow">

                <div class="flex justify-between items-start mb-4" >

                <div>

                    <div class="flex flex-wrap gap-3 mb-2">

                        
                        <div class="flex items-center gap-2 bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full font-medium shadow-sm">
                            
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Début: <p id="DEBUTDATE<?= $res["id"] ?>"><?= date("d/m/Y H:i", strtotime($res["dateDebut"])) ?></p>
                        <input type="date" value="" id="datedebut<?= $res["id"] ?>" class="hidden" name="datedebut"
                        required  min="<?= date('Y-m-d'); ?>">
                            <input type="time"  id="heuredebut<?= $res["id"] ?>" class="hidden" name="heuredebut" required  min="<?= date('Y-m-d'); ?>">
                            
                        </div>

                        
                        <div class="flex items-center gap-2 bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full font-medium shadow-sm">
                        
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Fin: <p id="FINDATE<?= $res["id"] ?>"><?= date("d/m/Y H:i", strtotime($res["dateFin"])) ?></p>
                            <input type="date" id="datefin<?= $res["id"] ?>" class="hidden" name="datefin" required  min="<?= date('Y-m-d'); ?>">
                            <input type="time"  id="heurefin<?= $res["id"] ?>" class="hidden" name="heurefin" required  min="<?= date('Y-m-d'); ?>">
                        </div>
                        

                    </div>
                </div>


            </div>
    <!-- BOUTON MODIFIER DISPO + requetes PHP -->
            <div class="flex justify-end gap-3 border-t border-slate-100 pt-4 flex-wrap">

                    <button type="button" id="button<?= $res["id"] ?>" onclick='modif_dispoo(<?= $res["id"] ?>)'
                    class="bg-indigo-600 text-white px-5 py-2 rounded-xl hover:bg-indigo-600 transition-all font-medium">
                        Modifier la disponibilité
                    </button>
                    <input type="hidden" name="id" value="<?= $res["id"] ?>">
                    <button type="submit" name="action" id="enreg<?= $res["id"] ?>" class="bg-indigo-600 hidden text-white px-5 py-2 rounded-xl hover:bg-indigo-600 transition-all font-medium" value="Enregistrer les modifications" >Enregistrer les modifications</button>
                

            </div>

        </div>
    </form>
        <?php } ?>
</div>


<!-- Calendrier -->
<div class="section hidden container mx-auto p-6 max-w-4xl" id="calendrier">
    <!-- Barre de recherche date -->
    <div class="flex items-center mb-2">
        <input
            id="rechercheMachine"
            type="date"
            name="recherche"
            placeholder="Entrez votre recherche..."
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
    <!-- filtre des machines -->
    <div class="flex flex-wrap gap-2 mb-6">
    <?php foreach ($machine as $mac): 
        $id = "machine_" . $mac["id"];
    ?>
        <div class="relative">

            <input type="checkbox" id="<?= $id ?>" name="machines[]" value="<?= $mac["id"] ?>" class="hidden peer" checked>

            <label for="<?= $id ?>"
                class="inline-block cursor-pointer px-3 py-1 rounded-full text-white bg-indigo-400 peer-checked:bg-indigo-600 transition-colors font-medium select-none text-sm">
                <?= htmlspecialchars($mac["nom"]) ?>
            </label>
        </div>
    <?php endforeach; ?>
    </div>
    <!-- bouton pour voir les anciennes reservations -->
    <button class="block mx-auto bg-indigo-200 text-gray-700 px-5 py-2 rounded-xl hover:bg-indigo-400 transition-all font-medium mb-8" onclick="Afficher_ancien_reserv()">
        Voir d'anciennes réservations
    </button>
    <!-- Affichage de ces anciennes réservations  -->
    <div id=ancien_reser class='hidden'>
        
        <?php foreach ($reserv_ancien_user as $res) { ?>

    <div class="reservationMac bg-white border border-slate-200 rounded-2xl p-6 mb-6 shadow-sm hover:shadow-md transition-shadow" id="res<?= $res["idReserv"] ?>">

        <div class="flex justify-between items-start mb-4" >

            <div>
                <h3 class="text-2xl font-bold text-indigo-600 mb-3">
                    <?= $res["nom"] ?>
                </h3>

                <div class="flex flex-wrap gap-3 mb-2">

                    
                    <div class="flex items-center gap-2 bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full font-medium shadow-sm">
                        
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Début: <?= date("d/m/Y H:i", strtotime($res["dateDebut"])) ?>
                    </div>

                
                    <div class="flex items-center gap-2 bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full font-medium shadow-sm">
                    
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Fin: <?= date("d/m/Y H:i", strtotime($res["dateFin"])) ?>
                    </div>

                </div>
            </div>
    <!-- petite bannière réserver -->
            <span class="text-sm font-medium px-3 py-1 rounded-full bg-indigo-100 text-indigo-700">
                Réservé
            </span>

        </div>

        <div class="flex justify-end gap-3 border-t border-slate-100 pt-4 flex-wrap">
    <!-- bouton signaler un pb -->
                <button onclick='afficher_form_com(<?= $res["idReserv"] ?>)'
                class="bg-indigo-600 text-white px-5 py-2 rounded-xl hover:bg-indigo-600 transition-all font-medium">
                    + Signaler un problème
                </button>
            </form>

        </div>
        <!-- Ajouter un commentaire, la partie qui s'affiche après appui sur le bouton -->
        <div id='add-com-<?= $res["idReserv"] ?>_' style='display:none;' class="mt-4 p-4 bg-slate-50 rounded-xl border border-dashed border-slate-300">
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
    <!-- si aucune reservations -->
    <?php if (empty($reserv_user)) { ?>
        <p class="text-slate-500 italic">Aucune réservation en cours.</p>
    <?php } ?>
    <!-- Sinon affichage des réservations qui se passe après la date du jour ou aujourd'hui -->
    <?php foreach ($reserv_user as $res) { ?>

    <div class="reservationMac bg-white border border-slate-200 rounded-2xl p-6 mb-6 shadow-sm hover:shadow-md transition-shadow" id="res<?= $res["idReserv"] ?>">

            <div class="flex justify-between items-start mb-4" >

            <div>
                <h3 class="text-2xl font-bold text-indigo-600 mb-3">
                    <?= $res["nom"] ?>
                </h3>

                <div class="flex flex-wrap gap-3 mb-2">

                    
                    <div class="flex items-center gap-2 bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full font-medium shadow-sm">
                        
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Début: <?= date("d/m/Y H:i", strtotime($res["dateDebut"])) ?>
                    </div>

                    
                    <div class="flex items-center gap-2 bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full font-medium shadow-sm">
                    
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
    <!-- Bouton signaler un pb -->
        <div class="flex justify-end gap-3 border-t border-slate-100 pt-4 flex-wrap">

                <button onclick='afficher_form_com(<?= $res["idReserv"] ?>)'
                class="bg-indigo-600 text-white px-5 py-2 rounded-xl hover:bg-indigo-600 transition-all font-medium">
                    + Signaler un problème
                </button>
            </form>

            <form action="controleur.php" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réservation ?');">
                <input type="hidden" name="id" value="<?= $res["idReserv"] ?>">
    <!-- Bouton supprimer la résevations -->
                <button type="submit" name="action" value="supprimer"
                class="bg-red-500 text-white px-5 py-2 rounded-xl hover:bg-red-600 transition-all font-medium">
                    Supprimer la réservation
                </button>
            </form>

        </div>
        <!-- formulaire du commenaitre -->
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



<!-------------------- EMPRUNTS --->

<div class="section hidden container mx-auto p-6 max-w-4xl" id="MesEmprunts">
    <!-- Barre de recherche date -->
    <div class="flex items-center mb-2">
        <input
            id="rechercheEmprunt"
            type="date"
            name="rechercheEmpruntt"
            placeholder="Entrez votre recherche..."
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
    <!-- filtre des outils -->
    <div class="flex flex-wrap gap-2 mb-6">
    <?php foreach ($outils as $outil): 
        $id = "outil_" . $outil["id"];
    ?>
        <div class="relative">

            <input type="checkbox" id="<?= $id ?>" name="outil[]" value="<?= $outil["id"] ?>" class="hidden peer" checked>

            <label for="<?= $id ?>"
                class="inline-block cursor-pointer px-3 py-1 rounded-full text-white bg-indigo-400 peer-checked:bg-indigo-600 transition-colors font-medium select-none text-sm">
                <?= htmlspecialchars($outil["nom"]) ?>
            </label>
        </div>
    <?php endforeach; ?>
    </div>
    <!-- bouton pour voir les ancien emprunts -->
    <button class="block mx-auto bg-indigo-200 text-gray-700 px-5 py-2 rounded-xl hover:bg-indigo-400 transition-all font-medium mb-8" onclick="Afficher_ancien_emrpunt()">
        Voir d'anciens emprunts
    </button>
    <!-- Affichage de ces anciens emprunts  -->
    <div id=ancien_empr class='hidden'>
        
        <?php foreach ($emprunt_ancien_user as $emprunt) { ?>

    <div class="reservationMac bg-white border border-slate-200 rounded-2xl p-6 mb-6 shadow-sm hover:shadow-md transition-shadow" id="emp<?= $emprunt["idOutil"] ?>">

        <div class="flex justify-between items-start mb-4" >

            <div>
                <h3 class="text-2xl font-bold text-indigo-600 mb-3">
                    <?= $emprunt["nom"] ?>
                </h3>

                <div class="flex flex-wrap gap-3 mb-2">

                    
                    <div class="flex items-center gap-2 bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full font-medium shadow-sm">
                        
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Début: <?= date("d/m/Y H:i", strtotime($res["dateDebut"])) ?>
                    </div>

                
                    <div class="flex items-center gap-2 bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full font-medium shadow-sm">
                    
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Fin: <?= date("d/m/Y H:i", strtotime($emprunt["dateRenduReel"])) ?>
                    </div>

                </div>
            </div>
    <!-- petite bannière réserver -->
            <span class="text-sm font-medium px-3 py-1 rounded-full bg-indigo-100 text-indigo-700">
                Rendu
            </span>

        </div>

    <?php } ?>

        
    </div>
    </div>

    <!-- si aucune reservations -->
    <?php if (empty($emprunts)) { ?>
        <p class="text-slate-500 italic">Aucun emprunts en cours.</p>
    <?php } ?>
    <!-- Sinon affichage des réservations qui se passe après la date du jour ou aujourd'hui -->
    <?php foreach ($emprunts as $emprunt) { ?>

    <div class="reservationMac bg-white border border-slate-200 rounded-2xl p-6 mb-6 shadow-sm hover:shadow-md transition-shadow" id="emp<?= $emprunt["idOutil"] ?>">

            <div class="flex justify-between items-start mb-4" >

            <div>
                <h3 class="text-2xl font-bold text-indigo-600 mb-3">
                    <?= $emprunt["nom"] ?>
                </h3>

                <div class="flex flex-wrap gap-3 mb-2">

                    
                    <div class="flex items-center gap-2 bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full font-medium shadow-sm">
                        
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Début: <?= date("d/m/Y H:i", strtotime($res["dateDebut"])) ?>
                    </div>

                    
                    <div class="flex items-center gap-2 bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full font-medium shadow-sm">
                    
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Fin: <?= date("d/m/Y H:i", strtotime($res["dateRenduTheorique"])) ?>
                    </div>

                </div>
            </div>

            <span class="text-sm font-medium px-3 py-1 rounded-full bg-indigo-100 text-indigo-700">
                Emprunté
            </span>

        </div>
        
    <!-- Bouton signaler un pb -->


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

<script>

//////////////////////////////////AJAX////////////////////////////////////

$(document).ready(function() {
   function filtrerReserv() {///filtre avec machine et date

    let date = $("#rechercheMachine").val();
    let machines = [];

    $("input[name='machines[]']:checked").each(function(){
        machines.push($(this).val());
    });
    console.log(date);
    //if (!date) {
      //  $(".reservationMac").show();
        //return;
    //}
     $.ajax({
    url: "ajax.php",
    type: "GET",
    data: {
        action: "filtre_mac_invers",
        date: date,
        machines:machines,
    },
    dataType: "json",

    success: function(res){
        console.log("SUCCESS :", res);
        res.forEach(orep => {
                $("#res" + orep.idReserv).hide();
            })
    },

    error: function(xhr, status, error){
        console.log("ERREUR AJAX");
        console.log("Response :", xhr.responseText); 
        console.log("Status :", status);
        console.log("Error :", error);
    }

   })
    $.ajax({
        url :"ajax.php",
        type : "GET",
        data: {
            action:"filtre_mac",
            date: date,
            machines: machines
        },
        dataType: "json",
        success: function(res){
            console.log("S :", res);
        res.forEach(orep => {
                $("#res" + orep.idReserv).show();
            })
        }, 
        error: function() {
                    console.log("Erreur lors de la récupération des machines");
                    console.log("Response :", xhr.responseText); 
        console.log("Status :", status);
        console.log("Error :", error);
                }
    })
   }

function filtrerEmprunts() {///filtre avec machine et date

    let date = $("#rechercheEmprunt").val();
    let emprunts = [];

    $("input[name='outil[]']:checked").each(function(){
        emprunts.push($(this).val());
    });
    console.log(date);
    //if (!date) {
      //  $(".reservationMac").show();
        //return;
    //}
     $.ajax({
    url: "ajax.php",
    type: "GET",
    data: {
        action: "filtre_emp_invers",
        date: date,
        emprunts:emprunts,
    },
    dataType: "json",

    success: function(res){
        console.log("SUCCESS :", res);
        res.forEach(orep => {
                $("#emp" + orep.idOutil).hide();
            })
    },

    error: function(xhr, status, error){
        console.log("ERREUR AJAX");
        console.log("Response :", xhr.responseText); 
        console.log("Status :", status);
        console.log("Error :", error);
    }

   })
    $.ajax({
        url :"ajax.php",
        type : "GET",
        data: {
            action:"filtre_emp",
            date: date,
            emprunts: emprunts
        },
        dataType: "json",
        success: function(res){
            console.log("S :", res);
        res.forEach(orep => {
                $("#emp" + orep.idOutil).show();
            })
        }, 
        error: function() {
                    console.log("Erreur lors de la récupération des machines");
                    console.log("Response :", xhr.responseText); 
        console.log("Status :", status);
        console.log("Error :", error);
                }
    })
   }


function filtrerDispo(){
    let date = $("#rechercheDispo").val();
    console.log(date);
    if (!date) {
        $(".reservation").show();
        return;
    }
    $.ajax({
        url :"ajax.php",
        type : "GET",
        data: {
            action:"filtrerDispo",
            date: date,
        },
        dataType: "json",
        success: function(res){
            console.log("S :", res);
        res.forEach(orep => {
                $("#" + orep.id).show();
            })
        }, 
        error: function() {
                    console.log("Erreur lors de la récupération des machines");
                    console.log("Response :", xhr.responseText); 
        console.log("Status :", status);
        console.log("Error :", error);
                }
    })
    $.ajax({
    url: "ajax.php",
    type: "GET",
    data: {
        action: "filtrerDispodispa",
        date: date
    },
    dataType: "json",

    success: function(res){
        console.log("SUCCESS :", res);
        res.forEach(orep => {
                $("#" + orep.id).hide();
            })
    },

    error: function(xhr, status, error){
        console.log("ERREUR AJAX");
        console.log("Response :", xhr.responseText); 
        console.log("Status :", status);
        console.log("Error :", error);
    }
});

}
/// appelle la fonction dès changement dans les forms 
$("#rechercheMachine").on("change", filtrerReserv);
$("#rechercheDispo").on("change", filtrerDispo);
$("input[name='machines[]']").on("change", filtrerReserv);
$("input[name='outil[]']").on("change", filtrerEmprunts);
$("#rechercheEmprunt").on("change", filtrerEmprunts);


})
</script>