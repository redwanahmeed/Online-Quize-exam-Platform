<!DOCTYPE html>
<html>
<head>
    <title>Register - LMS</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:Arial;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh;display:flex;justify-content:center;align-items:center;}
        .container{background:white;border-radius:10px;padding:40px;width:500px;}
        h2{text-align:center;margin-bottom:20px;}
        input,textarea{width:100%;padding:10px;margin:10px 0;border:1px solid #ddd;border-radius:5px;}
        button{width:100%;padding:10px;background:#667eea;color:white;border:none;border-radius:5px;cursor:pointer;}
        .error{color:red;margin:5px 0;font-size:12px;}
        .link{text-align:center;margin-top:15px;}
        .link a{color:#667eea;text-decoration:none;}
    </style>
</head>
<body>
    <div class="container">
        <h2>Create Account</h2>
        <?php if(isset($errors)): ?>
            <?php foreach($errors as $err): ?>
                <div class="error"><?php echo $err; ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <input type="text" name="department" placeholder="Department">
            <textarea name="bio" rows="3" placeholder="Bio (Optional)"></textarea>
            <button type="submit">Register</button>
        </form>
        <div class="link">
            <p>Already have an account? <a href="index.php?controller=auth&action=login">Login here</a></p>
        </div>
    </div>
</body>
</html>