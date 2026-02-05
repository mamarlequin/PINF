<?php
// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
	header("Location:../index.php?view=reserver");
	die("");
}

if(!isset($_SESSION["idUser"])){
    header("Location:../index.php?view=login");
    die("");
}
?>


<script type="text/javascript">

var planningAdmin = {};
var planningReservations = {};
var machines = [];
var $popup;

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

    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: {
            "action": "charger_donnees_semaine",
            "offset": offset
        },
        dataType: "json",
        success: function(data) {
            machines = data.machines;
            planningAdmin = data.planningAdmin;
            planningReservations = data.planningReservations;
            planningEmprunts = data.planningEmprunts;

            var lundi = new Date(data.lundi);
            const moisAnnee = lundi.toLocaleDateString('fr-FR', {month: 'short', year: 'numeric'}).toUpperCase();

            var html = afficherTete(lundi, moisAnnee);
            machines.forEach(m => {
                html += afficherLigne(m, lundi);
            });
            $('#calendrier-container').html(html);
        }
    });

    
}

function afficherTete(lundiRef, titre) {
    const jours = ['LUN', 'MAR', 'MER', 'JEU', 'VEN', 'SAM'];
    var header = `
    <div class="flex border-t border-slate-200 bg-white top-0 z-10 ">
        <div class="w-56 p-4 border border-slate-200 flex items-center justify-between bg-white">
            <button onclick="chargerSemaine(${offsetPage - 1})" class="hover:bg-indigo-50 text-indigo-600 p-2 rounded-full transition-colors font-bold">←</button>
            <span class="text-xs font-black tracking-widest text-black-400">${titre}</span>
            <button onclick="chargerSemaine(${offsetPage + 1})" class="hover:bg-indigo-50 text-indigo-600 p-2 rounded-full transition-colors font-bold">→</button>
        </div>`;

    var jour = new Date(lundiRef);
    for (var i = 0; i < 6; i++) {
        var cle = jour.toISOString().split('T')[0];

        header += `
        <div class="flex-1 p-4 text-center border-r border-gray-400 border border-slate-200 last:border-r-0"> 
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
    var ligne = `<div class="flex border border-slate-200 group">
        <div class="w-56 p-4 border-r border-slate-200 flex items-center bg-white transition-colors">
            <div>
                <div class="text-xs font-bold text-slate-800 uppercase leading-tight">${m.nom}</div>
            </div>
        </div>`;

    var jour = new Date(lundiRef);
    for (var i = 0; i < 6; i++) {
        var cle = jour.toISOString().split('T')[0];

        
        var bgClass = m.enMaintenance ? "bg-stripes" : (!planningAdmin[cle]? "bg-slate-200/50" : "bg-white hover:bg-indigo-100/50 calendar-case cursor-pointer");
        var clickAction = (planningAdmin[cle] && !m.enMaintenance) ? `onclick="reserverMachine(${m.id}, '${cle}',event)" ` : "";

        ligne += `
        <div data-nom="${m.nom}" class="flex-1 p-2 border-r border-slate-100 last:border-r-0 flex flex-col gap-1.5 min-h-[85px] ${bgClass}" ${clickAction}>
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
                hover = "hover:bg-indigo-700 cursor-pointer";
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
            <div class="bg-indigo-600 text-white text-[9px] font-bold py-1.5 px-2 rounded-lg shadow-sm ${hover} flex items-center justify-center">
                <span>${texte}</span>
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
            <div class="bg-green-500  text-white text-[9px] font-bold py-1.5 px-2 rounded-lg shadow-sm ${hover} flex items-center justify-center">
                ${texte}
            </div>`;
        }
    }
    return rep;
}

function reserverMachine(id, date, e) {
    e.stopPropagation();

    $("#debut").val("");
    $("#fin").val("");
    $("#form-machine").val(id);
    $("#form-date").val(date);
    
    var nom = $(e.currentTarget).attr("data-nom");
    $("#info-res").text((nom ? nom : "Machine " + id) + " le " + date);

    $popup.removeClass("hidden"); 

    var popupHeight = $popup.outerHeight();
    var popupWidth = $popup.outerWidth();

    var left = e.pageX + 20;

    var top = e.pageY + 10;

    if ((top + popupHeight) - ($(window).scrollTop() + $(window).height()) > 0) {
        top = top - ((top + popupHeight) - ($(window).scrollTop() + $(window).height())) - 10;
    }

    if (left < 10) left = 10;

    $popup.css({ 
        top: top + "px", 
        left: left + "px" 
    });
}

$(document).ready(function() {
    $(document).on("keydown", function(e) {
        if (e.key === "Escape") {
            $popup.addClass("hidden");
            caseCliquee = null;
        }
    });


    $popup = $("#pop-reservation");

    chargerSemaine(0);

    

    $popup.on("click", function(e) {
        e.stopPropagation();
    });

    $(document).on("click", function() {
        $popup.addClass("hidden");
        caseCliquee = null;
    });
});
</script>


<div id="calendrier-container" class="max-w-full bg-whiterounded-lg shadow-sm overflow-hidden font-sans select-none my-18">
    <!-- Le calendrier sera chargé ici -->
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