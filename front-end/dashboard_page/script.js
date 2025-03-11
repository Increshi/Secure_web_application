// Fetch user data on page load
document.addEventListener('DOMContentLoaded', () => {
    fetchUserData();
});

// Fetch user data function
function fetchUserData() {
    // This is a mock API endpoint. Replace it with your actual API URL.
    // const apiUrl = 'https://yourapi.com/user';
    
    // fetch(apiUrl)
    //     .then(response => response.json())
    //     .then(data => {
    //         // Display user info
    //         document.getElementById('user-name').textContent = data.name;
    //         document.getElementById('user-image').src = data.imageUrl;
    //         document.getElementById('user-balance').textContent = `Rs.${data.balance}`;
    //     })
    //     .catch(error => {
    //         console.error('Error fetching user data:', error);
    //     });

    const user = JSON.parse(sessionStorage.getItem('user'));

    if(user) {
        // Display user info
        document.getElementById('user-name').textContent = user.name;
        document.getElementById('user-image').src = user.imageUrl;
        document.getElementById('user-balance').textContent = `Rs.${user.balance}`;
    }
    else {
        // If no user is found in sessionStorage, redirect to the login page
        window.location.href = '../login_page/index.html';
    }
}

// Logout function
document.getElementById('logout-btn').addEventListener('click', () => {
    // Clear any stored user data (e.g., localStorage, sessionStorage)
    sessionStorage.removeItem('user');
    
    // Redirect to login page (adjust the URL as per your needs)
    window.location.href = '../login_page/index.html';
});

// Profile page navigation
document.getElementById('profile-btn').addEventListener('click', () => {
    // Redirect to user profile page
    window.location.href = '../profile_page/index.html';
});

// Transfer money page navigation
document.getElementById('transfer-btn').addEventListener('click', () => {
    // Redirect to transfer money page
    window.location.href = '../transfer_page/index.html';
});
