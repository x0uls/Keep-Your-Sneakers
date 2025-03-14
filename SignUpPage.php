<?php
include '_head.php';
?>

<div class="signup-container">
    <h2>Sign Up</h2>
    <form action="process_signup.php" method="POST" id="signup-form">
        <label for="name">Full Name</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="email">Email</label><br>
        <input type="email" id="email" name="email" required><br>
        <span id="email-error" class="error-message"></span><br>

        <label for="password">Password</label><br>
        <input type="password" id="password" name="password" required><br>
        <span id="password-error" class="error-message"></span><br>

        <label for="confirm_password">Confirm Password</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required><br>
        <span id="confirm-password-error" class="error-message"></span><br>

        <button type="submit">Sign Up</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="js/signup.js"></script>

<?php
include '_foot.php';
?>