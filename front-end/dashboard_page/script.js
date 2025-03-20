// Fetch user data on page load
document.addEventListener('DOMContentLoaded', () => {
    fetchUserData();
});

// Fetch user data function
async function fetchUserData() {

    const auth_token = sessionStorage.getItem('auth_token');

    if(auth_token) {
        try {
            const response = await fetch('https://localhost:8080/index.php?request=user_info', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${auth_token}`,
                    'Content-Type': 'application/json'
                }
            });
    
            const data = await response.json();
    
            if (response.ok) {
                console.log('User Info:', data);
                // Handle user data (e.g., display it in the UI)
                document.getElementById('user-name').textContent = data.username;

                if(data.profile_image)
                {
                    // Using fetch to request the image
                    fetch(`https://localhost:8080/index.php?request=get_image&image=${data.profile_image}`,{
                        method: 'GET'
                    })
                    .then(response => {
                    if (response.ok) {
                        return response.blob(); // Get the image as a Blob
                    }
                    throw new Error("Image not found");
                    })
                    .then(imageBlob => {
                    // Create an object URL for the image
                    const imageUrl = URL.createObjectURL(imageBlob);
                    document.getElementById('user-image').src = imageUrl;
                    })
                    .catch(error => {
                    console.error("Error fetching image:", error);
                    });
                }

                document.getElementById('user-balance').textContent = `Rs.${data.balance}`;

            } else {
                console.error('Error:', data.error);
            }
        } 
        catch (error) {
            console.error('Request failed', error);
        }
    }
    else {
        // If no user is found in sessionStorage, redirect to the login page
        window.location.href = '../login_page/index.html';
    }
}

// Logout function
document.getElementById('logout-btn').addEventListener('click',  async function(event) {
    event.preventDefault();

    // Get the token from sessionStorage (assuming it's stored under 'auth_token')
    const token = sessionStorage.getItem('auth_token');

    if (!token) {
        alert("You are not logged in.");
        return;
    }

    try {
        // Make the API call to logout
        const response = await fetch('https://localhost:8080/index.php?request=logout', {
            method: 'POST', // POST request for logout
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`  // Send the token in the Authorization header
            }
        });

        const data = await response.json();

        if (response.ok) {
            // Successfully logged out, clear the sessionStorage
            sessionStorage.removeItem('auth_token');
            sessionStorage.clear();

            localStorage.clear();

            // Modify the browser history so the user can't go back to the previous page
            window.history.replaceState(null, '', '../login_page/index.html');
    
            // Redirect to login page
            window.location.href = '../login_page/index.html';
            alert(data.message);
        } else {
            // Handle error (for example, token not blacklisted, invalid token)
            alert(data.error || 'Logout failed.');
        }
    } catch (error) {
        // Handle network or other errors
        alert('Error during logout: ' + error.message);
    }
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
