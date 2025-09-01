/* Mise en place d'un script JS pour afficher dynamiquement les différents formulaires et listes utiles directement sur le tableau de bord de l'administrateur sans passer par un changement de page. */

document.addEventListener('DOMContentLoaded', function() {

    const content = document.getElementById('admin-content');

    // Récupération de tous les liens avec l'attribut data-load 

    document.querySelectorAll("a[data-load]").forEach(link => {
        link.addEventListener("click", function (event) {
            event.preventDefault(); // empêche le changement de page
            const url = this.getAttribute("href");

            // Chargement du contenu à afficher via fetch
            fetch(url).then(response => {
                if(!response.ok) {
                    throw new Error("Erreur HTTP" + response.status);
                }
                return response.text(); // Convertit la réponse HTTP en texte brut (ici du HTML)
            }) // Quand la conversion est terminée, on récupère le HTML
            .then(html => { 
                content.innerHTML = html; // On insère le HTML dans la zone #admin-content
            }) // Si une erreur survient pendant le fetch ou le rendu
            .catch(err => {
                
                content.innerHTML = `<p>Erreur: ${err.message}</p>`; // On affiche le message d'erreur dans #admin-content
            })
        
        });
    });
});