# Sport11 Landing Page - Project Flowchart

## System Architecture Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                    SPORT11 LANDING PAGE                        │
│                         SYSTEM FLOW                            │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   USER ACCESS   │    │  ADMIN ACCESS   │    │  SYSTEM CORE    │
│                 │    │                 │    │                 │
│ • Landing Page  │    │ • Login Panel   │    │ • Database      │
│ • Contact Form  │    │ • Dashboard     │    │ • Email System  │
│ • Newsletter    │    │ • Analytics     │    │ • Security      │
│ • App Download  │    │ • Management    │    │ • Logging       │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
                    ┌─────────────────┐
                    │   WEB SERVER    │
                    │   (Apache)      │
                    └─────────────────┘
                                 │
                    ┌─────────────────┐
                    │   PHP ENGINE    │
                    │   (7.4+)        │
                    └─────────────────┘
                                 │
                    ┌─────────────────┐
                    │   MYSQL DB      │
                    │   (5.7+)        │
                    └─────────────────┘
```

## User Journey Flow

```
┌─────────────────┐
│   USER VISITS   │
│   LANDING PAGE  │
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│  VIEW HERO      │
│  SECTION        │
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│  BROWSE CONTENT │
│  • How to Play  │
│  • App Features │
│  • Reviews      │
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│  USER ACTIONS   │
└─────────┬───────┘
          │
    ┌─────┴─────┐
    │           │
    ▼           ▼
┌─────────┐ ┌─────────┐
│ CONTACT │ │NEWSLETTER│
│  FORM   │ │ SIGNUP  │
└────┬────┘ └────┬────┘
     │           │
     ▼           ▼
┌─────────┐ ┌─────────┐
│ SEND    │ │ SUBMIT  │
│ MESSAGE │ │  EMAIL  │
└────┬────┘ └────┬────┘
     │           │
     ▼           ▼
┌─────────┐ ┌─────────┐
│ DATABASE│ │ DATABASE│
│ STORAGE │ │ STORAGE │
└────┬────┘ └────┬────┘
     │           │
     ▼           ▼
┌─────────┐ ┌─────────┐
│ EMAIL   │ │ SUCCESS │
│ NOTIFY  │ │ MESSAGE │
│ ADMIN   │ │ TO USER │
└─────────┘ └─────────┘
```

## Admin Panel Flow

```
┌─────────────────┐
│  ADMIN LOGIN    │
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│  AUTHENTICATION │
│  • Username     │
│  • Password     │
│  • Session      │
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│   DASHBOARD     │
└─────────┬───────┘
          │
    ┌─────┴─────┐
    │           │
    ▼           ▼
┌─────────┐ ┌─────────┐
│ CONTACT │ │NEWSLETTER│
│MANAGEMENT│ │MANAGEMENT│
└────┬────┘ └────┬────┘
     │           │
     ▼           ▼
┌─────────┐ ┌─────────┐
│ VIEW    │ │ VIEW    │
│MESSAGES │ │SUBSCRIBERS│
└────┬────┘ └────┬────┘
     │           │
     ▼           ▼
┌─────────┐ ┌─────────┐
│ UPDATE  │ │ EXPORT  │
│ STATUS  │ │   CSV   │
└────┬────┘ └────┬────┘
     │           │
     ▼           ▼
┌─────────┐ ┌─────────┐
│ DELETE  │ │ ANALYTICS│
│MESSAGES │ │  VIEW   │
└─────────┘ └─────────┘
```

## Database Schema Flow

```
┌─────────────────────────────────────────────────────────────┐
│                    DATABASE STRUCTURE                       │
└─────────────────────────────────────────────────────────────┘

┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ contact_        │    │ newsletter_     │    │ admin_users     │
│ submissions     │    │ subscriptions   │    │                 │
│                 │    │                 │    │                 │
│ • id (PK)       │    │ • id (PK)       │    │ • id (PK)       │
│ • name          │    │ • email (UNIQUE)│    │ • username      │
│ • email         │    │ • active        │    │ • email         │
│ • message       │    │ • created_at    │    │ • password      │
│ • status        │    └─────────────────┘    │ • reset_token   │
│ • created_at    │                           │ • created_at    │
└─────────────────┘                           └─────────────────┘
         │                                           │
         ▼                                           ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ otp_            │    │ reviews         │    │ analytics       │
│ verifications   │    │                 │    │                 │
│                 │    │                 │    │                 │
│ • id (PK)       │    │ • id (PK)       │    │ • id (PK)       │
│ • email (UNIQUE)│    │ • name          │    │ • page_visits   │
│ • otp           │    │ • message       │    │ • app_downloads │
│ • created_at    │    │ • created_at    │    │ • last_updated  │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │
         ▼
┌─────────────────┐
│ visit_logs      │
│                 │
│ • id (PK)       │
│ • visit_time    │
│ • ip_address    │
│ • user_agent    │
│ • device_type   │
└─────────────────┘
```

## Security Flow

```
┌─────────────────┐
│  USER INPUT     │
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│  INPUT          │
│  SANITIZATION   │
│  • XSS Prevention│
│  • SQL Injection│
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│  VALIDATION     │
│  • Email Format │
│  • Required Fields│
│  • Length Checks│
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│  DATABASE       │
│  OPERATIONS     │
│  • Prepared     │
│    Statements   │
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│  SESSION        │
│  MANAGEMENT     │
│  • Authentication│
│  • Authorization│
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│  PASSWORD       │
│  SECURITY       │
│  • Hashing      │
│  • Salt         │
└─────────────────┘
```

## Email System Flow

```
┌─────────────────┐
│  CONTACT FORM   │
│  SUBMISSION     │
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│  DATABASE       │
│  STORAGE        │
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│  PHPMailer      │
│  INITIALIZATION │
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│  SMTP           │
│  CONFIGURATION  │
│  • Gmail SMTP   │
│  • Authentication│
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│  EMAIL          │
│  COMPOSITION    │
│  • Subject      │
│  • Body         │
│  • Recipients   │
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│  EMAIL          │
│  SENDING        │
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│  SUCCESS/ERROR  │
│  HANDLING       │
└─────────────────┘
```

## File Structure Flow

```
sport11-landingPage/
├── admin/                    # Admin Panel Files
│   ├── login.php            # Admin Authentication
│   ├── dashboard.php        # Main Dashboard
│   ├── visit_stats.php      # Analytics Display
│   ├── forgot_password.php  # Password Recovery
│   ├── reset_password.php   # Password Reset
│   └── [other admin files]  # Management Functions
│
├── config/                   # Configuration
│   └── database.php         # Database Connection
│
├── database/                 # Database Files
│   └── sport11_db.sql       # Database Schema
│
├── css/                      # Stylesheets
│   ├── style.css            # Main Styles
│   ├── dark-mode.css        # Dark Theme
│   └── bootstrap.min.css    # Bootstrap Framework
│
├── js/                       # JavaScript Files
│   ├── jquery-3.6.0.min.js  # jQuery Library
│   ├── bootstrap.min.js     # Bootstrap JS
│   └── owl.carousel.min.js  # Carousel Plugin
│
├── images/                   # Image Assets
│   ├── logo.png             # Company Logo
│   ├── banner1.jpg          # Hero Banner
│   └── [other images]       # UI Elements
│
├── vendor/                   # Dependencies
│   └── phpmailer/           # Email Library
│
├── index.php                # Main Landing Page
├── contact.php              # Contact Form Handler
├── newsletter.php           # Newsletter Handler
└── [other PHP files]        # Additional Functions
```

## Technology Stack Flow

```
┌─────────────────────────────────────────────────────────────┐
│                    TECHNOLOGY STACK                        │
└─────────────────────────────────────────────────────────────┘

┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   FRONTEND      │    │   BACKEND       │    │   DATABASE      │
│                 │    │                 │    │                 │
│ • HTML5         │    │ • PHP 7.4+      │    │ • MySQL 5.7+    │
│ • CSS3          │    │ • Apache        │    │ • phpMyAdmin    │
│ • JavaScript    │    │ • Composer      │    │ • PDO           │
│ • Bootstrap 5   │    │ • PHPMailer     │    │ • Prepared      │
│ • Font Awesome  │    │ • Session Mgmt  │    │   Statements    │
│ • Owl Carousel  │    │ • Security      │    │ • Indexing      │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
                    ┌─────────────────┐
                    │   DEVELOPMENT   │
                    │   TOOLS         │
                    │                 │
                    │ • VS Code       │
                    │ • Git           │
                    │ • XAMPP         │
                    │ • Composer      │
                    └─────────────────┘
```

## User Interaction Flow

```
┌─────────────────┐
│  LANDING PAGE   │
│  LOAD           │
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│  VISITOR        │
│  ANALYTICS      │
│  LOGGED         │
└─────────┬───────┘
          │
          ▼
┌─────────────────┐
│  USER BROWSE    │
│  CONTENT        │
└─────────┬───────┘
          │
    ┌─────┴─────┐
    │           │
    ▼           ▼
┌─────────┐ ┌─────────┐
│ CONTACT │ │NEWSLETTER│
│  FORM   │ │ SIGNUP  │
└────┬────┘ └────┬────┘
     │           │
     ▼           ▼
┌─────────┐ ┌─────────┐
│ FORM    │ │ EMAIL   │
│VALIDATION│ │VALIDATION│
└────┬────┘ └────┬────┘
     │           │
     ▼           ▼
┌─────────┐ ┌─────────┐
│ DATABASE│ │ DATABASE│
│ INSERT  │ │ INSERT  │
└────┬────┘ └────┬────┘
     │           │
     ▼           ▼
┌─────────┐ ┌─────────┐
│ EMAIL   │ │ SUCCESS │
│ SENT    │ │ MESSAGE │
│ TO ADMIN│ │ TO USER │
└─────────┘ └─────────┘
```

This flowchart provides a comprehensive visual representation of your Sport11 Landing Page project, showing the system architecture, user flows, database structure, security measures, and technology stack integration. 