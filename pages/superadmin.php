<?php

if (!isset($_SESSION["role"]) || $_SESSION["role"] != 2) {
    echo '<script>window.location.href="index.php?view=main";</script>';
    die();
}


$utilisateurs = parcoursRs(getUtilisateursGestion());

if (!$utilisateurs) {
    $utilisateurs = array();
}
?>
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
    <h1 class="text-2xl font-bold text-indigo-900 mb-6">Gestion des privilèges (Super Administration)</h1>

    <table class="w-full text-left">
        <thead>
            <tr class="border-b text-slate-500">
                <th class="py-3 px-2">Utilisateur</th>
                <th class="py-3 px-2">Rôle actuel</th>
                <th class="py-3 px-2 text-center">Actions de Rôle</th>
                <th class="py-3 px-2 text-right">Délégation Temporaire</th>
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
                                <?php if ($u['role'] == 0): ?>
                                    <form action="controleur.php" method="POST">
                                        <input type="hidden" name="idCible" value="<?= $u['id'] ?>">
                                        <button type="submit" name="action" value="Promouvoir Admin" class="text-xs bg-indigo-600 text-white px-3 py-1.5 rounded hover:bg-indigo-700">
                                            Rendre Admin
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($u['role'] == 1): ?>
                                    <form action="controleur.php" method="POST" onsubmit="return confirm('Voulez-vous vraiment retirer les droits Admin ?');">
                                        <input type="hidden" name="idCible" value="<?= $u['id'] ?>">
                                        <button type="submit" name="action" value="Rendre Etudiant" class="text-xs bg-slate-500 text-white px-3 py-1.5 rounded hover:bg-slate-600">
                                            Rendre Étudiant
                                        </button>
                                    </form>

                                    <form action="controleur.php" method="POST" onsubmit="return confirm('Attention : Transfert total des pouvoirs ! Confirmer ?');">
                                        <input type="hidden" name="idCible" value="<?= $u['id'] ?>">
                                        <button type="submit" name="action" value="Transfert SuperAdmin" class="text-xs bg-red-600 text-white px-3 py-1.5 rounded hover:bg-red-700">
                                            Rendre SuperAdmin
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>

                        <td class="py-4 px-2 text-right">
                            <?php if ($u['role'] == 1): ?>
                                <form action="controleur.php" method="POST" class="flex flex-col items-end gap-1">
                                    <input type="hidden" name="idCible" value="<?= $u['id'] ?>">
                                    
                                    <label class="text-[10px] text-slate-400 uppercase font-bold">Fin de délégation :</label>
                                    <div class="flex gap-1">
                                        <input type="datetime-local" 
                                               name="dateFinLabel" 
                                               class="text-xs border rounded px-1 py-1" 
                                               required
                                               min="<?= date('Y-m-d\TH:i'); ?>">
                                        
                                        <button type="submit" name="action" value="Delegation" class="text-xs bg-orange-500 text-white px-3 py-1.5 rounded hover:bg-orange-600">
                                            Déléguer
                                        </button>
                                    </div>
                                </form>
                            <?php else: ?>
                                <span class="text-xs text-slate-400 italic">Il doit être Admin pour lui déléguer</span>
                            <?php endif; ?>
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

<script>
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