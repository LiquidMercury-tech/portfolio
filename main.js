/* =========================================================
   Single-page Portfolio — main.js
   ========================================================= */

// ---------------------------------------------------------
// Mobile nav menu (called directly from onclick= in the HTML)
// ---------------------------------------------------------
function toggleMenu() {
    document.getElementById('navMenu').classList.toggle('open');
}
function closeMenu() {
    document.getElementById('navMenu').classList.remove('open');
}

// ---------------------------------------------------------
// Blog form validation (called from onsubmit=)
// ---------------------------------------------------------
function validateBlogForm() {
    const title = document.getElementById('blogTitle').value.trim();
    const date = document.getElementById('blogDate').value.trim();
    const category = document.getElementById('blogCategory').value.trim();
    const content = document.getElementById('blogContent').value.trim();

    if (!title || !date || !category || !content) {
        alert('Please fill in the title, date, category, and content before submitting.');
        return false;
    }
    return true;
}

// ---------------------------------------------------------
// Contact form validation (called from onsubmit=)
// ---------------------------------------------------------
function validateContactForm() {
    const name = document.getElementById('contactName').value.trim();
    const email = document.getElementById('contactEmail').value.trim();
    const subject = document.getElementById('contactSubject').value.trim();
    const message = document.getElementById('contactMessageField').value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!name || !subject || !message) {
        alert('Please fill in all fields before sending.');
        return false;
    }
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address.');
        return false;
    }
    return true;
}

// ---------------------------------------------------------
// Animated skill bars via Intersection Observer
// ---------------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
    const bars = document.querySelectorAll('.progress');
    if (bars.length) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const bar = entry.target;
                    bar.style.width = (bar.getAttribute('data-percent') || '0') + '%';
                    observer.unobserve(bar);
                }
            });
        }, { threshold: 0.4 });
        bars.forEach(bar => observer.observe(bar));
    }

    // Close mobile menu automatically once the viewport grows past mobile size
    window.addEventListener('resize', () => {
        if (window.innerWidth > 767) {
            closeMenu();
        }
    });

    // Highlight the current section's nav link while scrolling
    const sections = document.querySelectorAll('.section');
    const navLinks = document.querySelectorAll('.nav-link');
    if (sections.length && navLinks.length) {
        const sectionObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const id = entry.target.getAttribute('id');
                    navLinks.forEach(link => {
                        link.classList.toggle('active', link.getAttribute('href') === '#' + id);
                    });
                }
            });
        }, { threshold: 0.4 });
        sections.forEach(section => sectionObserver.observe(section));
    }
});
