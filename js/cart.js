$(document).ready(function () {

    function updateTotals() {
        let total = 0;

        $('.item-checkbox').each(function () {
            if ($(this).is(':checked')) {
                const row = $(this).closest('tr');
                const price = parseFloat($(this).data('price'));
                const qty = parseInt(row.find('.quantity-display').val(), 10);
                total += price * qty;
            }
        });

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

        const row = wrapper.closest('tr');
        const price = parseFloat(row.find('.item-checkbox').data('price'));
        row.find('.item-total').text('RM' + (price * qty).toFixed(2));

        updateTotals();
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
                        wrapper.closest('tr').remove();
                        updateTotals();

                        if ($(".item-checkbox").length === 0) {
                            $("#cartContainer").html("<p>Your cart is empty.</p>");
                        }
                    } else {
                        alert('Failed to remove item.');
                    }
                }, 'json');
                return;
            }
        }

        const row = wrapper.closest('tr');
        const price = parseFloat(row.find('.item-checkbox').data('price'));
        row.find('.item-total').text('RM' + (price * qty).toFixed(2));

        updateTotals();
    });

    // Recalculate when checkbox is toggled
    $(document).on('change', '.item-checkbox', function () {
        updateTotals();
    });

    // Initial total
    updateTotals();
});
