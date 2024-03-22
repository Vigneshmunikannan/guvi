$(document).ready(function () {
    // Check if the user is logged in
    var token = localStorage.getItem('jwt_token');
    if (token) {
        // User is logged in, allow access to the profile page
        window.location.href = 'profile.html';
    }

    $('#loginForm').submit(function (e) {
        e.preventDefault();
        var data = {
            username: $("#username").val(),
            password: $("#password").val(),
        };
        $.ajax({
            url: 'http://localhost:3000/php/login.php',
            type: 'POST',
            data: data,
            success: function (response) {
                var data = JSON.parse(response);
                // Access the token from the response
                var token = data.token;
                // Access other properties if needed
                var message = data.message;
                $('#message').text(message);
                localStorage.setItem('jwt_token', token);
                window.location.href = 'profile.html';
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
                $('#message').text('Invalid username or password');
            }
        });
    });
});
