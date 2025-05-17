document.addEventListener("DOMContentLoaded", () => {
    const cartBadge = document.getElementById("cart-badge");
    const cart = JSON.parse(localStorage.getItem("cart")) || [];

    function updateCartBadge() {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartBadge.textContent = totalItems;
    }

    function addToCart(productId, productName, productPrice, quantity = 1) {
        const existingProduct = cart.find(item => item.id === productId);
        if (existingProduct) {
            existingProduct.quantity += quantity;
        } else {
            cart.push({ id: productId, name: productName, price: productPrice, quantity });
        }
        localStorage.setItem("cart", JSON.stringify(cart));
        updateCartBadge();
    }

    document.querySelectorAll(".add-to-cart").forEach(button => {
        button.addEventListener("click", (e) => {
            e.preventDefault();
            const productId = button.getAttribute("data-product-id");
            const productName = button.getAttribute("data-product-name");
            const productPrice = parseFloat(button.getAttribute("data-product-price"));
            const quantityInput = button.previousElementSibling.querySelector("input");
            const quantity = parseInt(quantityInput?.value || 1);
            addToCart(productId, productName, productPrice, quantity);
        });
    });

    updateCartBadge();
});
