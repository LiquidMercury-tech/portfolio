-- =========================================================
-- Portfolio & Blog Database — full rebuild
-- For Clever Cloud (or any host that already provides a fixed database).
-- Matches the columns actually used in index.php:
--   id, title, slug, category, content, image, author, post_date, updated_at
-- =========================================================


-- -------------------------------------------------------
-- Table: blogs
-- -------------------------------------------------------
DROP TABLE IF EXISTS blogs;
CREATE TABLE blogs (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255)   NOT NULL,
    slug        VARCHAR(255)   NOT NULL UNIQUE,
    category    VARCHAR(100)   NOT NULL DEFAULT 'General',
    content     TEXT           NOT NULL,
    image       VARCHAR(255)   DEFAULT NULL,
    author      VARCHAR(100)   DEFAULT 'Dipson Mishra',
    post_date   DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------
-- Table: contact_messages
-- -------------------------------------------------------
DROP TABLE IF EXISTS contact_messages;
CREATE TABLE contact_messages (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(150)   NOT NULL,
    email         VARCHAR(150)   NOT NULL,
    subject       VARCHAR(255)   NOT NULL,
    message       TEXT           NOT NULL,
    submitted_at  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------
-- Sample blog posts
-- -------------------------------------------------------
INSERT INTO blogs (title, slug, category, content, image, author, post_date) VALUES
(
    'Getting Started with PHP 8 and MySQL',
    'getting-started-with-php-8-and-mysql',
    'Web Development',
    'PHP 8 brought a huge number of improvements to the language, including the JIT compiler, union types, named arguments, and match expressions. In this post I walk through setting up a clean PHP 8 + MySQL development environment using PDO, explain why prepared statements matter for security, and share a simple pattern for structuring database access in small to medium projects.',
    NULL,
    'Dipson Mishra',
    '2026-01-12'
),
(
    'Mastering CSS Grid for Responsive Layouts',
    'mastering-css-grid-for-responsive-layouts',
    'CSS & Design',
    'CSS Grid has completely changed how we approach layout on the web. Instead of hacking together floats or flexbox tricks, we can now describe two-dimensional layouts declaratively. This article breaks down the auto-fit and auto-fill keywords, minmax() function, and how to build a fully responsive photo gallery with just a few lines of CSS.',
    NULL,
    'Dipson Mishra',
    '2026-02-03'
),
(
    'A Practical Guide to the Intersection Observer API',
    'practical-guide-intersection-observer-api',
    'JavaScript',
    'Scroll-triggered animations used to mean expensive scroll event listeners and manual math. The Intersection Observer API gives us a performant, declarative way to detect when an element enters or leaves the viewport. In this tutorial we build a reveal effect that triggers smoothly the moment elements scroll into view.',
    NULL,
    'Dipson Mishra',
    '2026-03-18'
),
(
    'Building Secure Contact Forms with PHP',
    'building-secure-contact-forms-with-php',
    'Backend Development',
    'A contact form looks simple on the surface, but handling it safely requires real care. This post walks through validating and sanitizing user input on both the client and server, protecting against SQL injection using prepared statements, and returning clear success and error feedback to the user without a full page reload.',
    NULL,
    'Dipson Mishra',
    '2026-04-22'
),
(
    'Design Systems 101: Building Consistent UI with CSS Variables',
    'design-systems-101-css-variables',
    'CSS & Design',
    'CSS custom properties (variables) are one of the simplest ways to introduce a lightweight design system into any project. In this article we define a palette of primary, accent, and neutral colors, spacing scales, and typography tokens, then show how switching a single variable can retheme an entire site instantly.',
    NULL,
    'Dipson Mishra',
    '2026-05-30'
),
(
    'From Idea to Deployment: Planning a Personal Portfolio Site',
    'from-idea-to-deployment-personal-portfolio',
    'Career & Productivity',
    'A personal portfolio is often the first real full-stack project a developer builds, and also one of the most valuable for job hunting. This post covers how to plan your site''s information architecture, choose which projects to showcase, and structure your codebase so it is easy to extend later.',
    NULL,
    'Dipson Mishra',
    '2026-06-15'
);
