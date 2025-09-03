/* Mise en place d'un script JS pour afficher dynamiquement les différents formulaires et listes utiles directement sur le tableau de bord de l'administrateur sans passer par un changement de page. */

document.addEventListener('DOMContentLoaded', function() {

    const content = document.getElementById('admin-content');

    // Fonction pour attacher le submit AJAX à tous les formulaires injectés

    function attachFormAjaxHandlers() {
        const forms = content.querySelectorAll('form.admin-ajax-form');
        forms.forEach(form => {
            form.setAttribute('data-turbo', 'false');
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // empêche le submit classique

                const formData = new FormData(form);
                const url = form.getAttribute('action');

                // Vérifie qu'une URL est bien définie
                
                if (!url) {
                    console.error("Le formulaire n'a pas d'URL définie !");
                    return;
                }

                fetch(url, {
                    method: form.getAttribute('method'),
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error("Erreur HTTP " + response.status);
                    return response.text();
                })
                .then(html => {
                    content.innerHTML = html;
                    attachFormAjaxHandlers(); // ré-attache aux nouveaux formulaires injectés
                })
                .catch(err => {
                    console.error(err);
                });
            });
        });
    }

    // Récupération de tous les liens avec l'attribut data-load 

    document.querySelectorAll("a[data-load]").forEach(link => {
        link.addEventListener("click", function (event) {
            event.preventDefault(); // empêche le changement de page
            const url = this.getAttribute("href");

            // Chargement du contenu à afficher via fetch avec header AJAX pour afficher correctement la vue suivant la condition du RoomController.

            fetch(url, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest" // Ajout du header.
                }
            }).then(response => {
                if(!response.ok) {
                    throw new Error("Erreur HTTP" + response.status);
                }
                return response.text(); // Convertit la réponse HTTP en texte brut (ici du HTML)
            }) // Quand la conversion est terminée, on récupère le HTML
            .then(html => { 
                content.innerHTML = html; // On insère le HTML dans la zone #admin-content
                attachFormAjaxHandlers();
            }) // Si une erreur survient pendant le fetch ou le rendu
            .catch(err => {
                
                content.innerHTML = `<p>Erreur: ${err.message}</p>`; // On affiche le message d'erreur dans #admin-content
            })
        
        });
    });
});