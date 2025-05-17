document.addEventListener('DOMContentLoaded', function() {
    // Animation des cartes de catégories au chargement
    const categorieCards = document.querySelectorAll('.categorie-card');
    
    categorieCards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 150);
    });
    
    // Fonctionnalité de filtrage (exemple basique)
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    if(filterButtons.length > 0) {
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Enlever la classe active de tous les boutons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                // Ajouter la classe active au bouton cliqué
                this.classList.add('active');
                
                // Ici vous pourriez ajouter la logique pour filtrer les produits
                // Par exemple :
                // const category = this.dataset.category;
                // filterProducts(category);
            });
        });
    }
    
    // Animation au scroll
    const animateOnScroll = function() {
        const elements = document.querySelectorAll('.produit-card, .avantage-item');
        
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const screenPosition = window.innerHeight / 1.2;
            
            if(elementPosition < screenPosition) {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }
        });
    };
    
    // Écouteur d'événement pour l'animation au scroll
    window.addEventListener('scroll', animateOnScroll);
    
    // Initialiser les animations
    animateOnScroll();
});

// Ajoutez cette partie à la fin du fichier existant

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

document.addEventListener("DOMContentLoaded", () => {
    const addToCartButtons = document.querySelectorAll(".add-to-cart");

    addToCartButtons.forEach(button => {
        button.addEventListener("click", () => {
            const productId = button.dataset.productId;
            const productName = button.dataset.productName;
            const productPrice = parseFloat(button.dataset.productPrice);
            const quantityInput = button.parentElement.querySelector("input[type='number']");
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1;

            if (quantity > 0) {
                const cart = JSON.parse(localStorage.getItem("cart")) || [];
                const existingProductIndex = cart.findIndex(item => item.id === productId);

                if (existingProductIndex !== -1) {
                    cart[existingProductIndex].quantity += quantity;
                } else {
                    cart.push({
                        id: productId,
                        name: productName,
                        price: productPrice,
                        quantity: quantity
                    });
                }

                localStorage.setItem("cart", JSON.stringify(cart));
                alert(`${productName} a été ajouté au panier.`);
            } else {
                alert("Veuillez entrer une quantité valide.");
            }
        });
    });
});