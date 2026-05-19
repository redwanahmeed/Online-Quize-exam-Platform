<?php $page_title = 'Dashboard'; ?>
<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">Welcome back, <?php echo htmlspecialchars(explode(' ', $user['name'])[0]); ?>!</div>
        <div class="page-subtitle">Here's your TA overview for today.</div>
    </div>
</div>

<!-- STAT CARDS -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">◫</div>
        <div>
            <div class="stat-label">Assigned Courses</div>
            <div class="stat-value"><?php echo count($courses); ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold">✎</div>
        <div>
            <div class="stat-label">Total Students</div>
            <div class="stat-value">
                <?php
                $total_students = 0;
                foreach ($courses as $c) $total_students += $c['student_count'];
                echo $total_students;
                ?>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">◉</div>
        <div>
            <div class="stat-label">Your Name</div>
            <div class="stat-value" style="font-size:16px;margin-top:2px;"><?php echo htmlspecialchars($user['name']); ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red">⚑</div>
        <div>
            <div class="stat-label">Role</div>
            <div class="stat-value" style="font-size:16px;margin-top:2px;">Teaching Assistant</div>
        </div>
    </div>
</div>

<!-- ASSIGNED COURSES -->
<div class="card">
    <div class="card-header">
        <div class="card-title">My Assigned Courses</div>
        <a href="/ta_project/controllers/CourseController.php" class="btn btn-outline btn-sm">View All</a>
    </div>
    <?php if (empty($courses)): ?>
    <div class="card-body">
        <div class="empty-state">
            <div class="empty-icon">◫</div>
            <p>You are not assigned to any courses yet.</p>
        </div>
    </div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Subject</th>
                    <th>Instructor</th>
                    <th>Students</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $c): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($c['title']); ?></strong></td>
                    <td><?php echo htmlspecialchars($c['subject_name']); ?></td>
                    <td><?php echo htmlspecialchars($c['instructor_name']); ?></td>
                    <td><?php echo $c['student_count']; ?></td>
                    <td>
                        <?php
                        $statusMap = ['active' => 'badge-green', 'draft' => 'badge-grey', 'archived' => 'badge-navy'];
                        $cls = $statusMap[$c['status']] ?? 'badge-grey';
                        ?>
                        <span class="badge <?php echo $cls; ?>"><?php echo ucfirst($c['status']); ?></span>
                    </td>
                    <td>
                        <a href="/ta_project/controllers/CourseController.php?action=view&course_id=<?php echo $c['id']; ?>" class="btn btn-primary btn-xs">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- QUICK LINKS -->
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:24px;">
    <?php foreach (array_slice($courses, 0, 3) as $c): ?>
    <div class="card" style="padding:0">
        <div class="card-header" style="background:linear-gradient(135deg,#0f2044,#2d4a7a);color:#fff;padding:14px 18px;">
            <div style="font-family:'Playfair Display',serif;font-size:14px;font-weight:700;color:#fff;"><?php echo htmlspecialchars($c['title']); ?></div>
        </div>
        <div class="card-body" style="padding:14px 18px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;">
                <a href="/ta_project/controllers/QuizController.php?action=create&course_id=<?php echo $c['id']; ?>" class="btn btn-outline btn-sm" style="font-size:11px;">+ Quiz</a>
                <a href="/ta_project/controllers/ResultsController.php?course_id=<?php echo $c['id']; ?>" class="btn btn-outline btn-sm" style="font-size:11px;">Results</a>
                <a href="/ta_project/controllers/QAController.php?course_id=<?php echo $c['id']; ?>" class="btn btn-outline btn-sm" style="font-size:11px;">Q&amp;A</a>
                <a href="/ta_project/controllers/DoubtSessionController.php?action=create&course_id=<?php echo $c['id']; ?>" class="btn btn-outline btn-sm" style="font-size:11px;">Session</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
