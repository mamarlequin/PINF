<?php
// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
	header("Location:../index.php?view=reserver");
	die("");
}
?>


<script type="text/javascript">

const machines = [
    {id: 1, nom: 'IMPRIMANTE 3D 1', enMaintenance: 0},
    {id: 2, nom: 'IMPRIMANTE 3D 2', enMaintenance: 0},
    {id: 3, nom: 'DECOUPE LASER 1', enMaintenance: 0},
    {id: 4, nom: 'DECOUPE LASER 2', enMaintenance: 1},
    {id: 5, nom: 'IMPRIMANTE RESINE 1', enMaintenance: 0},
    {id: 6, nom: 'IMPRIMANTE RESINE 2', enMaintenance: 1}
];

const planningAdmin = {
    "2026-02-02": [{debut: "09:00", fin: "12:00", idAdmin: 1}, {debut: "13:30", fin: "18:00", idAdmin: 1}],
    "2026-02-03": [{debut: "09:00", fin: "18:00", idAdmin: 2}],
    "2026-02-04": [{debut: "14:00", fin: "20:00", idAdmin: 1}],
    "2026-02-05": [{debut: "08:30", fin: "17:30", idAdmin: 2}],
    "2026-02-06": [{debut: "09:00", fin: "16:00", idAdmin: 1}]
};

const planningReservations = {
    1 : {"2025-12-01": [{debut: "10:30", fin: "12:30"}]}, 
};

var offsetPage = 0;

function getLundiDeCetteSemaine() {
    var d = new Date();

    d.setDate(d.getDate() - d.getDay() + (d.getDay() == 0 ? -6 : 1));

    return d;
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
            Admin ${m.idAdmin}: ${m.debut}-${m.fin}
        </span>
    `;
    }
    return rep;
}

function afficherRes(mId, dateCle) {
    if (!planningReservations[mId] || !planningReservations[mId][dateCle]) return "";
    var rep = "";
    for(r of planningReservations[mId][dateCle])
    {
        rep += `
        <div class="bg-indigo-500 text-white text-[10px] py-2 px-1 rounded shadow-sm text-center">
            ${r.debut} - ${r.fin}
        </div>
    `;
    }
    return rep;
}



$(document).ready(function() {
    const $popup = $("#pop-reservation");
    var caseCliquee = null;

    chargerSemaine(0);

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