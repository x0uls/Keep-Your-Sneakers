<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/css/admin.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="dashboard-container">
        <h2>Admin Dashboard</h2>
        <h3>Add Product</h3>

        <form method="POST" enctype="multipart/form-data" action="admin_insert.php">
            <label>Name:</label>
            <input type="text" name="name" required />

            <label>Description:</label>
            <textarea name="description" required></textarea>

            <label>Price:</label>
            <input type="number" step="0.01" name="price" required />

            <label>Image:</label>
            <input type="file" name="image" required />

            <label>Categories:</label>
            <div class="checkbox-group">
                <div>
                    <label><input type="checkbox" name="categories[]" value="1" class="category-toggle" data-target="#sizes-men" /> Men</label>
                    <div id="sizes-men" class="category-section sizes-group">
                        <h4>Men Sizes</h4>
                        <div class="size-row">UK 6 <input type="number" name="sizes[Men][UK 6]" min="0" /></div>
                        <div class="size-row">UK 6.5 <input type="number" name="sizes[Men][UK 6.5]" min="0" /></div>
                        <div class="size-row">UK 7 <input type="number" name="sizes[Men][UK 7]" min="0" /></div>
                        <div class="size-row">UK 7.5 <input type="number" name="sizes[Men][UK 7.5]" min="0" /></div>
                        <div class="size-row">UK 8 <input type="number" name="sizes[Men][UK 8]" min="0" /></div>
                        <div class="size-row">UK 8.5 <input type="number" name="sizes[Men][UK 8.5]" min="0" /></div>
                        <div class="size-row">UK 9 <input type="number" name="sizes[Men][UK 9]" min="0" /></div>
                        <div class="size-row">UK 9.5 <input type="number" name="sizes[Men][UK 9.5]" min="0" /></div>
                        <div class="size-row">UK 10 <input type="number" name="sizes[Men][UK 10]" min="0" /></div>
                        <div class="size-row">UK 10.5 <input type="number" name="sizes[Men][UK 10.5]" min="0" /></div>
                        <div class="size-row">UK 11 <input type="number" name="sizes[Men][UK 11]" min="0" /></div>
                        <div class="size-row">UK 12 <input type="number" name="sizes[Men][UK 12]" min="0" /></div>
                    </div>
                </div>

                <div>
                    <label><input type="checkbox" name="categories[]" value="2" class="category-toggle" data-target="#sizes-women" /> Women</label>
                    <div id="sizes-women" class="category-section sizes-group">
                        <h4>Women Sizes</h4>
                        <div class="size-row">UK 2.5 <input type="number" name="sizes[Women][UK 2.5]" min="0" /></div>
                        <div class="size-row">UK 3 <input type="number" name="sizes[Women][UK 3]" min="0" /></div>
                        <div class="size-row">UK 3.5 <input type="number" name="sizes[Women][UK 3.5]" min="0" /></div>
                        <div class="size-row">UK 4 <input type="number" name="sizes[Women][UK 4]" min="0" /></div>
                        <div class="size-row">UK 4.5 <input type="number" name="sizes[Women][UK 4.5]" min="0" /></div>
                        <div class="size-row">UK 5 <input type="number" name="sizes[Women][UK 5]" min="0" /></div>
                        <div class="size-row">UK 5.5 <input type="number" name="sizes[Women][UK 5.5]" min="0" /></div>
                        <div class="size-row">UK 6 <input type="number" name="sizes[Women][UK 6]" min="0" /></div>
                        <div class="size-row">UK 6.5 <input type="number" name="sizes[Women][UK 6.5]" min="0" /></div>
                        <div class="size-row">UK 7 <input type="number" name="sizes[Women][UK 7]" min="0" /></div>
                        <div class="size-row">UK 7.5 <input type="number" name="sizes[Women][UK 7.5]" min="0" /></div>
                    </div>
                </div>

                <div>
                    <label><input type="checkbox" name="categories[]" value="3" class="category-toggle" data-target="#sizes-kids" /> Kids</label>
                    <div id="sizes-kids" class="category-section sizes-group">
                        <h4>Kids Sizes</h4>
                        <div class="size-row">UK 3 <input type="number" name="sizes[Kids][UK 3]" min="0" /></div>
                        <div class="size-row">UK 3.5 <input type="number" name="sizes[Kids][UK 3.5]" min="0" /></div>
                        <div class="size-row">UK 4 <input type="number" name="sizes[Kids][UK 4]" min="0" /></div>
                        <div class="size-row">UK 4.5 <input type="number" name="sizes[Kids][UK 4.5]" min="0" /></div>
                        <div class="size-row">UK 5 <input type="number" name="sizes[Kids][UK 5]" min="0" /></div>
                        <div class="size-row">UK 5.5 <input type="number" name="sizes[Kids][UK 5.5]" min="0" /></div>
                        <div class="size-row">UK 6 (EU 39) <input type="number" name="sizes[Kids][UK 6 (EU 39)]" min="0" /></div>
                        <div class="size-row">UK 6 (EU 40) <input type="number" name="sizes[Kids][UK 6 (EU 40)]" min="0" /></div>
                    </div>
                </div>
            </div>

            <input type="submit" value="Add Product" />
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $('.category-toggle').on('change', function() {
                const target = $($(this).data('target'));
                if ($(this).is(':checked')) {
                    target.slideDown();
                } else {
                    target.slideUp();
                    target.find('input[type=number]').val('');
                }
            });
        });
    </script>
</body>

</html>