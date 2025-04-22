$(document).ready(function () {

    function updateTotals() {
        let total = 0;

        // Iterate over all cart items (based on the class .cart-item)
        $('.cart-item').each(function () {
            const price = parseFloat($(this).find('.cart-item-price p').text().replace('RM', ''));
            const qty = parseInt($(this).find('.quantity-display').val(), 10);
            total += price * qty;
        });

        // Update total price display
        $('#totalPrice').text(total.toFixed(2));
    }

    // Handle + button
    $(document).on('click', '.qty-plus', function () {
        const wrapper = $(this).closest('.quantity-wrapper');
        const display = wrapper.find('.quantity-display');
        const minusBtn = wrapper.find('.qty-minus');
        let qty = parseInt(display.val(), 10);
        const max = parseInt(wrapper.data('max'), 10);

        if (qty < max) {
            qty++;
            display.val(qty);
            minusBtn.text('−'); // Restore from trash if it was 1
        }

        // Update item total price
        const row = wrapper.closest('.cart-item');
        const price = parseFloat(row.find('.cart-item-price p').text().replace('RM', ''));
        row.find('.cart-item-total p').text('RM' + (price * qty).toFixed(2));

        updateTotals(); // Recalculate total price
    });

    // Handle − / 🗑️ button
    $(document).on('click', '.qty-minus', function () {
        const wrapper = $(this).closest('.quantity-wrapper');
        const display = wrapper.find('.quantity-display');
        let qty = parseInt(display.val(), 10);
        const productId = wrapper.data('product');
        const sizeId = wrapper.data('size');

        if (qty > 1) {
            qty--;
            display.val(qty);

            // If new qty is 1, switch minus to 🗑️
            if (qty === 1) {
                $(this).text('🗑️');
            }
        } else {
            // Remove item from cart via AJAX
            if (confirm("Remove this item from your cart?")) {
                $.post('cart.php', {
                    action: 'remove',
                    product_id: productId,
                    size_id: sizeId
                }, function (res) {
                    if (res.success) {
                        wrapper.closest('.cart-item').remove();
                        updateTotals();

                        if ($(".cart-item").length === 0) {
                            $("#cartContainer").html("<p>Your cart is empty.</p>");
                        }
                    } else {
                        alert('Failed to remove item.');
                    }
                }, 'json');
                return;
            }
        }

        // Update item total price
        const row = wrapper.closest('.cart-item');
        const price = parseFloat(row.find('.cart-item-price p').text().replace('RM', ''));
        row.find('.cart-item-total p').text('RM' + (price * qty).toFixed(2));

        updateTotals(); // Recalculate total price
    });

    // Initial total
    updateTotals();

});
