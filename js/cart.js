$(document).ready(function () {

    function updateTotals() {
        let total = 0;

        $('.cart-item').each(function () {
            const price = parseFloat($(this).find('.cart-item-price p').text().replace('RM', ''));
            const qty = parseInt($(this).find('.quantity-display').val(), 10);
            total += price * qty;
        });

        $('#totalPrice').text(total.toFixed(2));
    }

    function updateQuantity(productId, sizeId, qty) {
        $.post('cart.php', {
            action: 'update',
            product_id: productId,
            size_id: sizeId,
            quantity: qty
        });
    }

    // Handle + button
    $(document).on('click', '.qty-plus', function () {
        const wrapper = $(this).closest('.quantity-wrapper');
        const display = wrapper.find('.quantity-display');
        const minusBtn = wrapper.find('.qty-minus');
        let qty = parseInt(display.val(), 10);
        const max = parseInt(wrapper.data('max'), 10);
        const productId = wrapper.data('product');
        const sizeId = wrapper.data('size');

        if (qty < max) {
            qty++;
            display.val(qty);
            minusBtn.text('−'); // Restore from trash if it was 1

            updateQuantity(productId, sizeId, qty);
        }

        const row = wrapper.closest('.cart-item');
        const price = parseFloat(row.find('.cart-item-price p').text().replace('RM', ''));
        row.find('.cart-item-total p').text('RM' + (price * qty).toFixed(2));

        updateTotals();
    });

    // Handle − / 🗑️ button
    $(document).on('click', '.qty-minus', function () {
        const wrapper = $(this).closest('.quantity-wrapper');
        const display = wrapper.find('.quantity-display');
        let qty = parseInt(display.val(), 10);
        const productId = wrapper.data('product');
        const sizeId = wrapper.data('size');
    
        // If button is showing 🗑️, remove item
        if ($(this).text() === '🗑️') {
            if (confirm("Remove this item from your cart?")) {
                $.post('cart.php', {
                    action: 'remove',
                    product_id: productId,
                    size_id: sizeId
                }, function (res) {
                    console.log(res);
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
            }
            return; // Stop here
        }
    
        // Otherwise, it's a regular minus
        if (qty > 1) {
            qty--;
            display.val(qty);
    
            // If new qty is 1, switch minus to 🗑️
            if (qty === 1) {
                $(this).text('🗑️');
            }
    
            updateQuantity(productId, sizeId, qty);
    
            const row = wrapper.closest('.cart-item');
            const price = parseFloat(row.find('.cart-item-price p').text().replace('RM', ''));
            row.find('.cart-item-total p').text('RM' + (price * qty).toFixed(2));
    
            updateTotals();
        }
    });

    updateTotals();

});
