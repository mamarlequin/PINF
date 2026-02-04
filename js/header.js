//sélectionne toutes les balises <a> qui ont la classe lien-protege
//et pour chaque lien sélectionné, on attache un événement
document.querySelectorAll("a.lien-protege").forEach(function(lien) {
	//On ajoute un écouteur d'événement sur le clic
	lien.addEventListener("click", function(event) {
		//Vérifie si l’utilisateur n’est pas connecté
		if (!utilisateurEstConnecte) {
			event.preventDefault(); // Empêche d'aller sur la page cible
			// Redirige vers la page de connexion avec un message
			window.location.href = "index.php?view=login&msg=Vous devez être connecté pour accéder à cette page.";
		}
	});
});