<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TA Login — Online Quiz &amp; Exam Platform</title>
<link rel="stylesheet" href="/ta_project/public/css/style.css">
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="login-logo">
            <h1>Online Quiz &amp;<br>Exam Platform</h1>
            <p>University of Excellence</p>
            <span class="login-badge">Teaching Assistant Portal</span>
        </div>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e): ?>
            <div>✕ <?php echo htmlspecialchars($e); ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="/ta_project/controllers/AuthController.php" onsubmit="return validateLoginForm()">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                       placeholder="you@university.edu" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <label style="display:flex;align-items:center;gap:6px;font-weight:400;font-size:13px;cursor:pointer;">
                    <input type="checkbox" name="remember_me" style="width:auto;accent-color:#0f2044">
                    Remember me for 30 days
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;">
                Sign In
            </button>
        </form>

        <p style="text-align:center;margin-top:20px;font-size:12px;color:#6b7c99;">
            This portal is restricted to Teaching Assistants only.
        </p>
    </div>
</div>
<script src="/ta_project/public/js/main.js"></script>
</body>
</html>
