<!DOCTYPE html>
<html>
<head>
    <title>My Profile - LMS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .nav a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
            padding: 5px 10px;
            border-radius: 5px;
        }
        
        .nav a:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .profile-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            margin-bottom: 15px;
            object-fit: cover;
            background: #f0f0f0;
        }
        
        .profile-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        input:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        
        button:hover {
            transform: translateY(-2px);
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }
        
        .info-text {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
        
        .btn-back {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        
        .btn-back:hover {
            background: #5a6268;
        }
        
        footer {
            text-align: center;
            padding: 20px;
            color: #999;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LMS System</h2>
        <div class="nav">
            <a href="index.php?controller=dashboard&action=index">Dashboard</a>
            <a href="index.php?controller=auth&action=profile">Profile</a>
            <a href="index.php?controller=auth&action=logout">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="profile-card">
            <div class="profile-header">
                <?php if($user['profile_pic']): ?>
                    <img src="uploads/<?php echo $user['profile_pic']; ?>" class="profile-pic" alt="Profile Picture">
                <?php else: ?>
                    <div class="profile-pic" style="display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.2); font-size: 48px;">
                        📷
                    </div>
                <?php endif; ?>
                <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
                <p><?php echo htmlspecialchars($user['role']); ?> | <?php echo htmlspecialchars($user['department'] ?: 'No Department'); ?></p>
            </div>
            
            <div class="profile-body">
                <?php if(isset($success)): ?>
                    <div class="success">✅ <?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if(isset($error)): ?>
                    <div class="error">❌ <?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>👤 Full Name</label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>📧 Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                        <div class="info-text">Email cannot be changed</div>
                    </div>
                    
                    <div class="form-group">
                        <label>👤 Username</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                        <div class="info-text">Username cannot be changed</div>
                    </div>
                    
                    <div class="form-group">
                        <label>🏛️ Department</label>
                        <input type="text" name="department" value="<?php echo htmlspecialchars($user['department']); ?>" placeholder="Your department">
                    </div>
                    
                    <div class="form-group">
                        <label>📝 Bio</label>
                        <textarea name="bio" rows="4" placeholder="Tell us about yourself"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>🖼️ Profile Picture</label>
                        <input type="file" name="profile_pic" accept="image/*">
                        <div class="info-text">Upload JPG, PNG or GIF (Max 2MB)</div>
                    </div>
                    
                    <button type="submit">Update Profile</button>
                </form>
                
                <a href="index.php?controller=dashboard&action=index" class="btn-back">← Back to Dashboard</a>
            </div>
        </div>
    </div>
    
    <footer>
        <p>&copy; 2024 LMS System. All rights reserved.</p>
    </footer>
</body>
</html>