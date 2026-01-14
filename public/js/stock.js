document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.btn-action');

    buttons.forEach(button => {
        button.addEventListener('click', function() {
            
          
            this.disabled = true; 

            const productId = this.getAttribute('data-id');
            const action = this.getAttribute('data-action');

           
           const stockDisplay = document.getElementById(`stock-display-${productId}`);

            const monToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Appel Fetch
            fetch(`/products/${productId}/update-stock`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': monToken
                },
                body: JSON.stringify({ action: action })
            })
            
            .then(response => response.json())
            .then(data => {
                console.log('Réponse serveur :', data);

                if (data.success) {
                    console.log('Stock mis à jour avec succès.' , stockDisplay);
                    // Mise à jour du chiffre
                    stockDisplay.textContent = data.newStock;

                   
                    stockDisplay.style.color = (action === 'increase') ? 'green' : 'red';
                    
                    setTimeout(() => {
                        stockDisplay.style.color = '';
                    }, 500);
                }
            })
            .catch(error => console.error('Erreur:', error))
            .finally(() => {
                // Réactivation du bouton
                this.disabled = false;
            });
        });
    });
});