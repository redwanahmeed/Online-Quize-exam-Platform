<?php
$is_edit    = isset($question) && $question;
$page_title = $is_edit ? 'Edit Question' : 'Add Question';
$opts       = $is_edit ? $existing_options : [];
?>
<div class="breadcrumb">
    <a href="/ta_project/controllers/CourseController.php">Courses</a>
    <span>/</span>
    <a href="/ta_project/controllers/CourseController.php?action=view&course_id=<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['title']); ?></a>
    <span>/</span>
    <a href="/ta_project/controllers/QuizController.php?action=questions&quiz_id=<?php echo $quiz['id']; ?>&course_id=<?php echo $course['id']; ?>"><?php echo htmlspecialchars($quiz['title']); ?></a>
    <span>/</span>
    <span><?php echo $is_edit ? 'Edit Question' : 'Add Question'; ?></span>
</div>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-title"><?php echo $is_edit ? 'Edit Question' : 'Add New Question'; ?></div>
        <div class="page-subtitle">Quiz: <?php echo htmlspecialchars($quiz['title']); ?></div>
    </div>
</div>

<div class="card" style="max-width:760px;">
    <div class="card-header">
        <div class="card-title">Question Details</div>
    </div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e): ?>
            <div>✕ <?php echo htmlspecialchars($e); ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php
        $form_action = $is_edit
            ? "/ta_project/controllers/QuizController.php?action=edit_question&question_id={$question['id']}&quiz_id={$quiz['id']}&course_id={$course['id']}"
            : "/ta_project/controllers/QuizController.php?action=add_question&quiz_id={$quiz['id']}&course_id={$course['id']}";
        ?>
        <form method="POST" action="<?php echo $form_action; ?>" onsubmit="return validateQuestionForm()">

            <div class="form-group">
                <label for="question_text">Question Text *</label>
                <textarea id="question_text" name="question_text" rows="3" placeholder="Enter your question here..." required><?php echo htmlspecialchars($is_edit ? $question['question_text'] : ($_POST['question_text'] ?? '')); ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="marks">Marks *</label>
                    <input type="number" id="marks" name="marks" min="0.5" step="0.5" required
                           value="<?php echo $is_edit ? $question['marks'] : ($_POST['marks'] ?? 1); ?>">
                </div>
                <div class="form-group">
                    <label for="order_index">Order / Position</label>
                    <input type="number" id="order_index" name="order_index" min="0"
                           value="<?php echo $is_edit ? $question['order_index'] : ($_POST['order_index'] ?? 0); ?>">
                    <div class="form-hint">Lower number = shown earlier</div>
                </div>
            </div>

            <div class="form-group">
                <label>Answer Options * <span class="text-muted" style="font-weight:400;">(select the correct answer using the radio button)</span></label>
                <div id="options-list" class="options-list">
                    <?php
                    // Pre-fill options when editing or on POST error
                    if ($is_edit && !empty($opts)):
                        foreach ($opts as $i => $opt):
                    ?>
                    <div class="option-row">
                        <input type="radio" name="correct_option" value="<?php echo $i; ?>" <?php echo $opt['is_correct'] ? 'checked' : ''; ?>>
                        <input type="text" name="options[]" class="option-text-input"
                               placeholder="Option <?php echo $i+1; ?>"
                               value="<?php echo htmlspecialchars($opt['option_text']); ?>" required>
                        <button type="button" class="remove-option-btn" onclick="removeOption(this)">&#x2715;</button>
                    </div>
                    <?php
                        endforeach;
                    elseif (!empty($_POST['options'])):
                        foreach ($_POST['options'] as $i => $opt):
                            $is_correct = (isset($_POST['correct_option']) && (int)$_POST['correct_option'] === $i);
                    ?>
                    <div class="option-row">
                        <input type="radio" name="correct_option" value="<?php echo $i; ?>" <?php echo $is_correct ? 'checked' : ''; ?>>
                        <input type="text" name="options[]" class="option-text-input"
                               placeholder="Option <?php echo $i+1; ?>"
                               value="<?php echo htmlspecialchars($opt); ?>" required>
                        <button type="button" class="remove-option-btn" onclick="removeOption(this)">&#x2715;</button>
                    </div>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </div>
                <button type="button" id="add-option-btn" class="btn btn-outline btn-sm" onclick="addOptionRow()" style="margin-top:8px;">
                    + Add Option
                </button>
                <div class="form-hint">Mark the correct answer with the radio button on the left.</div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-accent"><?php echo $is_edit ? 'Save Changes' : 'Add Question'; ?></button>
                <a href="/ta_project/controllers/QuizController.php?action=questions&quiz_id=<?php echo $quiz['id']; ?>&course_id=<?php echo $course['id']; ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
// Initialize with at least 4 blank options if none pre-filled
document.addEventListener('DOMContentLoaded', function() {
    var existing = document.querySelectorAll('#options-list .option-row');
    var needed   = 4 - existing.length;
    for (var i = 0; i < needed; i++) {
        addOptionRow();
    }
    reindexOptions();
});
</script>
