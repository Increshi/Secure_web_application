// Simulated API Calls
const apiBaseUrl = 'http://localhost:8080/index.php'; // Replace with your actual API endpoint

// Clear form fields
window.onload = function() {
    document.getElementById('email').value = '';
    document.getElementById('password').value = '';
    document.getElementById('username').value = '';
    document.getElementById('loginEmail').value = '';
    document.getElementById('loginPassword').value = '';
};

// Show the registration form
function showRegister() {
    document.getElementById('registerForm').style.display = 'block';
    document.getElementById('loginForm').style.display = 'none';

    document.getElementById('username').value = ''; // Reset username
    document.getElementById('fullname').value = ''; // Reset fullname
    document.getElementById('email').value = ''; // Reset email
    document.getElementById('password').value = ''; // Reset password
}

// Show the login form
function showLogin() {
    document.getElementById('loginForm').style.display = 'block';
    document.getElementById('registerForm').style.display = 'none';

    document.getElementById('email').value = ''; // Reset email
    document.getElementById('password').value = ''; // Reset password
}

// Handle user registration
document.getElementById('registerFormId').addEventListener('submit', async function(event) {
    event.preventDefault();

    const fullname = document.getElementById('fullname').value;
    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    const userData = {fullname, username, email, password };

    // Simulate an API call to register the user
    try {
        const response = await fetch(`${apiBaseUrl}?request=register`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(userData),
        });
        const data = await response.json();
        
        if (response.ok) {
            alert('Registration successful! Please login.');
            showLogin();
        } else {
            alert(data.error || 'Something went wrong.');
        }
    } catch (error) {
        alert('Error during registration: ' + error.message);
    }
});

// Handle user login
document.getElementById('loginFormId').addEventListener('submit', async function(event) {
    event.preventDefault();

    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;

    const loginData = { email, password };

    // Simulate an API call to login the user
    try {
        const response = await fetch(`${apiBaseUrl}?request=login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(loginData),
        });
        const data = await response.json();
        
        if (response.ok) {
            sessionStorage.setItem('auth_token', data.token);
            // Redirect to user dashboard page
            window.location.href = '../dashboard_page/index.html';
        } else {
            alert(data.error || 'Login failed.');
        }
    } catch (error) {
        alert('Error during login: ' + error.message);
    }

});

// Check if the user is already logged in (session management)
document.addEventListener('DOMContentLoaded', function() {
    const user = JSON.parse(sessionStorage.getItem('user'));
    if (user) {
        // Redirect to dashboard profile page
        window.location.href = '../dashboard_page/index.html';
    } else {
        showLogin();
    }
});
