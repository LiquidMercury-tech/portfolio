// main.js
// Mobile Menu Toggle
function toggleMenu() {
    const navMenu = document.getElementById('navMenu');
    navMenu.classList.toggle('active');
}

function closeMenu() {
    const navMenu = document.getElementById('navMenu');
    navMenu.classList.remove('active');
}

// Close menu when clicking outside
document.addEventListener('click', function(event) {
    const navMenu = document.getElementById('navMenu');
    const navToggle = document.querySelector('.nav-toggle');
    
    if (!navMenu.contains(event.target) && !navToggle.contains(event.target)) {
        navMenu.classList.remove('active');
    }
});

// Navbar background on scroll
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.style.boxShadow = '0 2px 20px rgba(0,0,0,0.15)';
    } else {
        navbar.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
    }
});

// Contact Form Validation
function validateContactForm() {
    const name = document.getElementById('contactName').value.trim();
    const email = document.getElementById('contactEmail').value.trim();
    const subject = document.getElementById('contactSubject').value.trim();
    const message = document.getElementById('contactMessage').value.trim();

    if (name.length < 2) {
        alert('Name must be at least 2 characters long.');
        return false;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address.');
        return false;
    }

    if (subject.length < 3) {
        alert('Subject must be at least 3 characters long.');
        return false;
    }

    if (message.length < 10) {
        alert('Message must be at least 10 characters long.');
        return false;
    }

    return true;
}

// Blog Form Validation
function validateBlogForm() {
    const title = document.getElementById('blogTitle').value.trim();
    const date = document.getElementById('blogDate').value;
    const category = document.getElementById('blogCategory').value.trim();
    const image = document.getElementById('blogImage').value.trim();
    const content = document.getElementById('blogContent').value.trim();

    if (title.length < 3) {
        alert('Title must be at least 3 characters long.');
        return false;
    }

    if (!date) {
        alert('Please select a date.');
        return false;
    }

    if (category.length < 2) {
        alert('Category must be at least 2 characters long.');
        return false;
    }

    if (!image) {
        alert('Please provide an image URL.');
        return false;
    }

    if (content.length < 10) {
        alert('Content must be at least 10 characters long.');
        return false;
    }

    return true;
}

// Animate progress bars on scroll
const observerOptions = {
    threshold: 0.5
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const progressBars = entry.target.querySelectorAll('.progress');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0';
                setTimeout(() => {
                    bar.style.width = width;
                }, 100);
            });
        }
    });
}, observerOptions);

document.addEventListener('DOMContentLoaded', () => {
    const skillsSection = document.querySelector('.skills-section');
    if (skillsSection) {
        observer.observe(skillsSection);
    }
});