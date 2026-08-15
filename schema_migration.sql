-- Run this only if `category` and `image` don't already exist on your `blogs` table.
-- (Skip it if you already ran this in an earlier step — check first with:
--   DESCRIBE blogs;
-- )

ALTER TABLE blogs
  ADD COLUMN category VARCHAR(100) NOT NULL DEFAULT 'General' AFTER slug,
  ADD COLUMN image VARCHAR(255) DEFAULT NULL AFTER content;

-- contact_messages table, in case it wasn't created yet:
CREATE TABLE IF NOT EXISTS contact_messages (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(150)   NOT NULL,
    email         VARCHAR(150)   NOT NULL,
    subject       VARCHAR(255)   NOT NULL,
    message       TEXT           NOT NULL,
    submitted_at  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
