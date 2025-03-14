$(document).ready(function () {
    $("#signup-form").submit(function (event) {
        event.preventDefault(); // Prevent default form submission

        let formData = $(this).serialize();

        $.ajax({
            type: "POST",
            url: "process_signup.php",
            data: formData,
            dataType: "json",
            success: function (response) {
                if (response.status === "exists") {
                    $("#email-error").html(response.message);
                } else if (response.status === "success") {
                    localStorage.setItem("signupSuccess", "true");
                    window.location.href = "index.php"; // Redirect to home page
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert("An error occurred. Please try again.");
            }
        });
    });

    // Show success message if redirected from signup
    if (localStorage.getItem("signupSuccess") === "true") {
        $("body").prepend('<div class="success-popup">Successfully signed up!</div>');
        $(".success-popup").slideDown().delay(5000).slideUp();
        localStorage.removeItem("signupSuccess");
    }
});