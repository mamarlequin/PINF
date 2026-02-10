<?php
// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
	header("Location:../index.php?view=machines");
	die("");
}

if(!isset($_SESSION["idUser"])){
    header("Location:../index.php?view=login");
    die("");
}
?>
<div class="flex items-center mb-6">
<button id='add_form' class='bg-indigo-600 text-white px-5 py-2 rounded-3xl hover:bg-indigo-700 transition-all mr-2 shadow-sm active:scale-95' onclick='afficher_form()'>+</button>
  <input
  id = "rechercheMachine"
    type="text"
    name="recherche"
    placeholder="Entrez votre recherche..."
    class="h-10 px-4 py-2 border border-gray-300 !rounded-l-full focus:outline-none focus:ring-2 focus:ring-gray-300"
  >

  <button
    class="h-10 px-4 py-2 border border-gray-300 border-l-0 rounded-r-full hover:bg-indigo-600 !text-white "
  >
		<svg
		class="w-4 h-4 text-gray-600"
		viewBox="0 0 24 24"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
		>
		<circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
		<line x1="16.65" y1="16.65" x2="22" y2="22"
				stroke="currentColor" stroke-width="2"/>
		</svg>
  </button>
</div>
<div id="resultats"></div>


<?php
if (isAdmin($_SESSION["idUser"])){
echo "<div id='hid' class=groupe1 style='display:none;'>";
mkForm("controleur.php");

echo "Entrez le nom du nouvel équipement : ";
mkInput("text", "nom");


echo "Entrez le type du nouvel équipement : ";
mkInput("text", "type");

echo "Entrez la description du nouvel équipement : ";
mkInput("text", "description", "");
echo "Entrez les risques du nouvel équipement : ";
mkInput("text", "risques", "");
mkInput("submit", "action", "Créer le nouvel équipement");
echo "</div>";
endForm();

}
$machines = lister_machine()?: [];
foreach($machines as $machine){
    $isMaintenance = ($machine["enMaintenance"] == 1);
    ?>

<div id="<?= $machine["id"] ?>" class="bg-white border border-slate-200 rounded-2xl p-6 mb-6 shadow-sm hover:shadow-md transition-shadow relative">
    
    <div class="flex justify-between items-start mb-4">
        <div>
            <h3 class="text-2xl font-bold text-indigo-600 mb-2"><?= $machine["nom"] ?></h3>
            <?php
            if($isMaintenance){
                ?>
                <span class="text-sm font-medium px-2.5 py-0.5 rounded-full bg-red-100 text-red-700">
                EN MAINTENANCE
                </span>
            <?php } ?>
        </div>

        <?php if (isAdmin($_SESSION["idUser"])): ?>
            <form action="controleur.php" method="POST" onsubmit="return confirm('Supprimer cette machine ?');">
                <input type="hidden" name="id" value="<?= $machine["id"] ?>">
                <button type="submit" name="action" value="-" class="text-slate-400 uppercase hover:text-red-500 transition-colors p-2">
                    x
                </button>
            </form>
        <?php endif; ?>
    </div>

    <div class="mb-6">
        <p class="text-slate-500 italic mb-2"><?= $machine["type"] ?></p>
        <p class="text-slate-700 mb-4"><?= $machine["description"] ?></p>
        
        <?php if (!empty($machine["risque"])): ?>
            <span class="text-sm font-bold uppercase text-red-600 tracking-wide">Risque : <?= $machine["risque"] ?></span>
        <?php endif; ?>
    </div>

    <div class="flex flex-wrap gap-3 items-center border-t border-slate-100 pt-4">
        
        <button class='add_form flex-1 sm:flex-none bg-indigo-600 text-white px-5 py-2 rounded-xl hover:bg-indigo-700 transition-all font-medium' data-id='<?=$machine["id"]?>' onclick='afficher_com(<?=$machine["id"]?>)'>
            Voir les commentaires
        </button>

		

        <?php if (isAdmin($_SESSION["idUser"])): ?>
            <form action='controleur.php' method='POST' class="flex-1 sm:flex-none">
                <input type='hidden' name='dest' value='machines'>
                <input type='hidden' name='id_equip' value='<?=$machine['id']?>'>
                <input type='hidden' name='etat_actuel' value='<?=$machine['enMaintenance']?>'>
                <button type='submit' name='action' value='Changer Maintenance' class='w-full text-white px-5 py-2  rounded-xl font-medium transition-colors <?=!$isMaintenance ? 'bg-orange-500 hover:bg-orange-600' : 'bg-indigo-600 hover:bg-indigo-700'?>'>
                    <?= $isMaintenance ? 'Afficher comme fonctionnel' : 'Afficher comme en maintenance' ?>
                </button>
            </form>
        <?php endif; ?>

    </div>
<div id='com-<?=$machine["id"]?>' style='display:none; position:relative'>
		<?php
$commentaires = lister_com($machine['id']) ?? [];

if ($commentaires == []){?>
	<BR>
	<p class="text-slate-700 mb-2 italic ">
        Aucun Commentaire
    </p><?php
}

foreach ($commentaires as $commentaire): ?>

<br>

<div class="bg-gray-100 rounded-lg p-4 relative">

    <!-- Nom et prénom -->
    <div class="flex items-center justify-between">
        <p class="text-slate-700 font-bold">
            <?= htmlspecialchars($commentaire["nom"]) ?> - <?= htmlspecialchars($commentaire["prenom"]) ?>
        </p>

        <!-- Date -->
        <p class="text-slate-700 italic text-sm">
            <?= htmlspecialchars($commentaire["dateDebut"]) ?>
        </p>
    </div>

    <!-- Statut + Bouton Résolu -->
    <div class="flex items-center gap-2 mt-2">
        <?php if ($commentaire["resolu"] == 0){ ?>
            <span class="text-sm font-bold uppercase text-red-600 tracking-wide">
                NON RESOLU
            </span>
            <form method="post" action="controleur.php">
                <input type="hidden" name="id" value="<?= $commentaire['id'] ?>">
				<?php if(isAdmin($_SESSION["idUser"])){ ?>
                <input type="submit" value="Marquer comme résolu" name="action"
                        class="!text-xs !text-gray-700 !bg-gray-200 hover:bg-gray-300 !px-2 !py-1 !rounded">
                    
		</input>
		<?php } ?>
            </form>
        <?php }else{ ?>
            <span class="text-sm font-bold uppercase text-green-600 tracking-wide">
                RESOLU
            </span>
			<form method="post" action="controleur.php">
                <input type="hidden" name="id" value="<?= $commentaire['id'] ?>">
				<?php if(isAdmin($_SESSION["idUser"])){ ?>
                <input type="submit" value="Marquer comme non résolu" name="action"
                        class="!text-xs !text-gray-700 !bg-gray-200 hover:bg-gray-300 !px-2 !py-1 !rounded">
                    
				</input>
				<?php } ?>
            </form>
        <?php } ?>
    </div>

    <!-- Contenu du commentaire -->
    <p class="mt-2 text-sm text-slate-600 tracking-wide">
        <?= htmlspecialchars($commentaire["contenu"]) ?>
    </p>

</div>
<?php endforeach; ?>


</div>

</div>


<?php } ?>

<script>
function afficher_form() {
    $("#hid").slideToggle(500);
}

function afficher_com(id){
	
	if ($("#com-" + id).css("display") === "block") {
		$("#com-" + id).css("display", "none");
	}else  $("#com-" + id).css("display", "block");

}

$(document).ready(function(){
    $("#resultats").on("click", ".add_form", function()
    {
        var id = $(this).data("id"); 

        $.ajax({
            type: "GET",
            url: "ajax.php",
            data: { "action": "afficher_com", "id": id },
            dataType: "json",
            success: function(oRep) {
                $("#comment").html(""); 
                oRep.forEach(element => {
                    $("#comment").append("Nom : " + element.Utilisateur.prenom + "<br>");
                });
            },
            error: function() {
                console.log("Erreur lors de la récupération des machines");
            }
        });
    });

    $("#rechercheMachine").on("keyup", function () {
        var titre = $(this).val() || "";
		
		$.ajax({
            type: "GET",
            url: "ajax.php",
            data: {"action": "disparaitre", "mot": titre},
            dataType: "json",
            success: function(oRep){
                oRep.forEach(element => {
                    $("#" + element.id).css("display", "block");
                });
            },
            error: function(){
                console.log("Erreur lors de la récupération des machines");
            },  
        });


        $.ajax({
            type: "GET",
            url: "ajax.php",
            data: {"action": "search", "mot": titre},
            dataType: "json",
            success: function(oRep){
                oRep.forEach(element => {
                    $("#" + element.id).css("display", "none");
                });
            },
            error: function(){
                console.log("Erreur lors de la récupération des machines");
            },  
        });
    })
});


</script>
