document.querySelectorAll('.faq-question').forEach(question => {
    question.addEventListener('click', () => {
        const faqItem = question.parentElement;
        const answer = question.nextElementSibling;

        faqItem.classList.toggle('active');

        if (faqItem.classList.contains('active')) {
            answer.style.display = 'block';
        } else {
            answer.style.display = 'none';
        }
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            
            const productId = button.getAttribute('data-product-id');
            const productName = button.getAttribute('data-product-name');
            const productPrice = parseFloat(button.getAttribute('data-product-price'));
            
            // Récupérer le panier depuis le localStorage
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            // Ajouter le produit au panier
            cart.push({
                id: productId,
                name: productName,
                price: productPrice,
                type: 'lavage'
            });
            
            // Mettre à jour le localStorage
            localStorage.setItem('cart', JSON.stringify(cart));
            
            // Mettre à jour le badge du panier
            const cartBadge = document.getElementById('cart-badge');
            cartBadge.textContent = cart.length;
            
            alert(`${productName} a été ajouté au panier !`);
        });
    });
});