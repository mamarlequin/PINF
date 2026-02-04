<?php
// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
	header("Location:../index.php?view=machines");
	die("");
}
?>
<div class="flex items-center">
	
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
echo "<button id='add_form' class='bg-indigo-600 text-white px-5 py-2 rounded-3xl hover:bg-indigo-700 transition-all shadow-sm active:scale-95' onclick='afficher_form()'>+</button>";
echo "<div id='hid' class=groupe1>";
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
	
echo "<div id='" . $machine["id"] . "' class='groupe1 relative'>";
if (isAdmin($_SESSION["idUser"])){
echo "<div class='absolute right-10'>";
mkForm("controleur.php");
echo "<input type='submit' name='action' value='-' class='!bg-indigo-600 !text-white !px-5 !py-2 !rounded-3xl !hover:bg-indigo-700 !shadow-sm !active:scale-95'>";
mkInput("hidden", "id", $machine["id"]);
endForm();
echo "</div>";
}
	//echo "<div class='styled-button' style='background-color:indigo; color:white; box-sizing: border-box; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);border-radius: 30px;'>";
	echo  "<h3 class='indent-2 text-2xl font-bold  text-indigo-600  rounded-3xl '>" . $machine["nom"]  . "</h3> ";
	//echo "</div>";
	echo "<p class='indent-2 break-keep'>" . $machine["type"] . "<BR>";
	echo "<div class='indent-2 text-gray-500 break-keep'>" . $machine["description"] . "</div> <BR>";
	echo "<div class='inline-block border-2 bg-red-500 border-red-500 text-white px-5 py-2 rounded-3xl'>";
	echo $machine["risque"];
	echo "</div>";
	echo "<BR>";
echo "<div id='comment' data-id='". $machine["id"] ."'class='flex justify-center'>";
echo "<button class='add_form bg-indigo-600 text-white px-5 py-2 rounded-3xl hover:bg-indigo-700 transition-all shadow-sm active:scale-95' data-id='" . $machine["id"] . "'>Voir les commentaires</button>";

echo "</div>";
echo "</div>";
echo "<BR>";
}



?>

<script>
function afficher_form() {
let hid = document.getElementById("hid");

if (hid.style.display == "block"){
	hid.style.display = "none";
}else {
    hid.style.display = "block";
}
}

$(document).ready(function(){
$("#resultats").on("click", ".add_form", function()
 {
    var id = $(this).data("id"); // récupère le data-id

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
