$(document).ready(function () {
    function updateCartCount() {
        $.ajax({
            url: "cart_action.php",
            type: "POST",
            data: { action: "count" },
            success: function (count) {
                $(".cart-count").text(count);
            }
        });
    }

    // Initial update on page load
    updateCartCount();

    // Add to Cart via image or other dynamic method
    window.addToCart = function (productId) {
        $.ajax({
            url: "add_to_cart.php",
            type: "POST",
            data: { product_id: productId },
            success: function (response) {
                try {
                    var data = JSON.parse(response);
                    if (data.status === 'success') {
                        alert('Added to cart!');
                        updateCartCount();
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (e) {
                    alert('Unexpected error. Check console.');
                    console.error(e, response);
                }
            }
        });
    };

    // Optional: Bind to .add-to-cart class (if used)
    $(".add-to-cart").click(function () {
        const productId = $(this).data("product-id");
        if (productId) {
            addToCart(productId);
        }
    });
});
