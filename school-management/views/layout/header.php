<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #2c3e50;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
        }
        .sidebar h5 {
            color: white;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #34495e;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            border-bottom: 1px solid #34495e;
        }
        .sidebar a:hover {
            background-color: #34495e;
        }
        .sidebar a i {
            margin-right: 10px;
        }
        .content {
            margin-left: 250px;
            min-height: 100vh;
        }
        .navbar-custom {
            background: white;
            padding: 15px 20px;
            border-bottom: 1px solid #dee2e6;
        }
        .user-info {
            background-color: #e9ecef;
            padding: 8px 15px;
            border-radius: 5px;
        }
        .role-badge {
            background-color: #6c757d;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            margin-left: 10px;
        }
        .main-content {
            padding: 20px;
        }
        .card {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }
        .footer {
            background: white;
            padding: 15px;
            text-align: center;
            border-top: 1px solid #dee2e6;
            margin-top: 30px;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .content {
                margin-left: 0;
            }
            .mobile-menu-btn {
                display: block;
            }
        }
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1100;
            background: #2c3e50;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 3px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<button class="mobile-menu-btn" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>

<?php if (isset($_SESSION['user_id'])): ?>
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 sidebar" id="sidebar">
            <h5><?php echo SITE_NAME; ?></h5>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="index.php?page=dashboard">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                
                <!-- শুধু Admin এই মেনু দেখতে পাবে -->
                <?php if (hasRole('admin')): ?>
                <li class="nav-item">
                    <a href="index.php?page=students">
                        <i class="fas fa-users"></i> Students
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=courses">
                        <i class="fas fa-book"></i> Courses
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=users">
                        <i class="fas fa-user-shield"></i> Users
                    </a>
                </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a href="index.php?page=profile">
                        <i class="fas fa-user-circle"></i> Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=logout" onclick="return confirm('Are you sure?')">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>
        
        <main class="col-md-9 col-lg-10 content">
            <div class="navbar-custom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Welcome, <?php echo $_SESSION['full_name']; ?></h5>
                    </div>
                    <div class="user-info">
                        <i class="fas fa-user-circle"></i>
                        <?php echo $_SESSION['full_name']; ?>
                        <span class="role-badge">
                            <?php 
                            $role_names = [
                                'admin' => 'Admin',
                                'instructor' => 'Instructor',
                                'teaching_assistant' => 'Teaching Assistant',
                                'student' => 'Student'
                            ];
                            echo $role_names[$_SESSION['role']] ?? $_SESSION['role'];
                            ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="main-content">
                <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

<?php else: ?>
    <div class="container">
        <div class="row">
            <div class="col-12">
<?php endif; ?>

<script>
function toggleSidebar() {
    var sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('show');
}

document.addEventListener('click', function(event) {
    var sidebar = document.getElementById('sidebar');
    var mobileBtn = document.querySelector('.mobile-menu-btn');
    
    if (window.innerWidth <= 768) {
        if (sidebar && mobileBtn && !sidebar.contains(event.target) && !mobileBtn.contains(event.target)) {
            sidebar.classList.remove('show');
        }
    }
});

setTimeout(function() {
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            setTimeout(function() {
                if (alert) alert.remove();
            }, 300);
        }, 5000);
    });
}, 1000);
</script>