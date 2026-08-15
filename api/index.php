<?php
// index.php
// Database configuration — reads from Vercel environment variables in
// production, falls back to local XAMPP defaults for local testing.
$host     = getenv('DB_HOST') ?: 'localhost';
$dbname   = getenv('DB_NAME') ?: 'portfolio_db';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';

// Create connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . htmlspecialchars($e->getMessage()));
}

/**
 * Turn a title into a URL-safe, unique slug.
 * Appends -2, -3, etc. if the base slug is already taken.
 */
function generateUniqueSlug(PDO $conn, string $title, ?int $ignoreId = null): string
{
    $base = strtolower(trim($title));
    $base = preg_replace('/[^a-z0-9]+/', '-', $base);
    $base = trim($base, '-');
    if ($base === '') {
        $base = 'post';
    }

    $slug = $base;
    $suffix = 2;

    while (true) {
        $sql = "SELECT id FROM blogs WHERE slug = ?" . ($ignoreId ? " AND id != ?" : "");
        $stmt = $conn->prepare($sql);
        $params = $ignoreId ? [$slug, $ignoreId] : [$slug];
        $stmt->execute($params);
        if (!$stmt->fetch()) {
            break;
        }
        $slug = $base . '-' . $suffix;
        $suffix++;
    }

    return $slug;
}

// Handle Blog CRUD Operations
$message = '';
$errorMessage = '';
$editMode = false;
$editPost = null;

// DELETE
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM blogs WHERE id = ?");
        $stmt->execute([(int) $_GET['delete']]);
    } catch (PDOException $e) {
        // fall through — redirect regardless so the URL doesn't stay dirty
    }
    header("Location: index.php#blog");
    exit;
}

// EDIT - Load post data
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
    $stmt->execute([(int) $_GET['edit']]);
    $editPost = $stmt->fetch();
    $editMode = (bool) $editPost; // only true if a matching row was actually found
    if (!$editPost) {
        $errorMessage = "That post couldn't be found — it may have already been deleted.";
    }
}

// CREATE or UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['blog_submit'])) {
    $title    = trim($_POST['title'] ?? '');
    $postDate = trim($_POST['date'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $image    = trim($_POST['image'] ?? '');
    $content  = trim($_POST['content'] ?? '');
    $postId   = !empty($_POST['post_id']) ? (int) $_POST['post_id'] : null;

    if ($title === '' || $postDate === '' || $category === '' || $content === '') {
        $errorMessage = "Please fill in all required fields.";
    } else {
        try {
            if ($postId) {
                // UPDATE — keep the existing slug untouched
                $stmt = $conn->prepare(
                    "UPDATE blogs SET title=?, post_date=?, category=?, image=?, content=? WHERE id=?"
                );
                $stmt->execute([$title, $postDate, $category, $image, $content, $postId]);
                $message = "Post updated successfully!";
            } else {
                // CREATE — generate a fresh, unique slug from the title
                $slug = generateUniqueSlug($conn, $title);
                $stmt = $conn->prepare(
                    "INSERT INTO blogs (title, slug, post_date, category, image, content) VALUES (?, ?, ?, ?, ?, ?)"
                );
                $stmt->execute([$title, $slug, $postDate, $category, $image, $content]);
                $message = "Post created successfully!";
            }
            header("Location: index.php#blog");
            exit;
        } catch (PDOException $e) {
            $errorMessage = "Something went wrong while saving the post. Please try again.";
        }
    }
}

// Handle Contact Form
$contactMessage = '';
$contactError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $messageText = trim($_POST['message'] ?? '');

    if ($name === '' || $subject === '' || $messageText === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $contactError = "Please fill in all fields with a valid email address.";
    } else {
        try {
            $stmt = $conn->prepare(
                "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$name, $email, $subject, $messageText]);
            $contactMessage = "Message sent successfully! I'll get back to you soon.";
        } catch (PDOException $e) {
            $contactError = "Something went wrong while sending your message. Please try again.";
        }
    }
}

// Fetch all blogs (most recent first)
$blogs = $conn->query("SELECT * FROM blogs ORDER BY post_date DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dipson Mishra — Aspiring Data Scientist</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-brand">Dipson<span class="accent">.</span>M</div>
        <button class="nav-toggle" onclick="toggleMenu()" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>
        <ul class="nav-menu" id="navMenu">
            <li><a href="#home" class="nav-link" onclick="closeMenu()">Home</a></li>
            <li><a href="#about" class="nav-link" onclick="closeMenu()">About</a></li>
            <li><a href="#skills" class="nav-link" onclick="closeMenu()">Skills</a></li>
            <li><a href="#projects" class="nav-link" onclick="closeMenu()">Projects</a></li>
            <li><a href="#blog" class="nav-link" onclick="closeMenu()">Blog</a></li>
            <li><a href="#gallery" class="nav-link" onclick="closeMenu()">Gallery</a></li>
            <li><a href="#contact" class="nav-link" onclick="closeMenu()">Contact</a></li>
        </ul>
    </nav>

    <!-- Home Section -->
    <section id="home" class="section home-section">
        <div class="home-content">
            <p class="terminal-line">$ whoami</p>
            <img src="https://placehold.co/300x300/141a24/3ddc84?text=DM" alt="Profile" class="profile-img">
            <h1>Dipson Mishra</h1>
            <p class="tagline">Aspiring Data Scientist</p>
            <p class="intro">I work with data, models, and code to find signal in the noise — with a growing focus on financial modeling and fintech.</p>
            <div class="hero-cta">
                <a href="#projects" class="btn btn-primary">View My Work</a>
                <a href="#contact" class="btn btn-secondary">Get In Touch</a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="section about-section">
        <div class="container">
            <span class="eyebrow">// about</span>
            <h2 class="section-title">About Me</h2>
            <div class="about-grid">
                <div class="about-card">
                    <i class="fas fa-graduation-cap"></i>
                    <h3>Education</h3>
                    <p><strong>B.Sc. in Data Science</strong> — Tribhuvan University (2024 – Present)</p>
                    <p><strong>Higher Secondary (PCM &amp; Computer Science)</strong> — NIST College (2022 – 2024)</p>
                </div>
                <div class="about-card">
                    <i class="fas fa-bullseye"></i>
                    <h3>Career Objective</h3>
                    <p>I'm working toward a career in fintech, building financial models and data-driven tools that turn raw numbers into decisions people can actually act on. I'm particularly drawn to the intersection of data science and finance, and I'm always looking to sharpen that edge.</p>
                </div>
                <div class="about-card">
                    <i class="fas fa-heart"></i>
                    <h3>Hobbies &amp; Interests</h3>
                    <p>Chess, music, video games, football, and reading — mostly philosophy, psychology, and manhuas.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Skills Section -->
    <section id="skills" class="section skills-section">
        <div class="container">
            <span class="eyebrow">// stack</span>
            <h2 class="section-title">Skills</h2>
            <div class="skills-grid">
                <div class="skill-chip"><i class="fa-brands fa-python"></i><span>Python</span></div>
                <div class="skill-chip"><i class="fa-solid fa-c"></i><span>C</span></div>
                <div class="skill-chip"><i class="fa-brands fa-r-project"></i><span>R</span></div>
                <div class="skill-chip"><i class="fa-solid fa-code"></i><span>HTML / CSS / JS</span></div>
                <div class="skill-chip"><i class="fa-solid fa-database"></i><span>SQL</span></div>
                <div class="skill-chip"><i class="fa-solid fa-chart-column"></i><span>Power BI</span></div>
                <div class="skill-chip"><i class="fa-solid fa-magnifying-glass-chart"></i><span>EDA</span></div>
                <div class="skill-chip"><i class="fa-solid fa-table"></i><span>Pandas &amp; NumPy</span></div>
                <div class="skill-chip"><i class="fa-solid fa-brain"></i><span>Machine Learning</span></div>
                <div class="skill-chip"><i class="fa-solid fa-square-root-variable"></i><span>Statistics</span></div>
                <div class="skill-chip"><i class="fa-solid fa-file-excel"></i><span>Excel</span></div>
                <div class="skill-chip"><i class="fa-brands fa-git-alt"></i><span>Git &amp; GitHub</span></div>
            </div>
        </div>
    </section>

    <!-- Projects Section -->
    <section id="projects" class="section projects-section">
        <div class="container">
            <span class="eyebrow">// projects</span>
            <h2 class="section-title">Projects</h2>
            <div class="projects-grid">
                <div class="project-card">
                    <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400&h=250&fit=crop" alt="Project 1">
                    <div class="project-info">
                        <h3>E-Commerce Platform</h3>
                        <p>A full-featured online store with cart, checkout, and admin dashboard built with PHP and MySQL.</p>
                        <button class="btn btn-secondary" onclick="alert('Project details coming soon!')">View Details</button>
                    </div>
                </div>
                <div class="project-card">
                    <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=400&h=250&fit=crop" alt="Project 2">
                    <div class="project-info">
                        <h3>Analytics Dashboard</h3>
                        <p>Real-time data visualization dashboard using JavaScript charts and REST API integration.</p>
                        <button class="btn btn-secondary" onclick="alert('Project details coming soon!')">View Details</button>
                    </div>
                </div>
                <div class="project-card">
                    <img src="https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=400&h=250&fit=crop" alt="Project 3">
                    <div class="project-info">
                        <h3>Task Manager App</h3>
                        <p>Mobile-responsive task management application with drag-and-drop functionality.</p>
                        <button class="btn btn-secondary" onclick="alert('Project details coming soon!')">View Details</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Section -->
    <section id="blog" class="section blog-section">
        <div class="container">
            <span class="eyebrow">// blog</span>
            <h2 class="section-title">Personal Blog</h2>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <?php if ($errorMessage): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>

            <!-- Blog Form -->
            <div class="blog-form-container">
                <h3><?php echo $editMode ? 'Edit Post' : 'Add New Post'; ?></h3>
                <form method="POST" action="index.php#blog" class="blog-form" id="blogForm" onsubmit="return validateBlogForm()">
                    <input type="hidden" name="post_id" value="<?php echo $editMode ? (int) $editPost['id'] : ''; ?>">

                    <div class="form-group">
                        <input type="text" name="title" id="blogTitle" placeholder="Post Title"
                               value="<?php echo $editMode ? htmlspecialchars($editPost['title']) : ''; ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <input type="date" name="date" id="blogDate"
                                   value="<?php echo $editMode ? date('Y-m-d', strtotime($editPost['post_date'])) : date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group">
                            <input type="text" name="category" id="blogCategory" placeholder="Category"
                                   value="<?php echo $editMode ? htmlspecialchars($editPost['category']) : ''; ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <input type="url" name="image" id="blogImage" placeholder="Image URL"
                               value="<?php echo $editMode ? htmlspecialchars($editPost['image']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <textarea name="content" id="blogContent" rows="4" placeholder="Post Content" required><?php echo $editMode ? htmlspecialchars($editPost['content']) : ''; ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="blog_submit" class="btn btn-primary">
                            <?php echo $editMode ? 'Update Post' : 'Add Post'; ?>
                        </button>
                        <?php if ($editMode): ?>
                            <a href="index.php#blog" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Blog Posts -->
            <div class="blog-grid">
                <?php if (empty($blogs)): ?>
                    <p class="empty-state">No blog posts yet. Add your first one above!</p>
                <?php endif; ?>
                <?php foreach ($blogs as $blog): ?>
                <article class="blog-card">
                    <?php if (!empty($blog['image'])): ?>
                        <img src="<?php echo htmlspecialchars($blog['image']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>">
                    <?php else: ?>
                        <img src="https://placehold.co/500x300/4f46e5/ffffff?text=<?php echo urlencode($blog['category'] ?: 'Blog'); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>">
                    <?php endif; ?>
                    <div class="blog-content">
                        <div class="blog-meta">
                            <span class="blog-date"><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($blog['post_date'])); ?></span>
                            <span class="blog-category"><?php echo htmlspecialchars($blog['category']); ?></span>
                        </div>
                        <h3><?php echo htmlspecialchars($blog['title']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($blog['content'])); ?></p>
                        <div class="blog-actions">
                            <a href="index.php?edit=<?php echo (int) $blog['id']; ?>#blog" class="btn btn-edit"><i class="fas fa-edit"></i> Edit</a>
                            <a href="index.php?delete=<?php echo (int) $blog['id']; ?>#blog" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this post?')"><i class="fas fa-trash"></i> Delete</a>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

<!-- Gallery Section -->
<section id="gallery" class="section gallery-section">
    <div class="container">
        <span class="eyebrow">// gallery</span>
        <h2 class="section-title">Gallery</h2>
        <div class="gallery-grid" style="display: flex; flex-direction: row; gap: 24px; align-items: center; justify-content: center; flex-wrap: nowrap;">
            
            <img src="https://i.pinimg.com/1200x/62/0b/4b/620b4b6698eb2ad65be08f4acd1d029b.jpg" alt="Gallery 1" style="width: 600px; height: 600px; object-fit: cover; margin: 20px; border-radius: 12px; display: block; flex-shrink: 0;">
            
            <img src="https://i.pinimg.com/1200x/1d/55/09/1d55091cd7e15913bd418d688e9ea765.jpg" alt="Gallery 2" style="width: 600px; height: 600px; object-fit: cover; border-radius: 12px; margin: 20px; display: block; transform: rotate(270deg); flex-shrink: 0;">
            
            <img src="https://i.pinimg.com/1200x/88/db/2c/88db2ca3f14b2d09135b2e80c7eed12a.jpg" alt="Gallery 3" style="width: 600px; height: 600px; object-fit: cover; border-radius: 12px; margin: 20px; display: block; flex-shrink: 0;">
            
            <img src="https://i.pinimg.com/1200x/b7/2f/43/b72f43257a4225a6cd3a3898f3d8ed84.jpg" alt="Gallery 4" style="width: 600px; height: 600px; object-fit: cover; border-radius: 12px; margin: 20px; display: block; flex-shrink: 0;">
            
        </div>
    </div>
</section>
    <!-- Contact Section -->
    <section id="contact" class="section contact-section">
        <div class="container">
            <span class="eyebrow">// contact</span>
            <h2 class="section-title">Contact Me</h2>

            <?php if ($contactMessage): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($contactMessage); ?></div>
            <?php endif; ?>
            <?php if ($contactError): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($contactError); ?></div>
            <?php endif; ?>

            <div class="contact-details">
                <span><i class="fas fa-envelope"></i> dipsonm136@gmail.com</span>
                <span><i class="fas fa-location-dot"></i> Budhanilakantha, Kathmandu</span>
            </div>

            <form method="POST" action="index.php#contact" class="contact-form" id="contactForm" onsubmit="return validateContactForm()">
                <div class="form-group">
                    <input type="text" name="name" id="contactName" placeholder="Your Name" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" id="contactEmail" placeholder="Your Email" required>
                </div>
                <div class="form-group">
                    <input type="text" name="subject" id="contactSubject" placeholder="Subject" required>
                </div>
                <div class="form-group">
                    <textarea name="message" id="contactMessageField" rows="5" placeholder="Your Message" required></textarea>
                </div>
                <button type="submit" name="contact_submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="social-links">
            <a href="https://github.com/LiquidMercury-tech" target="_blank" rel="noopener" aria-label="GitHub"><i class="fab fa-github"></i></a>
            <a href="https://www.linkedin.com/in/dipson-mishra-b5a1881b6/" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
            <a href="https://www.instagram.com/dusk_deus/" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
        </div>
        <p>&copy; <?php echo date('Y'); ?> Dipson Mishra. All rights reserved.</p>
    </footer>

    <script src="main.js"></script>
</body>
</html>
