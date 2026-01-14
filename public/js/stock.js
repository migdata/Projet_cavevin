// Fichier : public/js/stock.js

document.addEventListener('DOMContentLoaded', function() {
    console.log("Système de gestion de stock ");

    // 1. On repère tous les boutons + et -
    const buttons = document.querySelectorAll('.btn-action');

    // 2. Qui a cliqué sur quoi ?
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            // Récupération des infos stockées dans les attributs HTML du bouton
            let productId = this.getAttribute('data-id');
            let action = this.getAttribute('data-action'); // 'increase' ou 'decrease'

            // Pour l'instant, on teste juste que le clic marche
            console.log(`produit choisi : ${productId}, Action choisie : ${action}`);
            alert(`Tu veux ${action === 'increase' ? 'ajouter' : 'retirer'} du stock au produit n°${productId}`);
        });
    });
});