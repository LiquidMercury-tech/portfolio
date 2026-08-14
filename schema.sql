-- schema.sql
CREATE DATABASE IF NOT EXISTS portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE portfolio;

-- Blogs Table
CREATE TABLE IF NOT EXISTS blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    category VARCHAR(100) NOT NULL,
    image VARCHAR(500) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Contact Messages Table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample Blog Posts
INSERT INTO blogs (title, date, category, image, content) VALUES
('Getting Started with Web Development', '2024-01-15', 'Tutorial', 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=600&h=400&fit=crop', 'Web development is an exciting field that combines creativity with technical skills. In this post, I share my journey from writing my first HTML tag to building full-stack applications. The key is to start with the fundamentals and practice consistently.'),
('The Power of CSS Grid and Flexbox', '2024-02-20', 'CSS', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&h=400&fit=crop', 'Modern CSS layout techniques have revolutionized how we design web interfaces. CSS Grid and Flexbox provide powerful tools for creating responsive, complex layouts with minimal code. Understanding when to use each is crucial for efficient development.'),
('PHP and MySQL: A Dynamic Duo', '2024-03-10', 'Backend', 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=600&h=400&fit=crop', 'PHP remains one of the most popular server-side languages, and when paired with MySQL, it becomes a robust solution for database-driven applications. This post explores best practices for secure database connections and query optimization.'),
('JavaScript ES6 Features You Should Know', '2024-04-05', 'JavaScript', 'https://images.unsplash.com/photo-1579468118864-1b9ea3c0db4a?w=600&h=400&fit=crop', 'ES6 introduced many powerful features that make JavaScript more readable and maintainable. From arrow functions and destructuring to promises and modules, these features are essential for modern JavaScript development.'),
('Building Responsive Websites', '2024-05-12', 'Design', 'https://images.unsplash.com/photo-1504639725590-34d0984388bd?w=600&h=400&fit=crop', 'Responsive design is no longer optional—it is a necessity. With the variety of devices used to access the web, ensuring your site looks great on all screen sizes is critical. Learn about media queries, fluid layouts, and mobile-first approaches.');