document.addEventListener("DOMContentLoaded", () => {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    const cartTable = document.getElementById("cartTable");
    const totalElement = document.getElementById("total");
    const emptyMessage = document.getElementById("emptyMessage");
    const cartSection = document.getElementById("cartSection");

    function renderCart() {
        if (cart.length === 0) {
            emptyMessage.style.display = "block";
            cartSection.style.display = "none";
        } else {
            emptyMessage.style.display = "none";
            cartSection.style.display = "block";
            cartTable.innerHTML = cart.map((item, index) => `
                <tr>
                    <td>${item.name}</td>
                    <td>${item.price.toFixed(2)} €</td>
                    <td>
                        <input type="number" min="1" value="${item.quantity}" data-index="${index}" class="quantity-input">
                    </td>
                    <td>${(item.price * item.quantity).toFixed(2)} €</td>
                    <td>
                        <button data-index="${index}" class="remove-btn">Supprimer</button>
                    </td>
                </tr>
            `).join("");

            const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
            totalElement.textContent = total.toFixed(2) + " €";
        }
    }

    function updateCart() {
        localStorage.setItem("cart", JSON.stringify(cart));
        renderCart();
    }

    cartTable.addEventListener("input", (e) => {
        if (e.target.classList.contains("quantity-input")) {
            const index = e.target.dataset.index;
            const newQuantity = parseInt(e.target.value);
            if (newQuantity > 0) {
                cart[index].quantity = newQuantity;
                updateCart();
            }
        }
    });

    cartTable.addEventListener("click", (e) => {
        if (e.target.classList.contains("remove-btn")) {
            const index = e.target.dataset.index;
            cart.splice(index, 1);
            updateCart();
        }
    });

    document.getElementById("clearCart").addEventListener("click", () => {
        localStorage.removeItem("cart");
        cart.length = 0;
        updateCart();
    });

    renderCart();
});