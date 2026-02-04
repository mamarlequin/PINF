<?php
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
    header("Location:../index.php?view=compte");
    die("");
}


$nom = $_SESSION["nom"];
$prenom = $_SESSION["prenom"];
$idUser = $_SESSION["idUser"];


$estAdmin = isAdmin($idUser);
?>

<div class="container mx-auto p-6 max-w-4xl">
    <div class="bg-white shadow-lg rounded-3xl p-8 mb-8 flex justify-between items-center border border-gray-100">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"><?php echo "$prenom $nom"; ?></h1>
            <p class="text-indigo-600 font-medium">
                <?php echo ($estAdmin) ? "Administrateur du Fablab" : "Étudiant"; ?>
            </p>
        </div>
        <a href="controleur.php?action=Logout"
            class="bg-red-500 text-white px-6 py-2 rounded-3xl hover:bg-red-600 transition-all shadow-sm active:scale-95">
            Déconnexion
        </a>
    </div>

    <?php if ($estAdmin): ?>
        <div class="bg-white shadow-xl rounded-3xl p-8 border border-gray-100">
            <h2 class="text-2xl font-bold mb-6 text-indigo-600 flex items-center">
                <span class="mr-2">📅</span> Mes présences au Fablab
            </h2>

            <div class="space-y-4">
                <?php
                $mesDispos = lister_mes_dispos($idUser);
                if (!$mesDispos) {
                    echo "<p class='text-gray-500 italic'>Aucune présence enregistrée.</p>";
                } else {
                    foreach ($mesDispos as $d) {
                        // Découpage de la date pour les inputs
                        $dateJour = explode(" ", $d['dateDebut'])[0];
                        $heureD = substr(explode(" ", $d['dateDebut'])[1], 0, 5);
                        $heureF = substr(explode(" ", $d['dateFin'])[1], 0, 5);
                ?>

                        <form action="controleur.php" method="POST" class="flex flex-wrap items-center gap-4 p-4 border rounded-2xl hover:bg-gray-50 transition-colors">
                            <input type="hidden" name="id_creneau" value="<?php echo $d['id']; ?>">

                            <div class="flex-1 min-w-[150px]">
                                <label class="block text-[10px] uppercase text-gray-400 font-bold">Jour</label>
                                <input type="date" name="date_jour" value="<?php echo $dateJour; ?>" class="w-full border-none font-semibold p-0 focus:ring-0 bg-transparent">
                            </div>

                            <div>
                                <label class="block text-[10px] uppercase text-gray-400 font-bold">Début</label>
                                <input type="time" name="heure_debut" value="<?php echo $heureD; ?>" class="border-gray-200 rounded-lg text-sm p-1">
                            </div>

                            <div>
                                <label class="block text-[10px] uppercase text-gray-400 font-bold">Fin</label>
                                <input type="time" name="heure_fin" value="<?php echo $heureF; ?>" class="border-gray-200 rounded-lg text-sm p-1">
                            </div>

                            <div class="flex gap-2">
                                <button type="submit" name="action" value="Modifier Dispo" class="bg-indigo-100 text-indigo-600 p-2 rounded-xl hover:bg-indigo-200 transition-colors" title="Enregistrer">
                                    💾
                                </button>

                                <button type="submit" name="action" value="Supprimer Dispo" class="bg-red-100 text-red-600 p-2 rounded-xl hover:bg-red-200 transition-colors" title="Supprimer" onclick="return confirm('Supprimer ce créneau ?')">
                                    🗑️
                                </button>
                            </div>
                        </form>

                <?php
                    } 
                } 
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>