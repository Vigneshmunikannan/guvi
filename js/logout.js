$(document).ready(function () {
    // Check if the user is logged in
    var token = localStorage.getItem('jwt_token');

    if (!token) {
        // If not logged in, redirect to login page
        window.location.href = 'login.html';
    } else {

        // Fetch user profile data from MongoDB
        $.ajax({
            url: 'http://localhost:3000/php/profile.php',
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function (response) {
                var userData = JSON.parse(response);
                displayUserProfile(userData, token); // Pass token to displayUserProfile function
            },
            error: function (xhr, status, error) {
                console.log(status, error)
                profileInfo.append('Error in fetching data')
                if (error == "Unauthorized") {
                    localStorage.clear();
                }
            }
        });

        // Logout button click event
        $('#logoutButton').click(function (e) {
            e.preventDefault();
            console.log(token);
            // AJAX request to logout.php
            $.ajax({
                url: 'http://localhost:3000/php/logout.php',
                type: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function (response) {
                    localStorage.clear();
                    window.location.href = 'login.html';
                },
                error: function (xhr, status, error) {

                    console.error(xhr.responseText + "_______");

                    // Handle error if needed
                }
            });
        });
    }
});


// Function to display user profile
function displayUserProfile(user, token) { // Accept token as parameter
    var profileInfo = $('#profileInfo');
    profileInfo.empty();

    // Display user profile information
    var userProfileHtml = '<h5 class="card-title">' + user.username + '</h5>' +
        '<p class="card-text">Username: ' + user.username + '</p>' +
        '<p class="card-text">Age: <span id="userAge">' + user.age + '</span></p>' +
        '<p class="card-text">Date of Birth: <span id="userDob">' + user.dob + '</span></p>' +
        '<p class="card-text">Department: <span id="userDept">' + user.dept + '</span></p>' +
        '<p class="card-text">Location: <span id="userLocation">' + user.location + '</span></p>' +
        '<p class="card-text">Domain: <span id="userDomain">' + user.domain + '</span></p>' +
        '<button class="btn btn-primary" id="editBtn">Edit</button>' +
        '<button class="btn btn-success" id="saveBtn" style="display:none;">Save</button>';
    profileInfo.html(userProfileHtml);

    // Add event listener for edit button
    $('#editBtn').click(function () {
        // Show input fields for editing
        $('#userAge').replaceWith('<input type="number" class="form-control" id="editUserAge" value="' + user.age + '">');
        $('#userDob').replaceWith('<input type="date" class="form-control" id="editUserDob" value="' + user.dob + '">');
        $('#userDept').replaceWith('<input type="text" class="form-control" id="editUserDept" value="' + user.dept + '">');
        $('#userLocation').replaceWith('<input type="text" class="form-control" id="editUserLocation" value="' + user.location + '">');
        $('#userDomain').replaceWith('<input type="text" class="form-control" id="editUserDomain" value="' + user.domain + '">');

        // Hide edit button and show save button
        $('#editBtn').hide();
        $('#saveBtn').show();
    });

    // Add event listener for save button
    $('#saveBtn').click(function () {
        // Get updated values
        var updatedUser = {
            age: $('#editUserAge').val(),
            dob: $('#editUserDob').val(),
            dept: $('#editUserDept').val(),
            location: $('#editUserLocation').val(),
            domain: $('#editUserDomain').val()
        };

        // Call function to save updated user profile
        saveUserProfile(token, user._id, updatedUser);
    });
}

// Function to save updated user profile
function saveUserProfile(token, userId, updatedUserData) {
    // Send AJAX request to update user profile
    $.ajax({
        url: 'http://localhost:3000/php/update_profile.php',
        type: 'POST',
        headers: {
            'Authorization': 'Bearer ' + token // Ensure token is correctly passed here
        },
        data: { userId: userId, updatedUserData: updatedUserData },
        success: function (response) {
            // Reload the user profile after successful update
            console.log(response + "*********")
            alert(response)
            $('#editBtn').hide();
            // getUserProfile(userId, token);
        },
        error: function (xhr, status, error) {
            console.log(token + "-------------")
            console.error(xhr.responseText + "**********");
            alert('An error occurred while saving the profile.');
        }
    });
}
// function getUserProfile(userId, token) {
//     // Send AJAX request to fetch user profile
//     $.ajax({
//         url: 'http://localhost:3000/php/profile.php',
//         type: 'GET',
//         data: { userId: userId },
//         headers: {
//             'Authorization': 'Bearer ' + token
//         },
//         success: function (response) {
//             alert(response);
//             console.log(response);
//         },
//         error: function (xhr, status, error) {
//             console.error(xhr.responseText);
//             // Handle error if needed
//         }
//     });
// }

