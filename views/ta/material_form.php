<?php $page_title = 'Edit Material'; ?>
<div class="breadcrumb">
    <a href="/ta_project/controllers/CourseController.php">Courses</a>
    <span>/</span>
    <a href="/ta_project/controllers/CourseController.php?action=view&course_id=<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['title']); ?></a>
    <span>/</span>
    <a href="/ta_project/controllers/MaterialController.php?course_id=<?php echo $course['id']; ?>">Materials</a>
    <span>/</span>
    <span>Edit</span>
</div>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">Edit Material</div>
    </div>
</div>

<div class="card" style="max-width:640px;">
    <div class="card-header"><div class="card-title">Edit Resource</div></div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e): echo '<div>✕ ' . htmlspecialchars($e) . '</div>'; endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="POST"
              action="/ta_project/controllers/MaterialController.php?course_id=<?php echo $course['id']; ?>&action=edit&material_id=<?php echo $material['id']; ?>"
              onsubmit="return validateMaterialForm()">
            <div class="form-group">
                <label for="mat_title">Resource Title *</label>
                <input type="text" id="mat_title" name="title" maxlength="150" required
                       value="<?php echo htmlspecialchars($_POST['title'] ?? $material['title']); ?>">
            </div>
            <div class="form-group">
                <label for="material_type">Type</label>
                <select id="material_type" name="material_type">
                    <?php $cur_type = $_POST['material_type'] ?? $material['material_type']; ?>
                    <option value="document" <?php echo $cur_type === 'document' ? 'selected' : ''; ?>>📄 Document</option>
                    <option value="link"     <?php echo $cur_type === 'link'     ? 'selected' : ''; ?>>🔗 Link</option>
                    <option value="video"    <?php echo $cur_type === 'video'    ? 'selected' : ''; ?>>🎬 Video</option>
                </select>
            </div>
            <div class="form-group">
                <label for="file_path">File Path or URL *</label>
                <input type="text" id="file_path" name="file_path" required
                       value="<?php echo htmlspecialchars($_POST['file_path'] ?? $material['file_path']); ?>">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-accent">Save Changes</button>
                <a href="/ta_project/controllers/MaterialController.php?course_id=<?php echo $course['id']; ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
