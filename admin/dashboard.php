<?php
session_start();
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Pagination settings
$items_per_page = 10; // Number of items to show per page

// Get current page number from URL, default to 1
$current_contact_page = isset($_GET['contact_page']) && is_numeric($_GET['contact_page']) ? (int)$_GET['contact_page'] : 1;
$current_newsletter_page = isset($_GET['newsletter_page']) && is_numeric($_GET['newsletter_page']) ? (int)$_GET['newsletter_page'] : 1;

// Calculate offset for SQL queries
$contact_offset = ($current_contact_page - 1) * $items_per_page;
$newsletter_offset = ($current_newsletter_page - 1) * $items_per_page;

$contactCount = 0;
$unreadContactCount = 0;
$recentContacts = [];
$newsletterCount = 0;
$recentSubscriptions = [];
$contactTotalPages = 1;
$newsletterTotalPages = 1;
$error = '';

try {
    // Get total count for contact submissions
    $stmt = $pdo->query("SELECT COUNT(*) FROM contact_submissions");
    $contactCount = $stmt->fetchColumn();
    
    // Get unread count for contact submissions
    $stmt = $pdo->query("SELECT COUNT(*) FROM contact_submissions WHERE status = 'unread'");
    $unreadContactCount = $stmt->fetchColumn();

    $contactTotalPages = ceil($contactCount / $items_per_page);
    
    // Get paginated contact submissions
    $stmt = $pdo->prepare("SELECT * FROM contact_submissions ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bindParam(1, $items_per_page, PDO::PARAM_INT);
    $stmt->bindParam(2, $contact_offset, PDO::PARAM_INT);
    $stmt->execute();
    $recentContacts = $stmt->fetchAll();
    
    // Get total count for newsletter subscriptions
    $stmt = $pdo->query("SELECT COUNT(*) FROM newsletter_subscriptions");
    $newsletterCount = $stmt->fetchColumn();
    $newsletterTotalPages = ceil($newsletterCount / $items_per_page);
    
    // Get paginated newsletter subscriptions
    $stmt = $pdo->prepare("SELECT * FROM newsletter_subscriptions ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bindParam(1, $items_per_page, PDO::PARAM_INT);
    $stmt->bindParam(2, $newsletter_offset, PDO::PARAM_INT);
    $stmt->execute();
    $recentSubscriptions = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $error = 'Error fetching data: ' . $e->getMessage();
    // Log the error for debugging
    error_log("Database Error in admin/dashboard.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sport11</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #2c3e50;
            --success-color: #2ecc71;
            --warning-color: #f1c40f;
            --danger-color: #e74c3c;
            --light-bg: #f8f9fa;
            --dark-bg: #2c3e50;
            --text-color: #333;
            --card-bg: #fff;
            --border-color: #dee2e6;
        }

        [data-theme="dark"] {
            --primary-color: #4a90e2;
            --secondary-color: #2c3e50;
            --success-color: #2ecc71;
            --warning-color: #f1c40f;
            --danger-color: #e74c3c;
            --light-bg: #1a1a1a;
            --dark-bg: #2c3e50;
            --text-color: #f8f9fa;
            --card-bg: #2c3e50;
            --border-color: #404040;
        }

        body {
            background-color: var(--light-bg);
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            color: white !important;
        }

        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
        }

        .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-bottom: none;
            padding: 1rem;
        }

        .table {
            color: var(--text-color);
            margin-bottom: 0;
        }

        .table th {
            background-color: var(--light-bg);
            border-top: none;
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
            border-color: var(--border-color);
        }

        .status-dot {
            height: 12px;
            width: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            transition: all 0.3s ease;
        }

        .status-dot.unread {
            background-color: var(--danger-color);
            box-shadow: 0 0 8px var(--danger-color);
        }

        .status-dot.read {
            background-color: var(--success-color);
            box-shadow: 0 0 8px var(--success-color);
        }

        .btn {
            border-radius: 8px;
            padding: 8px 16px;
            transition: all 0.3s ease;
            margin: 2px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .pagination .page-link {
            color: var(--primary-color);
            border: none;
            margin: 0 2px;
            border-radius: 5px;
            transition: all 0.3s ease;
            background-color: var(--card-bg);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .pagination .page-link:hover {
            background-color: var(--light-bg);
            transform: translateY(-2px);
        }

        .message-cell {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .message-cell.expanded {
            white-space: normal;
            overflow: visible;
            text-overflow: clip;
            max-width: none;
            background-color: var(--light-bg);
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .search-container {
            position: relative;
            margin-bottom: 20px;
        }

        .search-container input {
            padding: 12px 20px;
            border-radius: 25px;
            border: 2px solid var(--border-color);
            width: 100%;
            transition: all 0.3s ease;
            background-color: var(--card-bg);
            color: var(--text-color);
        }

        .search-container input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 10px rgba(74, 144, 226, 0.2);
        }

        .search-container i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
        }

        .stats-card {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            text-align: center;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .stats-label {
            color: var(--text-color);
            font-size: 1.1rem;
        }

        /* Dark Mode Toggle Button */
        .theme-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .theme-toggle:hover {
            transform: scale(1.1);
        }

        .theme-toggle i {
            font-size: 1.5rem;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .stats-number {
                font-size: 1.8rem;
            }
            .stats-label {
                font-size: 1rem;
            }
        }

        @media (max-width: 992px) {
            .navbar-brand {
                font-size: 1.3rem;
            }
            .nav-link {
                padding: 0.4rem 0.8rem;
            }
            .stats-card {
                padding: 15px;
            }
            .stats-number {
                font-size: 1.6rem;
            }
            .table th, .table td {
                padding: 0.5rem;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 0.5rem 1rem;
            }
            .navbar-brand {
                font-size: 1.2rem;
            }
            .nav-link {
                padding: 0.3rem 0.6rem;
                font-size: 0.9rem;
            }
            .card-header {
                padding: 0.8rem;
            }
            .card-header h5 {
                font-size: 1.1rem;
            }
            .stats-card {
                margin-bottom: 15px;
            }
            .stats-number {
                font-size: 1.4rem;
            }
            .stats-label {
                font-size: 0.9rem;
            }
            .btn {
                padding: 6px 12px;
                font-size: 0.9rem;
            }
            .table {
                font-size: 0.9rem;
            }
            .message-cell {
                max-width: 200px;
            }
            .pagination .page-link {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .container {
                padding: 0 10px;
            }
            .navbar-brand {
                font-size: 1.1rem;
            }
            .nav-link {
                padding: 0.3rem 0.5rem;
                font-size: 0.8rem;
            }
            .card {
                margin-bottom: 15px;
            }
            .card-header {
                padding: 0.7rem;
            }
            .card-header h5 {
                font-size: 1rem;
            }
            .stats-card {
                padding: 12px;
            }
            .stats-number {
                font-size: 1.2rem;
            }
            .stats-label {
                font-size: 0.8rem;
            }
            .btn {
                padding: 5px 10px;
                font-size: 0.8rem;
            }
            .table {
                font-size: 0.8rem;
            }
            .message-cell {
                max-width: 150px;
            }
            .pagination .page-link {
                padding: 0.3rem 0.6rem;
                font-size: 0.8rem;
            }
            .search-container input {
                padding: 8px 15px;
                font-size: 0.9rem;
            }
        }

        .dark-toggle-btn {
            background: none; border: none; color: var(--primary-color); font-size: 1.1rem; cursor: pointer; margin-left: 10px; transition: color 0.3s; padding: 2px 6px; line-height: 1;
            display: flex; align-items: center; justify-content: center; height: 32px; width: 32px; border-radius: 50%;
        }
        .dark-toggle-btn i { font-size: 1.1rem; }
        [data-theme="dark"] .dark-toggle-btn { color: #f1c40f; }
        [data-theme="dark"] body { background: var(--dark-bg, #2c3e50); color: #f8f9fa; }
        [data-theme="dark"] .navbar, [data-theme="dark"] .card, [data-theme="dark"] .table, [data-theme="dark"] .table th, [data-theme="dark"] .table td {
            background: #232b3e !important; color: #f8f9fa !important;
        }
        [data-theme="dark"] .card-header { background: #1a2233 !important; color: #fff !important; }
        [data-theme="dark"] .nav-link { color: #ffe082 !important; }
        [data-theme="dark"] .btn-primary { background: linear-gradient(135deg, #232b3e, #4a90e2) !important; color: #fff !important; }
        [data-theme="dark"] .table tbody tr:hover { background: #2c3e50 !important; }
        [data-theme="dark"] .form-control { background: #232b3e !important; color: #f8f9fa !important; border-color: #444 !important; }
        [data-theme="dark"] .form-control::placeholder { color: #b0b8c1 !important; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand animate__animated animate__fadeIn" href="#">Sport11 Admin</a>
            <div class="d-flex align-items-center ms-auto">
                <a class="nav-link animate__animated animate__fadeIn" href="#" id="refreshData">
                    <i class="fas fa-sync"></i> Refresh Data
                </a>
                <a class="nav-link animate__animated animate__fadeIn" href="reset_password.php">
                    <i class="fas fa-key"></i> Reset Password
                </a>
                <a class="nav-link animate__animated animate__fadeIn" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <a class="nav-link animate__animated animate__fadeIn" href="visit_stats.php">
                    <i class="fas fa-chart-bar"></i> Visit Statistics
                </a>
                <button class="dark-toggle-btn" id="toggleDarkMode" title="Toggle light/dark mode">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if ($error): ?>
            <div class="alert alert-danger animate__animated animate__fadeIn">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stats-card animate__animated animate__fadeIn">
                    <div class="stats-number"><span id="totalMsgCounter"></span></div>
                    <div class="stats-label">Total Messages</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card animate__animated animate__fadeIn" style="animation-delay: 0.2s">
                    <div class="stats-number"><span id="unreadMsgCounter"></span></div>
                    <div class="stats-label">Unread Messages</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card animate__animated animate__fadeIn" style="animation-delay: 0.4s">
                    <div class="stats-number"><span id="newsletterCounter"></span></div>
                    <div class="stats-label">Newsletter Subscribers</div>
                </div>
            </div>
        </div>

        <!-- Search Container -->
        <div class="search-container animate__animated animate__fadeIn">
            <input type="text" id="adminSearch" class="form-control" placeholder="Search messages and subscribers...">
            <i class="fas fa-search"></i>
        </div>

        <div id="searchResults" class="mb-4" style="display: none;"></div>
        <div id="mainContent">
            <!-- Contact Messages -->
            <div class="card animate__animated animate__fadeIn">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Contact Messages</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentContacts as $contact): ?>
                                <tr class="animate__animated animate__fadeIn">
                                    <td>
                                        <span class="status-dot <?php echo $contact['status']; ?>"></span>
                                        <?php echo ucfirst($contact['status']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($contact['name']); ?></td>
                                    <td><?php echo htmlspecialchars($contact['email']); ?></td>
                                    <td class="message-cell"><?php echo htmlspecialchars($contact['message']); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($contact['created_at'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary mark-read" data-id="<?php echo $contact['id']; ?>" data-status="<?php echo $contact['status']; ?>">
                                            <i class="fas fa-check"></i> Mark <?php echo $contact['status'] === 'unread' ? 'Read' : 'Unread'; ?>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-message" data-id="<?php echo $contact['id']; ?>">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if ($contactTotalPages > 1): ?>
                    <nav class="mt-3">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $contactTotalPages; $i++): ?>
                            <li class="page-item <?php echo $i === $current_contact_page ? 'active' : ''; ?>">
                                <a class="page-link" href="?contact_page=<?php echo $i; ?>&newsletter_page=<?php echo $current_newsletter_page; ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Newsletter Subscribers -->
            <div class="card animate__animated animate__fadeIn" style="animation-delay: 0.2s">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Newsletter Subscribers</h5>
                    <a href="export_newsletter_csv.php" class="btn btn-primary">
                        <i class="fas fa-download"></i> Export Subscribers
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentSubscriptions as $subscription): ?>
                                <tr class="animate__animated animate__fadeIn">
                                    <td><?php echo htmlspecialchars($subscription['email']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $subscription['active'] ? 'success' : 'danger'; ?>">
                                            <?php echo $subscription['active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y H:i', strtotime($subscription['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if ($newsletterTotalPages > 1): ?>
                    <nav class="mt-3">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $newsletterTotalPages; $i++): ?>
                            <li class="page-item <?php echo $i === $current_newsletter_page ? 'active' : ''; ?>">
                                <a class="page-link" href="?contact_page=<?php echo $current_contact_page; ?>&newsletter_page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // Message cell click handler
        $('.message-cell').click(function() {
            $(this).toggleClass('expanded');
        });

        // Mark as read/unread handler
        $('.mark-read').click(function() {
            const button = $(this);
            const id = button.data('id');
            const currentStatus = button.data('status');
            const newStatus = currentStatus === 'unread' ? 'read' : 'unread';

            $.post('update_message_status.php', {
                id: id,
                status: newStatus
            }, function(response) {
                if (response.success) {
                    // Update UI
                    const row = button.closest('tr');
                    const statusDot = row.find('.status-dot');
                    const statusText = row.find('td:first-child').contents().last().text().trim();
                    
                    statusDot.removeClass('read unread').addClass(newStatus);
                    row.find('td:first-child').contents().last().replaceWith(' ' + newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
                    
                    button.data('status', newStatus);
                    button.html('<i class="fas fa-check"></i> Mark ' + (newStatus === 'unread' ? 'Read' : 'Unread'));
                    
                    // Animate the change
                    row.addClass('animate__animated animate__fadeIn');
                }
            });
        });

        // Delete message handler
        $('.delete-message').click(function() {
            if (confirm('Are you sure you want to delete this message?')) {
                const button = $(this);
                const id = button.data('id');

                $.post('delete_message.php', {
                    id: id
                }, function(response) {
                    if (response.success) {
                        // Animate and remove the row
                        const row = button.closest('tr');
                        row.addClass('animate__animated animate__fadeOut');
                        setTimeout(() => row.remove(), 500);
                    }
                });
            }
        });

        // Search functionality
        const searchInput = $('#adminSearch');
        const searchResultsDiv = $('#searchResults');
        const mainContentDiv = $('#mainContent');

        searchInput.on('input', function() {
            const searchTerm = $(this).val();
            if (searchTerm.length > 2 || searchTerm.length === 0) {
                performSearch(searchTerm);
            } else {
                searchResultsDiv.hide();
                mainContentDiv.show();
            }
        });

        function performSearch(term) {
            $.get('search.php', { keyword: term }, function(data) {
                if (data.error) {
                    searchResultsDiv.html('<div class="alert alert-danger">' + data.error + '</div>');
                } else {
                    let html = '<div class="card animate__animated animate__fadeIn">';
                    html += '<div class="card-header"><h5 class="mb-0">Search Results</h5></div>';
                    html += '<div class="card-body">';

                    // Contact submissions
                    if (data.contact_submissions.length > 0) {
                        html += '<h6>Contact Messages</h6>';
                        html += '<div class="table-responsive"><table class="table">';
                        html += '<thead><tr><th>Name</th><th>Email</th><th>Message</th><th>Date</th></tr></thead><tbody>';
                        data.contact_submissions.forEach(function(item) {
                            html += '<tr class="animate__animated animate__fadeIn">';
                            html += '<td>' + item.name + '</td>';
                            html += '<td>' + item.email + '</td>';
                            html += '<td class="message-cell">' + item.message + '</td>';
                            html += '<td>' + new Date(item.created_at).toLocaleString() + '</td>';
                            html += '</tr>';
                        });
                        html += '</tbody></table></div>';
                    }

                    // Newsletter subscriptions
                    if (data.newsletter_subscriptions.length > 0) {
                        html += '<h6 class="mt-4">Newsletter Subscribers</h6>';
                        html += '<div class="table-responsive"><table class="table">';
                        html += '<thead><tr><th>Email</th><th>Date</th></tr></thead><tbody>';
                        data.newsletter_subscriptions.forEach(function(item) {
                            html += '<tr class="animate__animated animate__fadeIn">';
                            html += '<td>' + item.email + '</td>';
                            html += '<td>' + new Date(item.created_at).toLocaleString() + '</td>';
                            html += '</tr>';
                        });
                        html += '</tbody></table></div>';
                    }

                    if (data.contact_submissions.length === 0 && data.newsletter_subscriptions.length === 0) {
                        html += '<div class="alert alert-info">No results found.</div>';
                    }

                    html += '</div></div>';
                    searchResultsDiv.html(html).show();
                    mainContentDiv.hide();
                }
            });
        }

        // Refresh data handler
        $('#refreshData').click(function(e) {
            e.preventDefault();
            const button = $(this);
            button.find('i').addClass('fa-spin');
            setTimeout(() => {
                button.find('i').removeClass('fa-spin');
                location.reload();
            }, 1000);
        });

        // Light/Dark mode toggle (shared with all admin pages)
        const root = document.documentElement;
        const toggleBtn = document.getElementById('toggleDarkMode');
        function setTheme(theme) {
            root.setAttribute('data-theme', theme);
            localStorage.setItem('adminTheme', theme);
            toggleBtn.innerHTML = theme === 'dark' ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
        }
        // On load, set theme from localStorage
        const savedTheme = localStorage.getItem('adminTheme') || 'light';
        setTheme(savedTheme);
        toggleBtn.addEventListener('click', function() {
            const current = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            setTheme(current);
        });
        window.addEventListener('storage', function(e) {
            if (e.key === 'adminTheme') {
                setTheme(e.newValue || 'light');
            }
        });

        function animateCounter(id, end, duration = 700) {
            const el = document.getElementById(id);
            let start = 0;
            const range = end - start;
            let startTime = null;
            function animate(currentTime) {
                if (!startTime) startTime = currentTime;
                const progress = Math.min((currentTime - startTime) / duration, 1);
                el.textContent = Math.floor(progress * range + start).toLocaleString();
                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    el.textContent = end.toLocaleString();
                }
            }
            requestAnimationFrame(animate);
        }
        animateCounter('totalMsgCounter', <?= (int)$contactCount ?>, 700);
        animateCounter('unreadMsgCounter', <?= (int)$unreadContactCount ?>, 700);
        animateCounter('newsletterCounter', <?= (int)$newsletterCount ?>, 700);
    });
    </script>
</body>
</html> 