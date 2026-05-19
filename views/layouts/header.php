<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TA Portal — Online Quiz &amp; Exam Platform</title>
<link rel="stylesheet" href="/ta_project/public/css/style.css">
</head>
<body>
<div class="layout">

<?php if (is_logged_in()): ?>
<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-title">Online Quiz &amp;<br>Exam Platform</div>
        <div class="logo-sub">Teaching Assistant</div>
    </div>

    <div class="sidebar-user">
        <div class="sidebar-avatar"><?php echo strtoupper(substr($_SESSION['user_name'] ?? 'T', 0, 1)); ?></div>
        <div class="sidebar-user-info">
            <div class="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></div>
            <div class="user-role">Teaching Assistant</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Overview</div>
        <a href="/ta_project/controllers/DashboardController.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) === 'DashboardController.php') ? 'active' : ''; ?>">
            <span class="nav-icon">⊞</span> Dashboard
        </a>
        <a href="/ta_project/controllers/ProfileController.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) === 'ProfileController.php') ? 'active' : ''; ?>">
            <span class="nav-icon">◉</span> My Profile
        </a>

        <div class="nav-section-label">Courses</div>
        <a href="/ta_project/controllers/CourseController.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) === 'CourseController.php') ? 'active' : ''; ?>">
            <span class="nav-icon">◫</span> My Courses
        </a>

        <div class="nav-section-label">Teaching</div>
        <a href="/ta_project/controllers/QuizController.php?action=questions&amp;quiz_id=0&amp;course_id=0" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) === 'QuizController.php') ? 'active' : ''; ?>">
            <span class="nav-icon">✎</span> Quizzes
        </a>
        <a href="/ta_project/controllers/ResultsController.php?course_id=0" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) === 'ResultsController.php') ? 'active' : ''; ?>">
            <span class="nav-icon">▤</span> Results
        </a>
        <a href="/ta_project/controllers/QAController.php?course_id=0" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) === 'QAController.php') ? 'active' : ''; ?>">
            <span class="nav-icon">✉</span> Q&amp;A Board
        </a>

        <div class="nav-section-label">Resources</div>
        <a href="/ta_project/controllers/MaterialController.php?course_id=0" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) === 'MaterialController.php') ? 'active' : ''; ?>">
            <span class="nav-icon">⇪</span> Materials
        </a>
        <a href="/ta_project/controllers/AnnouncementController.php?course_id=0" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) === 'AnnouncementController.php') ? 'active' : ''; ?>">
            <span class="nav-icon">⚑</span> Announcements
        </a>
        <a href="/ta_project/controllers/DoubtSessionController.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) === 'DoubtSessionController.php') ? 'active' : ''; ?>">
            <span class="nav-icon">◷</span> Doubt Sessions
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="/ta_project/controllers/AuthController.php?action=logout">
            <span>⇐</span> Logout
        </a>
    </div>
</aside>
<?php endif; ?>

<div class="<?php echo is_logged_in() ? 'main-content' : ''; ?>">
<?php if (is_logged_in()):
    $flash = get_flash();
?>
<div class="topbar">
    <div class="topbar-title">
        <?php echo isset($page_title) ? htmlspecialchars($page_title) : 'TA Portal'; ?>
    </div>
    <div class="topbar-right">
        <span class="badge badge-ta">TA</span>
        <span style="font-size:13px;color:#6b7c99;"><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></span>
    </div>
</div>
<div class="page-body">
<?php if ($flash): ?>
<div class="flash-area">
    <div class="alert alert-<?php echo $flash['type'] === 'success' ? 'success' : 'error'; ?>">
        <?php echo $flash['type'] === 'success' ? '✓' : '✕'; ?>
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>
