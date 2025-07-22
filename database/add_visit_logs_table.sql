CREATE TABLE IF NOT EXISTS visit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visit_time DATETIME NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    INDEX (visit_time)
);

ALTER TABLE visit_logs ADD COLUMN device_type VARCHAR(20) DEFAULT 'Desktop'; 