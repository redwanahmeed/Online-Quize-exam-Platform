<!DOCTYPE html>
<html>
<head>
    <title>Login - LMS</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:Arial;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh;display:flex;justify-content:center;align-items:center;}
        .container{background:white;border-radius:10px;padding:40px;width:400px;}
        h2{text-align:center;margin-bottom:20px;}
        input{width:100%;padding:10px;margin:10px 0;border:1px solid #ddd;border-radius:5px;}
        button{width:100%;padding:10px;background:#667eea;color:white;border:none;border-radius:5px;cursor:pointer;}
        .error{color:red;margin:10px 0;}
        .success{color:green;margin:10px 0;}
        .link{text-align:center;margin-top:15px;}
        .link a{color:#667eea;text-decoration:none;}
    </style>
</head>
<body>
    <div class="container">
        <h2>Login to LMS</h2>
        <?php if(isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if(isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="email" placeholder="Email or Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
      <div class="link">
            <p>Don't have an account? <a href="index.php?controller=auth&action=register">Register here</a></p>
            <p style="margin-top:10px;font-size:12px;color:#999;">Demo: suchi / admin</p>
        </div>
    </div> 
</body>
</html>