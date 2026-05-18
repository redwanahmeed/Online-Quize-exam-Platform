<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <!-- Profile Image Card -->
            <div class="card">
                <div class="card-header">
                    <h5>Profile Picture</h5>
                </div>
                <div class="card-body text-center">
                    <?php if ($user['profile_image'] && file_exists(__DIR__ . '/../../assets/uploads/' . $user['profile_image'])): ?>
                        <img src="assets/uploads/<?php echo $user['profile_image']; ?>" 
                             class="rounded-circle mb-3" 
                             style="width: 150px; height: 150px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mx-auto mb-3"
                             style="width: 150px; height: 150px; font-size: 48px;">
                            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    
                    <h5><?php echo $user['full_name']; ?></h5>
                    <p class="text-muted">
                        <?php 
                        $role_names = [
                            'admin' => 'Administrator',
                            'instructor' => 'Instructor',
                            'teaching_assistant' => 'Teaching Assistant',
                            'student' => 'Student'
                        ];
                        echo $role_names[$user['role']] ?? $user['role'];
                        ?>
                    </p>
                    <p><small>Member since: <?php echo date('F d, Y', strtotime($user['created_at'])); ?></small></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- Update Profile Form -->
            <div class="card">
                <div class="card-header">
                    <h5>Edit Profile Information</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo htmlspecialchars($_GET['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php echo htmlspecialchars($_GET['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="index.php?page=profile&action=update" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Full Name</label>
                                <input type="text" name="full_name" class="form-control" value="<?php echo $user['full_name']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="<?php echo $user['phone'] ?? ''; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Profile Image</label>
                                <input type="file" name="profile_image" class="form-control" accept="image/*">
                                <small class="text-muted">Allowed: jpg, jpeg, png, gif (Max 2MB)</small>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Address</label>
                                <textarea name="address" class="form-control" rows="3"><?php echo $user['address'] ?? ''; ?></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
            
            <!-- Change Password Form -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5>Change Password</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?page=profile&action=change_password">
                        <div class="mb-3">
                            <label>Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                            <small class="text-muted">Password must be at least 6 characters</small>
                        </div>
                        <div class="mb-3">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-warning">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>