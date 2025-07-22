# Sport11 Landing Page

A complete landing page for Sport11 with contact form, newsletter subscription, and admin panel.

## Features

- Responsive landing page
- Contact form with database storage
- Newsletter subscription system
- Admin panel with dashboard
- Secure password management
- Analytics tracking

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Modern web browser

## Installation

1. **Clone the repository**
   ```bash
   git clone [repository-url]
   cd sport11-landingPage
   ```

2. **Set up the database**
   - Create a new MySQL database named `sport11_db`
   - Import the database structure:
     ```bash
     mysql -u root -p sport11_db < database/sport11_db.sql
     ```

3. **Configure the database connection**
   - Open `config/database.php`
   - Update the database credentials:
     ```php
     $host = 'localhost';
     $dbname = 'sport11_db';
     $username = 'your_username';
     $password = 'your_password';
     ```

4. **Set up the web server**
   - Copy the project to your web server's document root
   - Ensure the web server has read/write permissions for the project directory

5. **Access the website**
   - Open your web browser
   - Navigate to `http://localhost/sport11-landingPage`

## Admin Access

- URL: `http://localhost/sport11-landingPage/admin/login.php`
- Default credentials:
  - Username: `admin`
  - Password: `admin123`

**Important**: Change the default password after first login!

## Directory Structure

```
sport11-landingPage/
├── admin/
│   ├── login.php
│   ├── dashboard.php
│   ├── reset_password.php
│   └── logout.php
├── config/
│   └── database.php
├── database/
│   └── sport11_db.sql
├── css/
├── js/
├── images/
├── contact.php
├── newsletter.php
└── index.html
```

## Security

- All passwords are hashed using PHP's password_hash()
- Input is sanitized to prevent XSS attacks
- SQL injection prevention using prepared statements
- Session management for admin access

## Support

For any issues or questions, please contact the development team.

## License

This project is licensed under the MIT License. 