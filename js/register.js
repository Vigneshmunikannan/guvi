$(document).ready(function () {
    $('#registerForm').submit(function (e) {
        e.preventDefault();
        var data = {
            name: $("#name").val(),
            username: $("#username").val(),
            lastname: $("#lastname").val(),
            age: $("#age").val(),
            dob: $("#dob").val(),
            dept: $("#dept").val(),
            location: $("#location").val(),
            domain: $("#domain").val(),
            password: $("#password").val(),
        };

        // Check if passwords match
        var password = $('#password').val();
        var confirmPassword = $('#confirmPassword').val();
        if (password !== confirmPassword) {
            alert("Passwords do not match!");
            return;
        }

        $.ajax({
            url: 'http://localhost:3000/php/register.php',
            type: 'POST',
            data: data,
            success: function (response) {
                console.log(response);
                alert(response);
                window.location.href = 'login.html';
            },
            error: function (xhr, status, error) {
                console.log(xhr.responseText + "***", status + "----", error + "^^^");
                alert(xhr.responseText, status, error);
            }
        });
    });
});
