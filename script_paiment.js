document.addEventListener("DOMContentLoaded", function () {
    const payBtn = document.querySelector(".btn");

    payBtn.addEventListener("click", function (e) {
        const cardNumber = document.getElementById("cardNumber").value.trim();
        const expiry = document.getElementById("expiryDate").value.trim();
        const cvv = document.getElementById("cvv").value.trim();
        const name = document.getElementById("cardName").value.trim();

        if (!cardNumber || !expiry || !cvv || !name) {
            e.preventDefault();
            alert("Veuillez remplir tous les champs de paiement.");
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const paymentSection = document.getElementById('paymentSection');
    paymentSection.classList.remove('hidden');
    
    const cart = JSON.parse(localStorage.getItem('cart'));
    
    if (cart && cart.length > 0) {
        displayCart(cart);
    } else {
        displayEmpty();
    }
    
    function displayCart(cart) {
        const selectedItemElement = document.getElementById('selectedItem');
        const itemPriceElement = document.getElementById('itemPrice');
        const totalPriceElement = document.getElementById('totalPrice');
        
        selectedItemElement.textContent = `${cart.length} article(s) dans le panier`;
        
        let itemsText = '';
        let total = 0;
        
        cart.forEach(item => {
            itemsText += `${item.name} - ${item.price.toFixed(2)} € x ${item.quantity}\n`;
            total += item.price * item.quantity;
        });
        
        itemPriceElement.textContent = itemsText;
        totalPriceElement.textContent = `${total.toFixed(2)} €`;
    }
    
    function displayEmpty() {
        document.getElementById('selectedItem').textContent = 'Aucun article sélectionné';
        document.getElementById('itemPrice').textContent = '0.00 €';
        document.getElementById('totalPrice').textContent = '0.00 €';
        
        // Redirection après 3 secondes
        setTimeout(() => {
            window.location.href = 'index_service.php';
        }, 3000);
    }
});