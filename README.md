# Personal Portfolio Website

A responsive personal portfolio built with HTML, CSS, JavaScript, PHP, and MySQL.

## Structure
```
portfolio/
├── home.html
├── about.html
├── skills.html
├── projects.html
├── gallery.html
├── contact.html
├── css/style.css
├── js/script.js
├── images/            ← add your photos here (see below)
└── php/
    ├── config.php         (DB credentials)
    ├── database.sql       (schema + seed blog data)
    ├── blogs.php           (dynamic blog page)
    └── contact_process.php (handles contact form submissions)
```

## Setup (for the PHP/MySQL parts)
1. Install a local server stack (XAMPP, MAMP, WAMP, or `php -S` + MySQL).
2. Create the database: `mysql -u root -p < php/database.sql`
3. Edit `php/config.php` with your MySQL username/password if different from the defaults.
4. Place the site folder inside your server's web root (e.g. `htdocs/portfolio`).
5. Visit `home.html` — static pages work immediately; `php/blogs.php` and the contact form need the PHP server running.

## Images
Drop your own images into `/images` using these filenames (pages fall back to a
color placeholder automatically if a file is missing, so the site still looks fine without them):
- `profile.jpg` — home page portrait
- `project-nepse.jpg`, `project-airquality.jpg`, `project-tunnel-farming.jpg`, `project-dashboard.jpg`
- `gallery-1.jpg` through `gallery-8.jpg`
- `blog-responsive.jpg`, `blog-hsk.jpg`, `blog-nepse.jpg`, `blog-airquality.jpg`, `blog-teaching.jpg`

## Design system
Palette: `#291C0E` (espresso), `#6E473B` (umber), `#A78D78` (clay), `#BEB5A9` (stone), `#E1D4C2` (cream).
Typography: Fraunces (display serif) + Inter (body) + Space Mono (labels/tags).
Concept: a "catalog card / index" theme — nav styled as filing tabs, hero as an ID card, sections numbered like index entries.

## Customizing content
- Blog posts live in the `blog_posts` MySQL table — edit rows directly or through phpMyAdmin.
- Contact messages are saved to the `contact_messages` table.
- All text content (bio, skills, project descriptions) is directly editable in the HTML files.
