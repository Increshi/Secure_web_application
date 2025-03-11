const profileImg = document.getElementById('profile-img');
const profileName = document.getElementById('profile-name');
const profileUsername = document.getElementById('profile-username');
const profileEmail = document.getElementById('profile-email');
const profileBio = document.getElementById('profile-bio');
const profileForm = document.getElementById('profile-form');
const nameInput = document.getElementById('name');
const usernameInput = document.getElementById('username');
const emailInput = document.getElementById('email');
const bioInput = document.getElementById('bio');
const fileInput = document.getElementById('file');
const currentPasswordInput = document.getElementById('current-password');
const passwordInput = document.getElementById('password');
const usersList = document.getElementById('users-list');
const errorMessage = document.createElement('div'); // Error message element
errorMessage.classList.add('error-message');
document.body.appendChild(errorMessage);

// Simulating a logged-in user ID and current password
const userId = JSON.parse(sessionStorage.getItem('user'));
const currentUserPassword = '';

// Fetch current profile data
function fetchProfile() {
    fetch(`/api/profile/${userId}`)
        .then(response => response.json())
        .then(data => {
            profileImg.src = data.imageUrl;
            profileName.textContent = `Name: ${data.name}`;
            profileUsername.textContent = `Username: ${data.username}`;
            profileEmail.textContent = `Email: ${data.email}`;
            profileBio.textContent = `Biography: ${data.bio}`;
            currentUserPassword = data.password;
            
            // Pre-fill the form fields with current data
            nameInput.value = data.name;
            emailInput.value = data.email;
            bioInput.value = data.bio;
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
    const currentPassword = currentPasswordInput.value;
    const newPassword = passwordInput.value;

    // Check if the current password is correct
    if (currentPassword !== currentUserPassword) {
        errorMessage.textContent = 'Error: Current password is incorrect.';
        return; // Exit if the password is incorrect
    }

    const formData = new FormData();
    formData.append('name', name);
    formData.append('email', email);
    formData.append('bio', bio);
    if (image) {
        formData.append('image', image);
    }
    if (newPassword) {
        formData.append('password', newPassword);
    }

    fetch(`/api/profile/${userId}`, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
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
    fetch('/api/users')
        .then(response => response.json())
        .then(users => {
            usersList.innerHTML = ''; // Clear previous list
            users.forEach(user => {
                const userElement = document.createElement('div');
                userElement.classList.add('user');
                userElement.innerHTML = `
                    <img src="${user.imageUrl}" alt="${user.name}" class="profile-image" />
                    <p>${user.name}</p>
                    <p>${user.username}</p>
                    <p>${user.email}</p>
                    <p>${user.bio}</p>
                `;
                usersList.appendChild(userElement);
            });
        })
        .catch(error => console.error('Error fetching users:', error));
}

// Initial fetch to load the profile when the page loads
document.addEventListener('DOMContentLoaded', () => {
    fetchProfile();
});



// Go back to the previous page
function goBack() {
    window.history.back();
}