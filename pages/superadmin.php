<?php
// On vérifie le rôle directement en session
if (!isset($_SESSION["role"]) || $_SESSION["role"] != 2) {
    echo '<script>window.location.href="index.php?view=main";</script>';
    die();
}

// On récupère les utilisateurs (Admins et Étudiants)
$utilisateurs = parcoursRs(getUtilisateursGestion());

if (!$utilisateurs) {
    $utilisateurs = array();
}
?>

<div class="max-w-5xl mx-auto mt-10 p-6 bg-white rounded-2xl shadow-sm border">
    <h1 class="text-2xl font-bold text-indigo-900 mb-6">Gestion des privilèges (Super Administration)</h1>

    <table class="w-full text-left">
        <thead>
            <tr class="border-b text-slate-500">
                <th class="py-3 px-2">Utilisateur</th>
                <th class="py-3 px-2">Rôle actuel</th>
                <th class="py-3 px-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (is_array($utilisateurs) && count($utilisateurs) > 0): ?>
                <?php foreach($utilisateurs as $u): ?>
                    <tr class="border-b hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-2 font-medium"><?= $u['prenom'] ?> <?= $u['nom'] ?></td>
                        <td class="py-4 px-2">
                            <span class="px-2 py-1 rounded-full text-xs <?= $u['role'] == 1 ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' ?>">
                                <?= $u['role'] == 1 ? 'Admin' : 'Étudiant' ?>
                            </span>
                        </td>
                        <td class="py-4 px-2 flex gap-2">
                            
                            <?php if ($u['role'] == 0): ?>
                                <form action="controleur.php" method="POST">
                                    <input type="hidden" name="idCible" value="<?= $u['id'] ?>">
                                    <button type="submit" name="action" value="Promouvoir Admin" class="text-xs bg-indigo-600 text-white px-3 py-1.5 rounded hover:bg-indigo-700">
                                        Rendre Admin
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if ($u['role'] == 1): ?>
                                <form action="controleur.php" method="POST" onsubmit="return confirm('Voulez-vous vraiment retirer les droits Admin de <?= $u['prenom'] ?> ?');">
                                    <input type="hidden" name="idCible" value="<?= $u['id'] ?>">
                                    <button type="submit" name="action" value="Rendre Etudiant" class="text-xs bg-slate-500 text-white px-3 py-1.5 rounded hover:bg-slate-600">
                                        Rendre Étudiant
                                    </button>
                                </form>

                                <form action="controleur.php" method="POST" onsubmit="return confirm('Attention : Vous allez transférer TOUS vos pouvoirs à <?= $u['prenom'] ?> et redevenir simple Admin. Confirmer ?');">
                                    <input type="hidden" name="idCible" value="<?= $u['id'] ?>">
                                    <button type="submit" name="action" value="Transfert SuperAdmin" class="text-xs bg-red-600 text-white px-3 py-1.5 rounded hover:bg-red-700">
                                        Rendre SuperAdmin
                                    </button>
                                </form>

                                <button onclick="ouvrirDelegation(<?= $u['id'] ?>, '<?= $u['prenom'] ?>')" class="text-xs bg-orange-500 text-white px-3 py-1.5 rounded hover:bg-orange-600">
                                    Déléguer
                                </button>
                            <?php endif; ?>

                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="py-10 text-center text-slate-400">
                        Aucun utilisateur (étudiant ou admin) trouvé dans la base.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function ouvrirDelegation(id, prenom) {
    let mins = prompt("Pendant combien de minutes voulez-vous déléguer vos pouvoirs à " + prenom + " ?");
    if (mins && !isNaN(mins) && mins > 0) {
        window.location.href = `controleur.php?action=Delegation&idCible=${id}&duree=${mins}`;
    } else if (mins != null) {
        alert("Veuillez saisir un nombre de minutes valide.");
    }
}
</script>