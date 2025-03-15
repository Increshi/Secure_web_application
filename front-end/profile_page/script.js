// Fetch user data on page load
document.addEventListener('DOMContentLoaded', () => {
    fetchUserData();
});

// Fetch user data function
async function fetchUserData() {

    const auth_token = sessionStorage.getItem('auth_token');

    if(auth_token) {
        try {
            const response = await fetch('http://localhost:8080/index.php?request=user_prof', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${auth_token}`,
                    'Content-Type': 'application/json'
                }
            });
    
            const data = await response.json();
    
            if (response.ok) {
                // console.log('User Prof:', data);
                console.log('Name:', data.fullname);
                console.log('Username:', data.username);
                console.log('User email:', data.email);
                
                // Handle user data (e.g., display it in the UI)
                document.getElementById('fname').value = data.fullname;
                document.getElementById('username').value = data.username;
                document.getElementById('email').value = data.email;
                if(data.profile_image)
                {
                    document.getElementById('profile-img').src = data.profile_image;
                }
                if(data.bio)
                    {
                        document.getElementById('bio').value = data.bio;
                    }

            } else {
                console.log("ERROR")
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

const profileForm = document.getElementById('profileForm');

profileForm.addEventListener('submit', (event) => {
    event.preventDefault();
    const auth_token = sessionStorage.getItem('auth_token');

    const fullname = document.getElementById('fname')?.value;
    const newEmail = document.getElementById('email')?.value;
    const newBio = document.getElementById('bio')?.value;
    const newImage = document.getElementById('file')?.files[0];
    const currPassword = document.getElementById('currentPassword')?.value;
    const newPassword = document.getElementById('Npassword')?.value;

    const formData = new FormData();
    formData.append('name', fullname);
    formData.append('email', newEmail);
    formData.append('bio', newBio);
    formData.append('currPass', currPassword);
    if (newImage) {
        formData.append('image', newImage);
    }
    if (newPassword) {
        formData.append('password', newPassword);
    }

    fetch(`http://localhost:8080/index.php?request=update_prof`, {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${auth_token}`,
            'Content-Type': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log(data.uname);
    });
    // fill here to get the response

    console.log("FormData contents:");
    for (const [key, value] of formData.entries()) {
        console.log(`${key}:`, value);
    }



    alert("DONE!!");
});

// Go back to the previous page
function goBack() {
    window.history.back();
}