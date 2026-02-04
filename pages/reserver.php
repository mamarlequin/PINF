<?php
// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
	header("Location:../index.php?view=reserver");
	die("");
}
?>


<script type="text/javascript">

var planningAdmin = {};
var planningReservations = {};
var machines = [];

var offsetPage = 0;

function getLundiDeCetteSemaine() {
    var d = new Date();

    d.setDate(d.getDate() - d.getDay() + (d.getDay() == 0 ? -6 : 1));

    return d;
}

function estDansIntervalle(dateCaseCle, dateDebutFull, dateFinFull) {
    // Sécurité : si les dates sont absentes, on retourne faux
    if (!dateDebutFull || !dateFinFull) return false;

    var dStart = dateDebutFull.split(' ')[0];
    var dEnd = dateFinFull.split(' ')[0];
    
    return (dateCaseCle >= dStart && dateCaseCle <= dEnd);
}

function chargerSemaine(offset) {
    offsetPage = offset;
    
    var lundi = getLundiDeCetteSemaine();
    lundi.setDate(lundi.getDate() + (offset * 7));
    
    const moisAnnee = lundi.toLocaleDateString('fr-FR', {month: 'short', year: 'numeric'}).toUpperCase();
    const dateLundiSQL = lundi.toISOString().split('T')[0];

    var html = afficherTete(lundi, moisAnnee);
    
    machines.forEach(m => {
        html += afficherLigne(m, lundi);
    });
    
    $('#calendrier-container').html(html);
}

function afficherTete(lundiRef, titre) {
    const jours = ['LUN', 'MAR', 'MER', 'JEU', 'VEN', 'SAM'];
    var header = `
    <div class="flex border-b border-gray-400 text-xs font-bold text-gray-500 uppercase">
        <div class="w-56 p-4  border-r border-gray-400 flex items-center justify-between bg-gray-50">
            <button onclick="chargerSemaine(${offsetPage - 1})" class="hover:bg-gray-200 p-1 px-2 rounded border border-gray-400"><</button>
            <span class="text-gray-700">${titre}</span>
            <button onclick="chargerSemaine(${offsetPage + 1})" class="hover:bg-gray-200 p-1 px-2 rounded border border-gray-400">></button>
        </div>`;

    var jour = new Date(lundiRef);
    for (var i = 0; i < 6; i++) {
        var cle = jour.toISOString().split('T')[0];

        header += `
        <div class="flex-1 p-4 text-center border-r border-gray-400 last:border-r-0"> 
            ${jours[i]} ${jour.getDate()} 
            <div class="mt-2 flex flex-col gap-1"> 
                ${afficherAdmins(cle)} 
            </div> 
        </div>`;
        jour.setDate(jour.getDate() + 1);
    }
    return header + `</div>`;
}

function afficherLigne(m, lundiRef) {
    var ligne = `<div class="flex border-b border-gray-400 h-auto min-h-[70px]">
        <div class="w-56 p-4 border-r border-gray-400 flex items-center font-bold text-gray-700 text-xs uppercase bg-white">${m.nom}</div>`;

    var jour = new Date(lundiRef);
    for (var i = 0; i < 6; i++) {
    
        var cle = jour.toISOString().split('T')[0];
        
        var bgClass = m.enMaintenance ? "bg-stripes" : (!planningAdmin[cle] ? "bg-gray-200" : "hover:bg-blue-50 cursor-pointer");
        var clickAction = (planningAdmin[cle] && !m.enMaintenance) ? 'onclick="reserverMachine(${m.id}, \'${cle}\')"' : "";

        ligne += `
        <div class="flex-1 p-2 border-r border-gray-400 flex flex-col gap-2 ${bgClass}" ${clickAction}>
            ${afficherRes(m.id, cle)}
            ${afficherEmprunts(m.id, cle)}
        </div>`;
        jour.setDate(jour.getDate() + 1);
    }
    return ligne + "</div>";
}

function afficherAdmins(cle) {
    if (!planningAdmin[cle]) return '<span class="text-[9px] text-red-400 italic">Fermé</span>';
    var rep = "";
    for(m of planningAdmin[cle])
    {
        rep += `
        <span class="text-[9px] bg-green-100 text-green-700 px-1 py-0.5 rounded border border-green-200">
           ${m.prenom}: ${m.debut}-${m.fin}
        </span>
    `;
    }
    return rep;
}

function afficherRes(mId, dateCle) {
    if (!planningReservations[mId]) return "";
    var rep = "";
    for (var r of planningReservations[mId]) {
        if (estDansIntervalle(dateCle, r.dateDebut, r.dateFin)) {
            var hover = "cursor-default";
            if (r.idUser == <?php echo valider("idUser", "SESSION") ?>) {
                hover = "hover:bg-indigo-600 cursor-pointer";
            }

            var texte = "";
            var dStart = r.dateDebut.split(' ')[0];
            var dEnd = r.dateFin.split(' ')[0];

            if (dStart === dEnd) {
                texte = r.debut + " - " + r.fin;
            } else if (dateCle === dStart) {
                texte = "Début : " + r.debut;
            } else if (dateCle === dEnd) {
                texte = "Fin : " + r.fin;
            } else {
                texte = "Réservation en cours...";
            }

            rep += `
            <div class="bg-indigo-500 text-white text-[10px] py-2 px-1 rounded shadow-sm ${hover} text-center mb-1">
                ${texte}
            </div>`;
        }
    }
    return rep;
}

function afficherEmprunts(mId, dateCle) {
    if (!planningEmprunts[mId]) return "";
    var rep = "";

    for (var e of planningEmprunts[mId]) {
        if (estDansIntervalle(dateCle, e.dateDebut, e.dateFin)) {
            
            var hover = "cursor-default";
            if (e.idUser == <?php echo valider("idUser", "SESSION") ?>) {
                hover = "hover:bg-green-600 cursor-pointer";
            } 

            var texte = "Emprunt";
            var dStart = e.dateDebut.split(' ')[0];
            var dEnd = e.dateFin.split(' ')[0];
            
            if (dStart === dEnd) {
                texte = e.heureDebut + " - " + e.heureFin;
            } else if (dateCle === dStart) {
                texte = "Début : " + e.heureDebut;
            } else if (dateCle === dEnd) {
                texte = "Fin : " + e.heureFin;
            }

            rep += `
            <div class="bg-green-500 text-white text-[10px] py-2 px-1 rounded shadow-sm ${hover} text-center mb-1">
                ${texte}
            </div>`;
        }
    }
    return rep;
}



$(document).ready(function() {
    const $popup = $("#pop-reservation");
    var caseCliquee = null;

    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: {"action": "lister_machines"},
        dataType: "json",
        success: function(oRep){
            machines = oRep;

            $.ajax({
                type: "POST",
                url: "ajax.php",
                data: {"action": "lister_dispo"},
                dataType: "json",
                success: function(oRep){
                    planningAdmin = oRep;

                    $.ajax({
                        type: "POST",
                        url: "ajax.php",
                        data: {"action": "lister_res"},
                        dataType: "json",
                        success: function(oRep){
                            planningReservations = oRep;
                            $.ajax({
                                type: "POST",
                                url: "ajax.php",
                                data: {"action": "lister_emprunts"},
                                dataType: "json",
                                success: function(oRep){
                                    planningEmprunts = oRep;
                                    chargerSemaine(0);
                                },
                                error: function(){
                                    console.log("Erreur lors de la récupération des emprunts");
                                },    
                            });  
                        },
                        error: function(){
                            console.log("Erreur lors de la récupération des dispos");
                        },	
                    });
                },
                error: function(){
                    console.log("Erreur lors de la récupération des dispos");
                },	
            });
        },
        error: function(){
            console.log("Erreur lors de la récupération des machines");
        },	
    });

    $(".case-libre").on("click", function(e) {

        e.stopPropagation();

        if(caseCliquee == this){
            return;
        }

        caseCliquee = this;

        $("#debut").val("");
        $("#fin").val("");

        

        const idMachine = $(this).data("machine");
        const date = $(this).data("date");

        $("#form-machine").val(idMachine);
        $("#form-date").val(date);
        $("#info-res").text("Machine " + idMachine + " le " + date);

        $popup.css({
            top: e.pageY + 10 + "px",
            left: e.pageX + 10 + "px"
        }).removeClass("hidden");
    });

    $popup.on("click", function(e) {
        e.stopPropagation();
    });

    $(document).on("click", function() {
        $popup.addClass("hidden");
        caseCliquee = null;
    });
});
</script>


<div id="calendrier-container" class="max-w-full bg-white border border-gray-400 rounded-lg shadow-sm overflow-hidden font-sans select-none">
</div>


<div id="pop-reservation" class="hidden absolute z-[100] bg-white border border-gray-300 shadow-xl rounded-lg p-4 w-64 border-t-4 border-t-indigo-600">
    <h3 class="font-bold text-indigo-600 mb-2">Réserver</h3>
    <p id="info-res" class="text-xs text-gray-500 mb-4"></p>
    
    <form action="controleur.php" method="POST" class="space-y-3">
        <input type="hidden" name="id_machine" id="form-machine">
        <input type="hidden" name="date_res" id="form-date">
        
        <div>
            <label class="block text-[10px] uppercase font-bold text-gray-400">Heure début</label>
            <input type="time" id="debut" name="debut" class="w-full border rounded px-2 py-1 text-sm" required>
        </div>
        <div>
            <label class="block text-[10px] uppercase font-bold text-gray-400">Heure fin</label>
            <input type="time" id="fin" name="fin" class="w-full border rounded px-2 py-1 text-sm" required>
        </div>

        <button type="submit" name="action" value="reserver" class="w-full bg-indigo-600 text-white py-2 rounded text-sm hover:bg-indigo-700">
            Valider
        </button>
    </form>
</div>