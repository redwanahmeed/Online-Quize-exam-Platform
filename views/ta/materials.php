<?php $page_title = 'Study Materials'; ?>
<div class="breadcrumb">
    <a href="/ta_project/controllers/CourseController.php">Courses</a>
    <span>/</span>
    <a href="/ta_project/controllers/CourseController.php?action=view&course_id=<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['title']); ?></a>
    <span>/</span>
    <span>Materials</span>
</div>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">Study Materials</div>
        <div class="page-subtitle"><?php echo htmlspecialchars($course['title']); ?> &bull; <?php echo count($materials); ?> resource(s) uploaded</div>
    </div>
</div>

<!-- Upload Form -->
<div class="card" style="margin-bottom:24px;">
    <div class="card-header">
        <div class="card-title">Upload New Material</div>
    </div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e): echo '<div>✕ ' . htmlspecialchars($e) . '</div>'; endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="/ta_project/controllers/MaterialController.php?course_id=<?php echo $course['id']; ?>&action=add"
              onsubmit="return validateMaterialForm()">
            <div class="form-row">
                <div class="form-group">
                    <label for="mat_title">Resource Title *</label>
                    <input type="text" id="mat_title" name="title" maxlength="150"
                           placeholder="e.g. Week 4 Cheat Sheet"
                           value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="material_type">Type</label>
                    <select id="material_type" name="material_type">
                        <option value="document" <?php echo (($_POST['material_type'] ?? '') === 'document') ? 'selected' : ''; ?>>📄 Document</option>
                        <option value="link"     <?php echo (($_POST['material_type'] ?? '') === 'link') ? 'selected' : ''; ?>>🔗 Link</option>
                        <option value="video"    <?php echo (($_POST['material_type'] ?? '') === 'video') ? 'selected' : ''; ?>>🎬 Video</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="file_path">File Path or URL *</label>
                <input type="text" id="file_path" name="file_path"
                       placeholder="e.g. /uploads/cheatsheet.pdf or https://example.com/resource"
                       value="<?php echo htmlspecialchars($_POST['file_path'] ?? ''); ?>" required>
                <div class="form-hint">Enter the file path (if uploaded to server) or a full URL for links.</div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-accent">⇪ Upload Material</button>
            </div>
        </form>
    </div>
</div>

<!-- Materials List -->
<div class="card">
    <div class="card-header">
        <div class="card-title">All Materials</div>
    </div>
    <?php if (empty($materials)): ?>
    <div class="card-body">
        <div class="empty-state">
            <div class="empty-icon">⇪</div>
            <p>No materials uploaded yet. Add your first resource above.</p>
        </div>
    </div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Title</th><th>Type</th><th>Uploaded By</th><th>Date</th><th>Link</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($materials as $m):
                    $type_icon = ['document' => '📄', 'link' => '🔗', 'video' => '🎬'];
                    $icon = $type_icon[$m['material_type']] ?? '📄';
                    $ta_id = current_user_id();
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($m['title']); ?></strong></td>
                    <td>
                        <span class="badge badge-navy"><?php echo $icon; ?> <?php echo ucfirst($m['material_type']); ?></span>
                    </td>
                    <td class="text-small"><?php echo htmlspecialchars($m['uploader_name']); ?></td>
                    <td class="text-small"><?php echo date('d M Y', strtotime($m['created_at'])); ?></td>
                    <td class="text-small">
                        <a href="<?php echo htmlspecialchars($m['file_path']); ?>" target="_blank"
                           style="color:var(--slate);text-decoration:none;">
                            View ↗
                        </a>
                    </td>
                    <td style="display:flex;gap:6px;">
                        <?php if ($m['uploaded_by'] == $ta_id): ?>
                        <a href="/ta_project/controllers/MaterialController.php?course_id=<?php echo $course['id']; ?>&action=edit&material_id=<?php echo $m['id']; ?>" class="btn btn-outline btn-xs">Edit</a>
                        <a href="/ta_project/controllers/MaterialController.php?course_id=<?php echo $course['id']; ?>&action=delete&material_id=<?php echo $m['id']; ?>"
                           class="btn btn-danger btn-xs" onclick="return confirm('Delete this material?')">Delete</a>
                        <?php else: ?>
                        <span class="text-muted text-small">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
