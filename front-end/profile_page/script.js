const profileImg = document.getElementById('profile-img');
const profileForm = document.getElementById('profile-form');
const nameInput = document.getElementById('name');
const usernameInput = document.getElementById('username');
const emailInput = document.getElementById('email');
const bioInput = document.getElementById('bio');
const fileInput = document.getElementById('file');
const usersList = document.getElementById('users-list');
const errorMessage = document.createElement('div'); // Error message element
errorMessage.classList.add('error-message');
document.body.appendChild(errorMessage);

// Simulating a logged-in user ID
const token = sessionStorage.getItem('auth_token');

// Fetch current profile data

function fetchProfile() {
    fetch(`http://localhost:8080/index.php?request=get_profile`, {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`, // Send token in the authorization header
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error('Error fetching profile:', data.error);
            return;
        }

        // Fetch the profile image asynchronously
        fetchProfileImage(data.profile_image).then(imageUrl => {
            profileImg.src = imageUrl; // Set image only after fetching it
        });

        // Pre-fill the form fields with current data
        nameInput.value = data.name;
        emailInput.value = data.email;
        if (data.bio !== '') {
            bioInput.value = data.bio;
        }
        usernameInput.value = data.username; // Cannot change
    })
    .catch(error => console.error('Error fetching profile:', error));
}

// Handle form submission to update profile
profileForm.addEventListener('submit', (event) => {
    event.preventDefault();

    const name = nameInput.value;
    const email = emailInput.value;
    const bio = bioInput.value;
    const image = fileInput.files[0];

    const formData = new FormData();
    formData.append('name', name);
    formData.append('email', email);
    formData.append('biography', bio);
    if (image) {
        formData.append('profile_image', image);
    }

    fetch('http://localhost:8080/index.php?request=update_profile', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`, // Ensure token is included in the header
        },
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error updating profile:', data.error);
                errorMessage.textContent = 'Error: Could not update profile.';
                return;
            }
            if(data.message == "File upload failed")
            {
                alert("File upload failed");
            }
            alert('Profile updated successfully!');
            fetchProfile();  // Refresh profile
            errorMessage.textContent = '';  // Clear error message on success
        })
        .catch(error => {
            console.error('Error updating profile:', error);
            errorMessage.textContent = 'Error: Could not update profile.';
        });
});

// Fetch list of other users' profiles
function fetchOtherProfiles() {
    fetch(`http://localhost:8080/index.php?request=get_profile&user_id=ALL`, {
        headers: {
            'Authorization': `Bearer ${token}`, // Send token in the authorization header
        }
    })
        .then(response => response.json())
        .then(users => {

            usersList.innerHTML = ''; // Clear previous list
            
            if (users.length === 0) {
                usersList.innerHTML = '<li>No users available.</li>';
                return;
            }
            
            users.forEach(user => {
                // const imageUrl = fetchProfileImage(user.profile_image);
                const userElement = document.createElement('li');
                userElement.innerHTML = `
                    <strong>${user.name}</strong><br>
                    <strong>${user.username}</strong><br>
                    <strong>${user.email}</strong><br>
                    <p>${user.bio || 'No Biography'}</p>
                `;
                usersList.appendChild(userElement);
            });
        })
        .catch(error => console.error('Error fetching users:', error));
}

function fetchProfileImage(profile_image) {
    if (profile_image && profile_image !== '../images/user_image.jpg') {
        return fetch(`http://localhost:8080/index.php?request=get_image&image=${profile_image}`, { 
            method: 'GET'
        })
        .then(response => {
            if (response.ok) {
                return response.blob(); // Convert response to Blob
            }
            throw new Error("Image not found");
        })
        .then(imageBlob => {
            return URL.createObjectURL(imageBlob); // Generate URL from Blob
        })
        .catch(error => {
            console.error("Error fetching image:", error);
            return '../images/user_image.jpg'; // Return default image in case of an error
        });
    } else {
        return Promise.resolve('../images/user_image.jpg'); // Return a resolved promise for default image
    }
}



// Initial fetch to load the profile when the page loads
document.addEventListener('DOMContentLoaded', () => {
    fetchProfile();
});



// Go back to the previous page
function goBack() {
    window.location.href = '../dashboard_page/index.html';
}