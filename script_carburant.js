document.addEventListener('DOMContentLoaded', function() {
    // Fonctionnalité FAQ
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        
        question.addEventListener('click', () => {
            // Fermer tous les autres items
            faqItems.forEach(otherItem => {
                if (otherItem !== item) {
                    otherItem.classList.remove('active');
                    otherItem.querySelector('.faq-answer').style.display = 'none';
                }
            });
            
            // Basculer l'item actuel
            item.classList.toggle('active');
            const answer = item.querySelector('.faq-answer');
            
            if (item.classList.contains('active')) {
                answer.style.display = 'block';
            } else {
                answer.style.display = 'none';
            }
        });
    });
    
    // Animation des cartes de carburant
    const carburantCards = document.querySelectorAll('.carburant-card');
    
    carburantCards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 150);
    });
    
    // Simulation de recherche de stations
    const searchForm = document.querySelector('.search-box');
    
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const input = this.querySelector('input');
            const query = input.value.trim();
            
            if (query) {
                // Ici vous pourriez ajouter une vraie recherche AJAX
                alert(`Recherche des stations près de: ${query}`);
                input.value = '';
            }
        });
    }
    
    // Animation au scroll
    const animateOnScroll = function() {
        const elements = document.querySelectorAll('.avantage-item, .station-card');
        
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const screenPosition = window.innerHeight / 1.2;
            
            if(elementPosition < screenPosition) {
                element.style.opacity = '1';
                element.style.transform = 'translateX(0)';
            }
        });
    };
    
    // Écouteur d'événement pour l'animation au scroll
    window.addEventListener('scroll', animateOnScroll);
    
    // Initialiser les animations
    animateOnScroll();
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