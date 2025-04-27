<?php
require 'lib/PHPMailer.php';
require 'lib/SMTP.php';
require 'lib/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure PHPMailer's autoload is included

function sendEmail($userEmail)
{
    // Prepare the email content
    $subject = 'Your Password Has Been Changed';
    $body = 'Hello, your password has been successfully changed. If you did not make this change, please contact support immediately.';

    // Initialize PHPMailer
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.yourmailserver.com'; // Set the SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@yourdomain.com'; // Your SMTP username
        $mail->Password = 'your_email_password'; // Your SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587; // Or 465 for SSL

        //Recipients
        $mail->setFrom('no-reply@yourdomain.com', 'Your Site Name');
        $mail->addAddress($userEmail); // Add the user's email address

        //Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = nl2br($body);

        // Send email
        $mail->send();
    } catch (Exception $e) {
        // Handle error (optional)
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}


function renderFilters($pdo, $search = '')
{
    // Fetch all categories
    try {
        $stmt = $pdo->query("SELECT name FROM categories");
        $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        $categories = [];
    }

    // Fetch sizes by category
    $sizes = [];
    foreach ($categories as $category) {
        try {
            $stmt = $pdo->prepare("SELECT DISTINCT size_label FROM sizes WHERE category = :category");
            $stmt->execute([':category' => $category]);
            $sizes[$category] = extractSizes($stmt->fetchAll(PDO::FETCH_COLUMN));
        } catch (PDOException $e) {
            $sizes[$category] = [];
        }
    }
?>
    <script>
        const sizesByCategory = <?= json_encode($sizes) ?>;
    </script>

    <form method="GET" action="">
        <input type="hidden" name="query" value="<?= htmlspecialchars($search) ?>">

        <div class="filter-group">
            <label>Min Price</label>
            <input type="number" name="min_price" placeholder="Min">
        </div>

        <div class="filter-group">
            <label>Max Price</label>
            <input type="number" name="max_price" placeholder="Max">
        </div>

        <div class="filter-group">
            <label>Category</label>
            <select name="category" id="category-select" onchange="showSizeOptions()">
                <option value="">-- Select --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group" id="size-group" style="display: none;">
            <label>Size</label>
            <select name="size" id="size-select">
                <!-- Sizes filled dynamically -->
            </select>
        </div>

        <button class="filter-submit" type="submit">Apply Filters</button>
    </form>

    <script>
        function showSizeOptions() {
            const category = document.getElementById('category-select').value;
            const sizeGroup = document.getElementById('size-group');
            const sizeSelect = document.getElementById('size-select');

            sizeSelect.innerHTML = '';

            if (sizesByCategory[category] && sizesByCategory[category].length > 0) {
                sizeGroup.style.display = 'block';
                sizesByCategory[category].forEach(size => {
                    sizeSelect.innerHTML += `<option value="${size}">${size} UK</option>`;
                });
            } else {
                sizeGroup.style.display = 'none';
            }
        }
    </script>
<?php
}

function extractSizes($rows)
{
    $sizes = array_map('trim', $rows); // one size per row
    $sizes = array_unique($sizes);
    sort($sizes, SORT_NATURAL);
    return $sizes;
}
?>