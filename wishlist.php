<?php
session_start();
require 'db.php';
include '_head.php'; // This includes the header, assuming it has a link to CSS/JS

// Fetch all wishlist items for the logged-in user
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("
            SELECT p.id AS product_id, p.name, p.price, p.image, s.size_label, c.name AS category_name 
            FROM wishlist w
            JOIN products p ON w.product_id = p.id
            JOIN sizes s ON w.size_id = s.id
            JOIN product_categories pc ON p.id = pc.product_id
            JOIN categories c ON pc.category_id = c.id
            WHERE w.user_id = :user_id
        ");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($wishlist_items)) {
            echo '<div class="wishlist-container">';
            echo '<h2>Your Wishlist</h2>';
            echo '<div class="wishlist-items">';
            foreach ($wishlist_items as $item) {
                echo '<div class="wishlist-item">';
                echo '<div class="wishlist-item-image">';
                echo '<img src="/products/' . htmlspecialchars($item['image']) . '" alt="' . htmlspecialchars($item['name']) . '">';
                echo '</div>';
                echo '<div class="wishlist-item-info">';
                echo '<h3>' . htmlspecialchars($item['name']) . '</h3>';
                echo '<p>Size: ' . htmlspecialchars($item['size_label']) . '</p>';
                echo '<p>Category: ' . htmlspecialchars($item['category_name']) . '</p>';
                echo '<p>Price: RM' . htmlspecialchars($item['price']) . '</p>';
                echo '<div class="wishlist-item-buttons">';
                echo '<button class="remove-from-wishlist" data-product-id="' . htmlspecialchars($item['product_id']) . '" data-size="' . htmlspecialchars($item['size_label']) . '"><img src="/images/cart-white.png" alt="Cart" style="width:20px; height:20px; vertical-align:middle; margin-right:5px;">Remove</button>';
                echo '<button class="add-to-cart" data-product-id="' . htmlspecialchars($item['product_id']) . '" data-size="' . htmlspecialchars($item['size_label']) . '" data-category="' . htmlspecialchars($item['category_name']) . '"><img src="/images/bin.png" alt="Bin" style="width:20px; height:20px; vertical-align:middle; margin-right:5px;">Add to Cart</button>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
            echo '</div>';
        } else {
            echo '<div class="wishlist-container"><p>Your wishlist is empty.</p></div>';
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    echo '<div class="wishlist-container"><p>Please log in to view your wishlist.</p></div>';
}
?>

<script>
    // Wait for the document to be ready
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners to all "Add to Cart" buttons
        const addToCartButtons = document.querySelectorAll('.add-to-cart');

        addToCartButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const productId = button.dataset.productId;
                const size = button.dataset.size;
                const category = button.dataset.category;

                // Send the data using Fetch API
                fetch('add_to_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            'product_id': productId,
                            'size': size,
                            'category': category
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Handle the response (success or error)
                        if (data.status === 'success') {
                            alert('Item added to cart!');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
            });
        });
    });

    // Add event listeners to all "Remove from Wishlist" buttons
    // Add event listeners to all "Remove from Wishlist" buttons
    const removeFromWishlistButtons = document.querySelectorAll('.remove-from-wishlist');

    removeFromWishlistButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const productId = button.dataset.productId;
            const size = button.dataset.size;

            // Send the data using Fetch API
            fetch('remove_wishlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        'product_id': productId,
                        'size': size
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Handle the response (success or error)
                    if (data.status === 'success') {
                        alert('Item removed from wishlist!');
                        button.closest('.wishlist-item').remove(); // Remove the item from the DOM
                        // Reload the page to refresh the wishlist
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
        });
    });
</script>

<!-- Add your CSS below -->
<style>
    /* General Styles */
    body {
        font-family: 'Helvetica Neue', sans-serif;
        background-color: #f5f5f5;
        margin: 0;
        padding: 0;
    }

    /* Wishlist Container */
    .wishlist-container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background-color: white;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    h2 {
        text-align: center;
        font-size: 28px;
        margin-bottom: 20px;
        color: #333;
    }

    /* Wishlist Item List (Now Grid) */
    .wishlist-items {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        /* Create flexible columns */
        gap: 20px;
        /* Add spacing between items */
    }

    /* Wishlist Item */
    .wishlist-item {
        display: flex;
        flex-direction: column;
        /* Stack items vertically */
        background-color: #fff;
        border: 1px solid #ddd;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s ease-in-out;
    }

    /* Wishlist Item Image */
    .wishlist-item-image {
        margin-bottom: 15px;
    }

    .wishlist-item-image img {
        width: 100%;
        height: auto;
        border-radius: 8px;
    }

    /* Wishlist Item Info */
    .wishlist-item-info {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .wishlist-item-info h3 {
        font-size: 20px;
        margin: 0;
        color: #333;
        text-align: center;
    }

    .wishlist-item-info p {
        font-size: 16px;
        color: #777;
    }

    /* Wishlist Item Buttons */
    .wishlist-item-buttons {
        display: flex;
        justify-content: space-between;
        /* Space out buttons */
        gap: 10px;
        /* Space between buttons */
    }

    .wishlist-item-buttons button {
        flex: 1;
        padding: 8px 5px;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        font-size: 15px;
        font-weight: bold;
        transition: background-color 0.3s;
    }

    .wishlist-item:hover {
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
    }

    .remove-from-wishlist {
        background-color: black;
        color: white;
    }

    .add-to-cart {
        background-color: black;
        color: white;
    }

    .remove-from-wishlist:hover {
        background-color: gray;
        transition: 0.3s;
    }

    .add-to-cart:hover {
        background-color: gray;
        transition: 0.3s;
    }

    /* Empty Wishlist Message */
    .wishlist-container p {
        text-align: center;
        font-size: 18px;
        color: #666;
    }
</style>