

function validateLoginForm() {
    var email = document.getElementById('email');
    var password = document.getElementById('password');
    var errors = [];

    clearValidation();

    if (!email.value.trim()) {
        errors.push({ field: email, msg: 'Email is required.' });
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
        errors.push({ field: email, msg: 'Enter a valid email address.' });
    }

    if (!password.value) {
        errors.push({ field: password, msg: 'Password is required.' });
    }

    if (errors.length > 0) {
        showErrors(errors);
        return false;
    }
    return true;
}

function validateQuizForm() {
    var title      = document.getElementById('title');
    var time_limit = document.getElementById('time_limit_minutes');
    var total_marks= document.getElementById('total_marks');
    var pass_mark  = document.getElementById('pass_mark');
    var errors = [];

    clearValidation();

    if (!title || !title.value.trim()) {
        errors.push({ field: title, msg: 'Quiz title is required.' });
    } else if (title.value.length > 150) {
        errors.push({ field: title, msg: 'Title must be under 150 characters.' });
    }

    if (!time_limit || parseInt(time_limit.value) < 1) {
        errors.push({ field: time_limit, msg: 'Time limit must be at least 1 minute.' });
    }

    if (!total_marks || parseFloat(total_marks.value) <= 0) {
        errors.push({ field: total_marks, msg: 'Total marks must be a positive number.' });
    }

    if (pass_mark && parseFloat(pass_mark.value) < 0) {
        errors.push({ field: pass_mark, msg: 'Pass mark cannot be negative.' });
    }

    if (errors.length > 0) {
        showErrors(errors);
        return false;
    }
    return true;
}

function validateQuestionForm() {
    var qtext   = document.getElementById('question_text');
    var marks   = document.getElementById('marks');
    var errors  = [];

    clearValidation();

    if (!qtext || !qtext.value.trim()) {
        errors.push({ field: qtext, msg: 'Question text is required.' });
    }

    if (!marks || parseFloat(marks.value) <= 0) {
        errors.push({ field: marks, msg: 'Marks must be greater than 0.' });
    }

    
    var optionInputs = document.querySelectorAll('.option-text-input');
    var filled = 0;
    optionInputs.forEach(function(inp) { if (inp.value.trim()) filled++; });
    if (filled < 2) {
        errors.push({ field: null, msg: 'At least 2 options are required.' });
    }

    
    var radios = document.querySelectorAll('input[name="correct_option"]');
    var selected = false;
    radios.forEach(function(r) { if (r.checked) selected = true; });
    if (!selected) {
        errors.push({ field: null, msg: 'Please select the correct answer.' });
    }

    if (errors.length > 0) {
        showErrors(errors);
        return false;
    }
    return true;
}

function validateAnnouncementForm() {
    var title = document.getElementById('ann_title');
    var body  = document.getElementById('ann_body');
    var errors = [];

    clearValidation();

    if (!title || !title.value.trim()) {
        errors.push({ field: title, msg: 'Title is required.' });
    }
    if (!body || !body.value.trim()) {
        errors.push({ field: body, msg: 'Body is required.' });
    }

    if (errors.length > 0) {
        showErrors(errors);
        return false;
    }
    return true;
}

function validateProfileForm() {
    var name = document.getElementById('name');
    var errors = [];
    clearValidation();
    if (!name || !name.value.trim()) {
        errors.push({ field: name, msg: 'Name is required.' });
    }
    if (errors.length > 0) {
        showErrors(errors);
        return false;
    }
    return true;
}

function validatePasswordForm() {
    var curr   = document.getElementById('current_password');
    var newp   = document.getElementById('new_password');
    var conf   = document.getElementById('confirm_password');
    var errors = [];
    clearValidation();

    if (!curr.value) errors.push({ field: curr, msg: 'Current password is required.' });
    if (!newp.value) errors.push({ field: newp, msg: 'New password is required.' });
    else if (newp.value.length < 6) errors.push({ field: newp, msg: 'Password must be at least 6 characters.' });
    if (newp.value && conf.value !== newp.value) errors.push({ field: conf, msg: 'Passwords do not match.' });

    if (errors.length > 0) { showErrors(errors); return false; }
    return true;
}

function validateMaterialForm() {
    var title = document.getElementById('mat_title');
    var path  = document.getElementById('file_path');
    var errors = [];
    clearValidation();
    if (!title || !title.value.trim()) errors.push({ field: title, msg: 'Title is required.' });
    if (!path || !path.value.trim())   errors.push({ field: path, msg: 'File path or link is required.' });
    if (errors.length > 0) { showErrors(errors); return false; }
    return true;
}

function validateSessionForm() {
    var title  = document.getElementById('session_title');
    var sched  = document.getElementById('scheduled_at');
    var dur    = document.getElementById('duration_minutes');
    var maxatt = document.getElementById('max_attendees');
    var errors = [];
    clearValidation();
    if (!title || !title.value.trim()) errors.push({ field: title, msg: 'Title is required.' });
    if (!sched || !sched.value)        errors.push({ field: sched, msg: 'Date and time is required.' });
    if (!dur || parseInt(dur.value) < 1) errors.push({ field: dur, msg: 'Duration must be at least 1 minute.' });
    if (!maxatt || parseInt(maxatt.value) < 1) errors.push({ field: maxatt, msg: 'Max attendees must be at least 1.' });
    if (errors.length > 0) { showErrors(errors); return false; }
    return true;
}


function showErrors(errors) {
    var globalDiv = document.getElementById('js-errors');
    if (!globalDiv) {
        globalDiv = document.createElement('div');
        globalDiv.id = 'js-errors';
        globalDiv.className = 'alert alert-error';
        var first = document.querySelector('.card-body') || document.querySelector('form');
        if (first) first.insertBefore(globalDiv, first.firstChild);
    }
    var html = '<strong>Please fix the following:</strong><ul style="margin:.5em 0 0 1.2em">';
    errors.forEach(function(e) {
        html += '<li>' + e.msg + '</li>';
        if (e.field) {
            e.field.style.borderColor = '#c0392b';
        }
    });
    html += '</ul>';
    globalDiv.innerHTML = html;
    globalDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function clearValidation() {
    var old = document.getElementById('js-errors');
    if (old) old.remove();
    document.querySelectorAll('input, textarea, select').forEach(function(el) {
        el.style.borderColor = '';
    });
}


var optionCount = 0;

function addOptionRow(text, isCorrect, index) {
    var list = document.getElementById('options-list');
    if (!list) return;

    optionCount++;
    var idx = (index !== undefined) ? index : (optionCount - 1);
    var row = document.createElement('div');
    row.className = 'option-row';
    row.innerHTML =
        '<input type="radio" name="correct_option" value="' + idx + '"' + (isCorrect ? ' checked' : '') + '>' +
        '<input type="text" name="options[]" class="option-text-input" placeholder="Option ' + optionCount + '" value="' + (text || '') + '" required>' +
        '<button type="button" class="remove-option-btn" onclick="removeOption(this)">&#x2715;</button>';
    list.appendChild(row);
    reindexOptions();
}

function removeOption(btn) {
    var rows = document.querySelectorAll('#options-list .option-row');
    if (rows.length <= 2) { alert('Minimum 2 options required.'); return; }
    btn.parentElement.remove();
    reindexOptions();
}

function reindexOptions() {
    var rows = document.querySelectorAll('#options-list .option-row');
    rows.forEach(function(row, i) {
        var radio = row.querySelector('input[type=radio]');
        var input = row.querySelector('input[type=text]');
        if (radio) radio.value = i;
        if (input) input.placeholder = 'Option ' + (i + 1);
    });
}


function loadAtRisk() {
    var slider    = document.getElementById('threshold-slider');
    var display   = document.getElementById('threshold-value');
    var container = document.getElementById('at-risk-results');
    var courseId  = document.getElementById('course-id-input');

    if (!slider || !container || !courseId) return;

    var threshold = slider.value;
    if (display) display.textContent = threshold + '%';

    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/ta_project/ajax/api.php?action=get_at_risk&course_id=' + courseId.value + '&threshold=' + threshold, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        renderAtRisk(data.students, data.threshold);
                    } else {
                        container.innerHTML = '<div class="alert alert-error">' + data.message + '</div>';
                    }
                } catch(e) {
                    container.innerHTML = '<div class="alert alert-error">Failed to load data.</div>';
                }
            }
        }
    };
    xhr.send();
}

function renderAtRisk(students, threshold) {
    var container = document.getElementById('at-risk-results');
    if (!container) return;

    if (students.length === 0) {
        container.innerHTML = '<div class="empty-state"><div class="empty-icon">✅</div><p>No at-risk students found below ' + threshold + '% threshold.</p></div>';
        return;
    }

    var html = '<div class="table-wrap"><table><thead><tr>' +
        '<th>Student</th><th>Email</th><th>Program</th><th>Attempts</th><th>Avg Score</th><th>Min Score</th><th>Status</th>' +
        '</tr></thead><tbody>';

    students.forEach(function(s) {
        var avg = parseFloat(s.avg_score).toFixed(1);
        var riskClass = avg < 30 ? 'risk-high' : avg < 50 ? 'risk-med' : 'risk-low';
        html += '<tr>' +
            '<td><strong>' + escHtml(s.name) + '</strong><br><span class="text-muted text-small">' + escHtml(s.student_no || '') + '</span></td>' +
            '<td class="text-small">' + escHtml(s.email) + '</td>' +
            '<td class="text-small">' + escHtml(s.program || '—') + '</td>' +
            '<td>' + s.attempt_count + '</td>' +
            '<td class="' + riskClass + '">' + avg + '%</td>' +
            '<td class="text-small">' + parseFloat(s.min_score).toFixed(1) + '%</td>' +
            '<td><span class="badge badge-red">⚑ At Risk</span></td>' +
            '</tr>';
    });

    html += '</tbody></table></div>';
    html += '<p class="text-small text-muted mt-8">' + students.length + ' student(s) flagged below ' + threshold + '% threshold.</p>';
    container.innerHTML = html;
}

function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ---- AJAX: ENDORSE TOGGLE ----
function toggleEndorse(answerId, currentlyEndorsed) {
    var newEndorse = currentlyEndorsed ? 0 : 1;
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/ta_project/ajax/api.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                var data = JSON.parse(xhr.responseText);
                if (data.success) {
                    var btn   = document.querySelector('[data-answer-id="' + answerId + '"]');
                    var block = document.getElementById('answer-block-' + answerId);
                    if (data.endorsed) {
                        if (btn) { btn.textContent = '★ Endorsed'; btn.classList.add('active'); btn.setAttribute('data-endorsed','1'); }
                        if (block) block.classList.add('endorsed');
                    } else {
                        if (btn) { btn.textContent = '☆ Endorse'; btn.classList.remove('active'); btn.setAttribute('data-endorsed','0'); }
                        if (block) block.classList.remove('endorsed');
                    }
                }
            } catch(e) {}
        }
    };
    xhr.send('action=toggle_endorse&answer_id=' + answerId + '&endorse=' + newEndorse);
}


document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5s
    var alerts = document.querySelectorAll('.alert-success');
    alerts.forEach(function(a) {
        setTimeout(function() {
            a.style.transition = 'opacity .5s';
            a.style.opacity = '0';
            setTimeout(function() { a.remove(); }, 500);
        }, 5000);
    });

    
    var slider = document.getElementById('threshold-slider');
    if (slider) {
        loadAtRisk();
        slider.addEventListener('input', loadAtRisk);
    }
});
