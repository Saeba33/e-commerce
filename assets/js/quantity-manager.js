document.addEventListener("DOMContentLoaded", function () {
    //#region PLUS BUTTONS HANDLING
    const plusButtons = document.querySelectorAll(".btn-plus");

    // For each + button, add a click listener
    plusButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            // 1. Find the product container
            const productContainer = button.closest("[data-product-id]");

            // 2. Get necessary elements
            const quantityDisplay =
                productContainer.querySelector(".quantity-display");
            const availableStock = parseInt(
                productContainer.dataset.availableStock
            );

            // 3. Get current quantity
            let currentQuantity = parseInt(quantityDisplay.textContent);

            // 4. Check if we can increase
            if (currentQuantity < availableStock) {
                // Increase quantity
                currentQuantity = currentQuantity + 1;
                // Update display
                quantityDisplay.textContent = currentQuantity;
                // Update buttons
                updateButtons(
                    productContainer,
                    currentQuantity,
                    availableStock
                );
            }
        });
    });
    //#endregion

    //#region MINUS BUTTONS HANDLING
    const minusButtons = document.querySelectorAll(".btn-minus");

    // For each - button, add a click listener
    minusButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            // 1. Find the product container
            const productContainer = button.closest("[data-product-id]");

            // 2. Get necessary elements
            const quantityDisplay =
                productContainer.querySelector(".quantity-display");
            const availableStock = parseInt(
                productContainer.dataset.availableStock
            );

            // 3. Get current quantity
            let currentQuantity = parseInt(quantityDisplay.textContent);

            // 4. Check if we can decrease (minimum = 1)
            if (currentQuantity > 1) {
                // Decrease quantity
                currentQuantity = currentQuantity - 1;
                // Update display
                quantityDisplay.textContent = currentQuantity;
                // Update buttons
                updateButtons(
                    productContainer,
                    currentQuantity,
                    availableStock
                );
            }
        });
    });
    //#endregion

    //#region ADD TO CART BUTTONS HANDLING
    const addToCartButtons = document.querySelectorAll(".add-to-cart-btn");

    // For each button, add a click listener
    addToCartButtons.forEach(function (button) {
        button.addEventListener("click", function (event) {
            // Prevent default link behavior
            event.preventDefault();

            // 1. Get product ID
            const productId = button.dataset.productId;

            // 2. Find the product container
            const productContainer = document.querySelector(
                '[data-product-id="' + productId + '"]'
            );

            // 3. Get selected quantity
            const selectedQuantity = parseInt(
                productContainer.querySelector(".quantity-display").textContent
            );

            // 4. Redirect to add to cart page
            window.location.href =
                "/cart/add/" + productId + "/quantity/" + selectedQuantity;
        });
    });
    //#endregion

    //#region INITIALIZATION
    // Update initial state of all buttons
    const allProducts = document.querySelectorAll("[data-product-id]");

    allProducts.forEach(function (container) {
        const quantity = parseInt(
            container.querySelector(".quantity-display").textContent
        );
        const availableStock = parseInt(container.dataset.availableStock);

        updateButtons(container, quantity, availableStock);
    });
    //#endregion
});

//#region UTILITY FUNCTIONS
function updateButtons(container, quantity, availableStock) {
    // Get buttons
    const plusButton = container.querySelector(".btn-plus");
    const minusButton = container.querySelector(".btn-minus");

    // MINUS BUTTON: disabled if quantity = 1
    if (quantity <= 1) {
        minusButton.disabled = true;
        minusButton.classList.add("opacity-50", "cursor-not-allowed");
    } else {
        minusButton.disabled = false;
        minusButton.classList.remove("opacity-50", "cursor-not-allowed");
    }

    // PLUS BUTTON: disabled if quantity = available stock
    if (quantity >= availableStock) {
        plusButton.disabled = true;
        plusButton.classList.add("opacity-50", "cursor-not-allowed");
    } else {
        plusButton.disabled = false;
        plusButton.classList.remove("opacity-50", "cursor-not-allowed");
    }
}
//#endregion
