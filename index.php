<?php
session_start();
?>

<body>

    <?php include '_head.php'; ?>

    <div class="content">
        <h1>Welcome to the Home Page</h1>
    </div>

    <?php include '_foot.php'; ?>

    <?php if (isset($_SESSION['signup_success'])): ?>
        <div class="success-popup"><?php echo $_SESSION['signup_success']; ?></div>
        <script>
            $(document).ready(function() {
                $(".success-popup").slideDown().delay(5000).slideUp();
            });
        </script>
        <?php unset($_SESSION['signup_success']); // Clear message after displaying 
        ?>
    <?php endif; ?>

</body>

</html>