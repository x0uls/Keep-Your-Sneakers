<?php
session_start();
require 'db.php';
require 'filter.php'; // Our new filter class
include '_head.php';
include '_base.php';

try {
    $filter = new ProductFilter($pdo);

    if (isset($_GET['query'])) {
        $filter->applyFilters($_GET);
    }

    $result = $filter->getResults();
    $categories = $filter->getCategories();
    $sizes = $filter->getSizes();
    $search = $filter->getSearchTerm();
    $category_id = $filter->getCurrentCategory();
    $size_id = $filter->getCurrentSize();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    $result = null;
    $search = '';
    $categories = [];
    $sizes = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/filter.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            padding: 40px;
            margin-top: 100px;
        }

        .sidebar {
            width: 200px;
            min-width: 200px;
            background-color: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
            height: fit-content;
            margin-right: 40px;
        }

        .search-results-container {
            flex: 1;
        }

        h2 {
            text-align: center;
            font-size: 36px;
            font-weight: 600;
            margin-bottom: 40px;
            color: #333;
        }

        .product {
            display: inline-block;
            width: calc(33% - 40px);
            /* Adjust to 1/3 width */
            margin: 20px;
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
            text-decoration: none;
            color: inherit;
        }


        .product:hover {
            transform: scale(1.05, 1.05);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
        }

        .product img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .product h3 {
            font-size: 20px;
            font-weight: 600;
            margin: 20px 0;
            color: #333;
        }

        .product p {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 20px;
            color: #333;
        }

        .no-results {
            text-align: center;
            font-size: 18px;
            font-weight: 400;
            color: #999;
        }

        .product-button {
            display: inline-block;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            background-color: #111;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .product-button:hover {
            background-color: #333;
        }

        .price-input {
            position: relative;
            width: 100%;
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }

        .price-input span {
            position: absolute;
            left: 10px;
            top: 40%;
            transform: translateY(-50%);
            font-size: 14px;
            color: #666;
        }

        .price-input input {
            width: 100%;
            padding: 10px 10px 10px 30px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            text-align: center;
        }

        @media (max-width: 768px) {
            .product {
                width: calc(33.333% - 40px);
                /* 3 items per row on tablets */
            }
        }

        @media (max-width: 480px) {
            .product {
                width: calc(100% - 20px);
                /* 1 item per row on mobile */
                margin: 10px 0;
            }

            h2 {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Sidebar Filter -->
        <div class="sidebar">
            <h3>Filter by Price</h3>
            <div class="filter-form">
                <form method="GET" action="search.php" id="filterForm">
                    <input type="hidden" name="query" value="<?= htmlspecialchars($search) ?>">

                    <div class="price-input">
                        <span>RM</span>
                        <input type="number" name="min_price" placeholder="Min Price" id="minPrice"
                            value="<?= $filter->getMinPrice() !== null ? $filter->getMinPrice() : '' ?>">
                    </div>

                    <div class="price-input">
                        <span>RM</span>
                        <input type="number" name="max_price" placeholder="Max Price" id="maxPrice"
                            value="<?= $filter->getMaxPrice() !== null ? $filter->getMaxPrice() : '' ?>">
                    </div>

                    <div>
                        <select name="category" id="categorySelect">
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= $category_id == $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <select name="size" id="sizeSelect" <?= empty($sizes) ? 'disabled' : '' ?>>
                            <option value="">Select Size</option>
                            <?php foreach ($sizes as $size): ?>
                                <option value="<?= $size['id'] ?>" <?= $size_id == $size['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($size['size_label']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="button" onclick="submitFilterForm()">Apply Filter</button>
                </form>
            </div>
        </div>

        <!-- Results Section -->
        <div class="search-results-container">
            <h2>Search Results</h2>

            <?php if ($result && count($result) > 0): ?>
                <?php foreach ($result as $row): ?>
                    <a href="product_page.php?id=<?= $row['id'] ?>" class="product">
                        <img src="/products/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                        <h3><?= htmlspecialchars($row['name']) ?></h3>
                        <p>RM <?= number_format($row['price'], 2) ?></p>
                        <span class="product-button">View Product</span>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-results">No products found.</p>
            <?php endif; ?>
        </div>

        <!-- JavaScript remains the same -->
        <script>
            function submitFilterForm() {
                const form = document.getElementById('filterForm');
                const formData = new FormData(form);
                const params = new URLSearchParams();

                for (const [key, value] of formData.entries()) {
                    if (value !== '' && value !== null) {
                        params.append(key, value);
                    }
                }

                window.location.href = 'search.php?' + params.toString();
            }

            document.getElementById('categorySelect').addEventListener('change', function() {
                submitFilterForm();
            });
        </script>
    </div>
</body>

</html>