<?php
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
    header("Location:../index.php?view=emprunts");
    die("");
}

if (!isset($_SESSION["idUser"])) {
    header("Location:../index.php?view=login");
    die("");
}










$outilsLibres = lister_outil_dispo();
$outilsOccupes = lister_emprunts_actifs();

?>

<div class="flex items-center mb-6">
    <input
        id="rechercheOutil"
        type="text"
        name="recherche"
        placeholder="Recherchez un outil..."
        class="h-10 px-4 py-2 border border-gray-300 !rounded-l-full focus:outline-none focus:ring-2 focus:ring-gray-300 text-sm w-64">

    <button
        class="h-10 px-4 py-2 border border-gray-300 border-l-0 rounded-r-full bg-white hover:bg-slate-50 transition-colors">
        <svg
            class="w-4 h-4 text-gray-500"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2" />
            <line x1="16.65" y1="16.65" x2="22" y2="22"
                stroke="currentColor" stroke-width="2" />
        </svg>
    </button>
</div>

<div class="max-w-6xl mx-auto mt-10 p-6 bg-white rounded-2xl shadow-sm border text-slate-800">
    <h1 class="text-2xl font-bold text-indigo-900 mb-6 border-b pb-4">Matériel disponible</h1>

    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="text-slate-500 text-sm uppercase tracking-wider border-b">
                <th class="py-3 px-4 font-semibold">Outil</th>
                <th class="py-3 px-4 font-semibold">Description</th>
                <th class="py-3 px-4 font-semibold text-center">Risque</th>
                <th class="py-3 px-4 font-semibold text-right">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($outilsLibres) && count($outilsLibres) > 0): ?>
                <?php foreach ($outilsLibres as $o): ?>
                    <tr id="outil-<?= $o['id'] ?>" class="border-b hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-4 font-medium"><?= $o['nom'] ?></td>
                        <td class="py-4 px-4 text-sm text-slate-600"><?= $o['description'] ?></td>
                        <td class="py-4 px-4 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border 
                                <?= $o['risque'] == 'Élevé' ? 'bg-red-50 text-red-600 border-red-200' : 'bg-green-50 text-green-600 border-green-200' ?>">
                                <?= $o['risque'] ?>
                            </span>
                        </td>
                        <td class="py-4 px-4 text-right">
                        <td class="py-4 px-4 text-right text-sm">
                            <form action="controleur.php" method="POST" class="flex flex-col items-end gap-2">
                                <input type="hidden" name="id_outil" value="<?= $o['id'] ?>">

                                <div class="flex flex-col items-end">
                                    <label class="text-[10px] text-slate-400 uppercase font-bold mb-1">Date de retour :</label>
                                    <input type="date"
                                        name="dateFinLabel"
                                        class="text-xs border rounded px-2 py-1 focus:ring-1 focus:ring-indigo-500 outline-none"
                                        required
                                        value="<?= date('Y-m-d', strtotime('+1 day')); ?>"
                                        min="<?= date('Y-m-d'); ?>"
                                        max="<?= date('Y-m-d', strtotime('+7 days')); ?>">
                                </div>

                                <button type="submit" name="action" value="RealiserEmprunt"
                                    class="text-xs bg-indigo-600 text-white px-3 py-1.5 rounded hover:bg-indigo-700 transition-all active:scale-95 shadow-sm">
                                    Emprunter
                                </button>
                            </form>
                        </td>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="py-12 text-center text-slate-400 italic">
                        Aucun matériel disponible actuellement.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="max-w-6xl mx-auto mt-8 p-6 bg-slate-50 rounded-2xl border border-dashed border-slate-300">
    <h2 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-6">Emprunts en cours</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php if (isset($outilsOccupes) && count($outilsOccupes) > 0): ?>
            <?php foreach ($outilsOccupes as $e): ?>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div class="flex justify-between items-start mb-2">
                        <span class="font-bold text-indigo-900"><?= $e['nom'] ?></span>
                        <span class="text-[10px] bg-orange-100 text-orange-700 px-2 py-0.5 rounded-md font-bold">EMPRUNTE</span>
                    </div>
                    <div class="text-xs text-slate-500 mb-4">
                        Emprunteur : <span class="font-medium text-slate-700"><?= $e['prenom'] ?></span>
                    </div>
                    <div class="pt-3 border-t border-slate-100 flex justify-between items-center">
                        <span class="text-[10px] uppercase font-bold text-slate-400">Retour prévu</span>
                        <span class="text-sm font-bold text-orange-600"><?= date('d/m/Y', strtotime($e['dateRenduTheorique'])) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full py-4 text-center text-slate-400 text-sm italic">
                Aucun emprunt en cours.
            </div>
        <?php endif; ?>
    </div>
</div>


<script>
    $(document).ready(function() {

        $("#rechercheOutil").on("keyup", function() {

            var titre = $(this).val() || "";




            if (titre === "") {

                $("tr[id^='outil-']").show();

                return;

            }


            $.ajax({

                type: "GET",

                url: "ajax.php",

                data: {

                    "action": "rechercher_outil_ajax",

                    "mot": titre

                },

                dataType: "json",

                success: function(oRep) {



                    $("tr[id^='outil-']").hide();




                    if (oRep && oRep.length > 0) {

                        oRep.forEach(element => {

                            $("#outil-" + element.id).show();

                        });

                    }

                },

                error: function() {

                    console.log("Erreur lors de la recherche");

                }

            });

        });

    });
</script>