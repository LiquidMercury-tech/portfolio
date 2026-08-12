<?php
$dbFile = __DIR__ . '/blogs.db';

try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$pdo->exec("CREATE TABLE IF NOT EXISTS blogs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$edit = null;

if (isset($_POST['create'])) {
    $stmt = $pdo->prepare("INSERT INTO blogs (title, content) VALUES (?, ?)");
    $stmt->execute([$_POST['title'], $_POST['content']]);
    header("Location: blogs.php");
    exit;
}

if (isset($_POST['update'])) {
    $stmt = $pdo->prepare("UPDATE blogs SET title = ?, content = ? WHERE id = ?");
    $stmt->execute([$_POST['title'], $_POST['content'], $_POST['id']]);
    header("Location: blogs.php");
    exit;
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: blogs.php");
    exit;
}

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit = $stmt->fetch();
}

$blogs = $pdo->query("SELECT * FROM blogs ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dipson — Blog</title>
<link rel="stylesheet" href="css/1style.css">
</head>
<body>

<nav class="card-tabs">
  <div class="card-tabs__inner">
    <a href="home.html" class="brand"><span class="dot">●</span> Dipson<span aria-hidden="true">.</span></a>
    <a href="home.html" class="tab">Home</a>
    <a href="about.html" class="tab">About</a>
    <a href="skills.html" class="tab">Skills</a>
    <a href="projects.html" class="tab">Projects</a>
    <a href="blogs.php" class="tab is-active">Blog</a>
    <a href="gallery.html" class="tab">Gallery</a>
    <a href="contact.html" class="tab">Contact</a>
  </div>
</nav>

<main>
  <section class="section">
    <div class="container">
      <div class="section__head reveal">
        <span class="eyebrow"><span class="index-number">05</span> — Blog</span>
        <h2>Notes, updates, and things worth writing down.</h2>
      </div>
    </div>
  </section>

  <section class="section section--tint">
    <div class="container">
      <div class="reveal">
        <div class="blog-form">
          <?php if ($edit): ?>
            <span class="eyebrow" style="display:block;margin-bottom:18px;">Edit Entry</span>
            <form method="post">
              <input type="hidden" name="id" value="<?php echo $edit['id']; ?>">
              <div class="field">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($edit['title']); ?>" required>
              </div>
              <div class="field">
                <label for="content">Content</label>
                <textarea id="content" name="content" required><?php echo htmlspecialchars($edit['content']); ?></textarea>
              </div>
              <div class="form-actions">
                <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
                <a href="blogs.php" class="btn btn-ghost">Cancel</a>
              </div>
            </form>
          <?php else: ?>
            <span class="eyebrow" style="display:block;margin-bottom:18px;">New Entry</span>
            <form method="post">
              <div class="field">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" placeholder="Title" required>
              </div>
              <div class="field">
                <label for="content">Content</label>
                <textarea id="content" name="content" placeholder="Write something..." required></textarea>
              </div>
              <button type="submit" name="create" class="btn btn-primary">Publish</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section><br>
  <br>

  <section class="section">
    <nav class="container">
      <?php if (count($blogs) === 0): ?>
        <div class="empty-state reveal">No entries yet. Write your first note above.</div>
      <?php endif; ?>

      <?php foreach ($blogs as $blog): ?>
        <article class="record reveal">
          <span class="record__tag"><?php echo date('M d', strtotime($blog['created_at'])); ?></span>
          <div>
            <h3><?php echo htmlspecialchars($blog['title']); ?></h3>
            <p><?php echo htmlspecialchars($blog['content']); ?></p>
            <div class="post-actions">
              <a href="?edit=<?php echo $blog['id']; ?>">Edit</a>
              <a href="?delete=<?php echo $blog['id']; ?>" onclick="return confirm('Delete this entry?')">Delete</a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
      </nav>
      <br>
      <br>
  </section>
</main>

<footer class="site-footer">
  <div class="container">
    <div class="site-footer__top">
      <a href="../home.html" class="brand">Dipson.</a>
      <div class="social-links">
        <a href="#">GitHub</a>
        <a href="#">LinkedIn</a>
        <a href="#">Twitter</a>
      </div>
    </div>
    <div class="site-footer__bottom">
      <span>© 2026 Dipson. All rights reserved.</span>
      <span>Kathmandu, Nepal</span>
    </div>
  </div>
</footer>

</body>
</html>