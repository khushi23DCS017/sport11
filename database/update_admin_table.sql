-- Add email column to admin_users table
ALTER TABLE admin_users ADD COLUMN email VARCHAR(100) NOT NULL AFTER username;

-- Add reset token columns
ALTER TABLE admin_users 
ADD COLUMN reset_token VARCHAR(100) DEFAULT NULL,
ADD COLUMN reset_expires DATETIME DEFAULT NULL;

-- Update existing admin user with email
UPDATE admin_users SET email = 'khushidadhaniya8@gmail.com' WHERE username = 'admin';

-- Add unique constraint for email
ALTER TABLE admin_users ADD UNIQUE KEY (email); 