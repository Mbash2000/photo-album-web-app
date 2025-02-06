// script.js

// Function to validate the registration form
function validateRegistrationForm() {
    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    if (username === "" || email === "" || password === "") {
        alert("All fields are required!");
        return false;
    }

    if (password.length < 6) {
        alert("Password must be at least 6 characters long!");
        return false;
    }

    return true;
}

// Function to validate the login form
function validateLoginForm() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    if (username === "" || password === "") {
        alert("Username and password are required!");
        return false;
    }

    return true;
}

// Function to handle photo upload form submission
function validateUploadForm() {
    const fileInput = document.getElementById('photo');
    const description = document.getElementById('description').value;

    if (fileInput.files.length === 0) {
        alert("Please select a photo to upload!");
        return false;
    }

    if (description === "") {
        alert("Please add a description for the photo!");
        return false;
    }

    return true;
}

// Function to dynamically load albums and photos
function loadAlbums() {
    fetch('get_albums.php') // Create a PHP file to fetch albums from the database
        .then(response => response.json())
        .then(data => {
            const albumList = document.getElementById('album-list');
            albumList.innerHTML = ""; // Clear existing content

            data.forEach(album => {
                const albumItem = document.createElement('div');
                albumItem.className = 'album-item';
                albumItem.innerHTML = `
                    <h3>${album.name}</h3>
                    <button onclick="viewAlbum(${album.id})">View Album</button>
                `;
                albumList.appendChild(albumItem);
            });
        })
        .catch(error => console.error('Error loading albums:', error));
}

// Function to view photos in a specific album
function viewAlbum(albumId) {
    fetch(`get_photos.php?album_id=${albumId}`) // Create a PHP file to fetch photos for a specific album
        .then(response => response.json())
        .then(data => {
            const photoList = document.getElementById('photo-list');
            photoList.innerHTML = ""; // Clear existing content

            data.forEach(photo => {
                const photoItem = document.createElement('div');
                photoItem.className = 'photo-item';
                photoItem.innerHTML = `
                    <img src="${photo.filename}" alt="${photo.description}">
                    <p>${photo.description}</p>
                    <p>Tags: ${photo.tags}</p>
                `;
                photoList.appendChild(photoItem);
            });
        })
        .catch(error => console.error('Error loading photos:', error));
}

// Load albums when the page loads
window.onload = loadAlbums;

// Typing animation for the welcome message
const welcomeText = document.getElementById('welcome-text');
const text = "Welcome to the Photo Album Web App";
let index = 0;

function typeWriter() {
    if (index < text.length) {
        welcomeText.innerHTML += text.charAt(index);
        index++;
        setTimeout(typeWriter, 100); // Adjust typing speed here
    }
}

// Start the typing animation when the page loads
window.onload = () => {
    typeWriter();
};

// Add hover effects to buttons
const buttons = document.querySelectorAll('.btn');

buttons.forEach(button => {
    button.addEventListener('mouseenter', () => {
        button.style.transform = 'scale(1.1)';
        button.style.boxShadow = '0 0 15px rgba(255, 111, 97, 0.5)';
    });

    button.addEventListener('mouseleave', () => {
        button.style.transform = 'scale(1)';
        button.style.boxShadow = 'none';
    });
});
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('index.php?action=upload', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Photo uploaded successfully');
        } else {
            alert('Upload failed');
        }
    });
});
/* Add hover effects to sidebar links
const sidebarLinks = document.querySelectorAll('.sidebar-nav a');

sidebarLinks.forEach(link => {
    link.addEventListener('mouseenter', () => {
        if (!link.classList.contains('active')) {
            link.style.background = '#2575fc';
        }
    });

    link.addEventListener('mouseleave', () => {
        if (!link.classList.contains('active')) {
            link.style.background = 'transparent';
        }
    });
});*/
// Function to validate the upload form
function validateUploadForm() {
    const fileInput = document.getElementById('photo');
    const description = document.getElementById('description').value;
    const tags = document.getElementById('tags').value;

    if (fileInput.files.length === 0) {
        alert("Please select a photo to upload!");
        return false;
    }

    if (description.trim() === "") {
        alert("Please add a description for the photo!");
        return false;
    }

    if (tags.trim() === "") {
        alert("Please add tags for the photo!");
        return false;
    }

    return true;
}
// Add hover effects to sidebar links
const sidebarLinks = document.querySelectorAll('.sidebar-nav a');

sidebarLinks.forEach(link => {
    link.addEventListener('mouseenter', () => {
        if (!link.classList.contains('active')) {
            link.style.background = '#2575fc';
        }
    });

    link.addEventListener('mouseleave', () => {
        if (!link.classList.contains('active')) {
            link.style.background = 'transparent';
        }
    });
});

/*// Function to toggle the sidebar on mobile devices
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('active');
}

// Close sidebar when clicking outside on mobile
document.addEventListener('click', (event) => {
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');

    if (window.innerWidth <= 768 && !sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
        sidebar.classList.remove('active');
    }
});*/