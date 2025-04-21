$(document).ready(function () {

    // ========== Live Total Price Update ==========
    function calculateTotal() {
        let total = 0;

        $('.item-checkbox').each(function () {
            if ($(this).is(':checked')) {
                const price = parseFloat($(this).data('price'));
                const quantity = parseInt($(this).closest('tr').find('.quantity-input').val());
                total += price * quantity;
            }
        });

        $('#totalPrice').text(total.toFixed(2));
    }

    // ========== Add to Cart ==========
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

    // Bind to dynamic "Add to cart" buttons
    $(".add-to-cart").click(function () {
        const productId = $(this).data("product-id");
        if (productId) {
            addToCart(productId);
        }
    });

    // ========== Remove from Cart ==========
    $(document).off("click", ".remove-btn").on("click", ".remove-btn", function (e) {
        e.preventDefault();
        const btn = $(this);
        const productId = btn.data("id");

        if (confirm("Remove this item from your cart?")) {
            $.post("cart.php", { action: "remove", product_id: productId }, function (res) {
                try {
                    const result = res;
                    if (result.success) {
                        btn.closest("tr").remove();
                        calculateTotal();

                        if ($(".item-checkbox").length === 0) {
                            $("#cartContainer").html("<p>Your cart is empty.</p>");
                        }
                    } else {
                        alert("Failed to remove item.");
                    }
                } catch (err) {
                    alert("Something went wrong.");
                    console.error(err);
                }
            });
        }
    });

    // Recalculate when quantity or checkbox changes
    $(document).on('change', '.item-checkbox, .quantity-input', function () {
        calculateTotal();
    });

    // Initial run
    calculateTotal();
});
