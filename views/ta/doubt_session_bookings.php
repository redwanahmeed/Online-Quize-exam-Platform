<?php $page_title = 'Session Bookings'; ?>
<div class="breadcrumb">
    <a href="/ta_project/controllers/DoubtSessionController.php">Doubt Sessions</a>
    <span>/</span>
    <span>Bookings</span>
</div>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-title"><?php echo htmlspecialchars($session['title']); ?></div>
        <div class="page-subtitle">
            📚 <?php echo htmlspecialchars($session['course_title']); ?>
            &bull; 📅 <?php echo date('d M Y, H:i', strtotime($session['scheduled_at'])); ?>
            &bull; ⏱ <?php echo $session['duration_minutes']; ?> min
            &bull; 📍 <?php echo $session['location_or_link'] ? htmlspecialchars($session['location_or_link']) : 'TBD'; ?>
        </div>
    </div>
    <div class="page-header-actions">
        <a href="/ta_project/controllers/DoubtSessionController.php?action=edit&session_id=<?php echo $session['id']; ?>" class="btn btn-outline btn-sm">Reschedule</a>
        <a href="/ta_project/controllers/DoubtSessionController.php?action=delete&session_id=<?php echo $session['id']; ?>"
           class="btn btn-danger btn-sm"
           onclick="return confirm('Cancel this session? Booked students will be notified.')">Cancel Session</a>
    </div>
</div>

<!-- Stats -->
<div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon blue">👥</div>
        <div>
            <div class="stat-label">Total Booked</div>
            <div class="stat-value"><?php echo count($bookings); ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold">📊</div>
        <div>
            <div class="stat-label">Max Capacity</div>
            <div class="stat-value"><?php echo $session['max_attendees']; ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">✓</div>
        <div>
            <div class="stat-label">Seats Available</div>
            <div class="stat-value"><?php echo max(0, $session['max_attendees'] - count($bookings)); ?></div>
        </div>
    </div>
</div>

<!-- Capacity bar -->
<div style="margin-bottom:24px;">
    <?php
    $pct = $session['max_attendees'] > 0
         ? min(100, round(count($bookings) / $session['max_attendees'] * 100))
         : 0;
    ?>
    <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px;">
        <span style="font-weight:600;color:var(--navy);">Capacity</span>
        <span class="text-muted"><?php echo count($bookings); ?> / <?php echo $session['max_attendees']; ?> (<?php echo $pct; ?>%)</span>
    </div>
    <div class="progress-bar-wrap">
        <div class="progress-bar" style="width:<?php echo $pct; ?>%;background:<?php echo $pct >= 90 ? 'var(--red)' : ($pct >= 70 ? 'var(--accent)' : 'var(--slate)'); ?>"></div>
    </div>
</div>

<!-- Bookings Table -->
<div class="card">
    <div class="card-header">
        <div class="card-title">Attending Students (<?php echo count($bookings); ?>)</div>
    </div>
    <?php if (empty($bookings)): ?>
    <div class="card-body">
        <div class="empty-state">
            <div class="empty-icon">👤</div>
            <p>No students have booked this session yet.</p>
        </div>
    </div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Student ID</th>
                    <th>Booked At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $i => $b): ?>
                <tr>
                    <td class="text-muted"><?php echo $i + 1; ?></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="sidebar-avatar" style="width:28px;height:28px;font-size:11px;background:var(--slate);flex-shrink:0;">
                                <?php echo strtoupper(substr($b['student_name'], 0, 1)); ?>
                            </div>
                            <strong><?php echo htmlspecialchars($b['student_name']); ?></strong>
                        </div>
                    </td>
                    <td class="text-small"><?php echo htmlspecialchars($b['email']); ?></td>
                    <td><?php echo htmlspecialchars($b['student_no'] ?? '—'); ?></td>
                    <td class="text-small"><?php echo date('d M Y, H:i', strtotime($b['booked_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<div style="margin-top:16px;">
    <a href="/ta_project/controllers/DoubtSessionController.php" class="btn btn-outline">← Back to Sessions</a>
</div>
