<?php $page_title = 'My Profile'; ?>
<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">My Profile</div>
        <div class="page-subtitle">Manage your account information and password</div>
    </div>
</div>

<?php if ($success): ?>
<div class="alert alert-success">✓ <?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <?php foreach ($errors as $e): echo '<div>✕ ' . htmlspecialchars($e) . '</div>'; endforeach; ?>
</div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">

    <!-- Profile Info -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">Personal Information</div>
        </div>
        <div class="card-body">
            <!-- Avatar display -->
            <div style="text-align:center;margin-bottom:24px;">
                <div class="sidebar-avatar" style="width:72px;height:72px;font-size:28px;margin:0 auto;background:var(--slate);">
                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                </div>
                <div style="margin-top:10px;font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:var(--navy);">
                    <?php echo htmlspecialchars($user['name']); ?>
                </div>
                <div style="font-size:13px;color:var(--muted);"><?php echo htmlspecialchars($user['email']); ?></div>
                <span class="badge badge-ta" style="margin-top:6px;">Teaching Assistant</span>
            </div>

            <form method="POST" action="/ta_project/controllers/ProfileController.php" onsubmit="return validateProfileForm()">
                <input type="hidden" name="action" value="update_profile">

                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" maxlength="100" required
                           value="<?php echo htmlspecialchars($user['name']); ?>">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly
                           style="background:#f4f6fb;cursor:not-allowed;">
                    <div class="form-hint">Email cannot be changed here.</div>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" maxlength="20"
                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                           placeholder="+880 1XXXXXXXXX">
                </div>
                <div class="form-group">
                    <label for="program">Department / Program</label>
                    <input type="text" id="program" name="program" maxlength="100"
                           value="<?php echo htmlspecialchars($user['program'] ?? ''); ?>"
                           placeholder="e.g. Computer Science">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Password -->
    <div>
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <div class="card-title">Change Password</div>
            </div>
            <div class="card-body">
                <form method="POST" action="/ta_project/controllers/ProfileController.php" onsubmit="return validatePasswordForm()">
                    <input type="hidden" name="action" value="change_password">
                    <div class="form-group">
                        <label for="current_password">Current Password *</label>
                        <input type="password" id="current_password" name="current_password" placeholder="••••••••" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password * <span class="text-muted" style="font-weight:400;">(min. 6 characters)</span></label>
                        <input type="password" id="new_password" name="new_password" placeholder="••••••••" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-danger">Change Password</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Account Info -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">Account Details</div>
            </div>
            <div class="card-body">
                <table style="width:100%;border-collapse:collapse;">
                    <tr>
                        <td style="padding:8px 0;font-size:13px;color:var(--muted);width:40%;border-bottom:1px solid var(--border);">User ID</td>
                        <td style="padding:8px 0;font-size:13px;border-bottom:1px solid var(--border);"><?php echo $user['id']; ?></td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;font-size:13px;color:var(--muted);border-bottom:1px solid var(--border);">Role</td>
                        <td style="padding:8px 0;font-size:13px;border-bottom:1px solid var(--border);"><span class="badge badge-ta">Teaching Assistant</span></td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;font-size:13px;color:var(--muted);border-bottom:1px solid var(--border);">Student ID</td>
                        <td style="padding:8px 0;font-size:13px;border-bottom:1px solid var(--border);"><?php echo htmlspecialchars($user['student_id'] ?? '—'); ?></td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;font-size:13px;color:var(--muted);">Joined</td>
                        <td style="padding:8px 0;font-size:13px;"><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
