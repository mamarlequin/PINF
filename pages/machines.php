<?php
// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
	header("Location:../index.php?view=machines");
	die("");
}


$machines = lister_machine()?: [];

foreach($machines as $machine){
echo "<div class='groupe1 relative'>";
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
	echo "</div>";
	echo "<BR>";
}


if (isAdmin($_SESSION["idUser"])){
echo "<button id='add_form' class='bg-indigo-600 text-white px-5 py-2 rounded-3xl hover:bg-indigo-700 transition-all shadow-sm active:scale-95' onclick='afficher_form()'>+</button>";
echo "<div id='hid' class=groupe1>";
mkForm("controleur.php");

echo "Entrez le nom du nouvel équipement : ";
mkInput("text", "nom");


echo "Entrez le type du nouvel équipement : ";
mkInput("text", "type");

echo "Entrez la description du nouvel équipement : ";
mkInput("text", "description");
echo "Entrez les risques du nouvel équipement : ";
mkInput("text", "risques");
mkInput("submit", "action", "Créer le nouvel équipement");
echo "</div>";
endForm();
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

</script>
