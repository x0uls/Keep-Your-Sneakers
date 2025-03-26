$(document).ready(function() {
    function updateCartCount() {
        $.ajax({
            url: "cart_action.php",
            type: "POST",
            data: { action: "count" },
            success: function(count) {
                $(".cart-count").text(count);
            }
        });
    }

    updateCartCount(); // Update on page load

    // Example: Update when adding to cart
    $(".add-to-cart").click(function() {
        updateCartCount();
    });
});
