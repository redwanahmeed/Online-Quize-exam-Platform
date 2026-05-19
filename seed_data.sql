-- ============================================================
-- SAMPLE DATA SEEDER for Online Quiz & Exam Platform TA Portal
-- Run this after creating the database schema.
-- TA Login: ta@university.edu / password: ta123456
-- ============================================================

USE online_quiz_platform;

-- Users
INSERT INTO users (name, email, password_hash, phone, role, program, is_active) VALUES
('Dr. Sarah Ahmed',   'instructor@university.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJCmCmCmi', '+8801711000001', 'instructor', 'Computer Science', 1),
('Rahim Hossain',     'ta@university.edu',         '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJCmCmCmi', '+8801711000002', 'ta',         'Computer Science', 1),
('Mitu Khan',         'student1@university.edu',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJCmCmCmi', '+8801711000003', 'student',    'CSE',              1),
('Karim Uddin',       'student2@university.edu',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJCmCmCmi', '+8801711000004', 'student',    'CSE',              1),
('Nasrin Akter',      'student3@university.edu',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uJCmCmCmi', '+8801711000005', 'student',    'EEE',              1);

-- Note: password hash above = 'password' using password_hash('password', PASSWORD_DEFAULT)
-- Use this for TA login: email=ta@university.edu, password=password

-- Subjects
INSERT INTO subjects (name, description) VALUES
('Data Structures & Algorithms', 'Core CS subject covering arrays, trees, sorting, graphs'),
('Database Systems',             'SQL, relational algebra, normalization, transactions');

-- Courses
INSERT INTO courses (instructor_id, subject_id, title, description, enrollment_type, max_students, status) VALUES
(1, 1, 'CSE 301 - Data Structures', 'Fundamental data structures for CS students', 'open', 50, 'active'),
(1, 2, 'CSE 402 - Database Systems', 'Introduction to relational databases and SQL', 'open', 40, 'active');

-- Assign TA to courses
INSERT INTO course_tas (course_id, ta_id) VALUES (1, 2), (2, 2);

-- Enroll students
INSERT INTO enrollments (student_id, course_id, status) VALUES
(3, 1, 'active'), (4, 1, 'active'), (5, 1, 'active'),
(3, 2, 'active'), (4, 2, 'active');

-- Update student_id field
UPDATE users SET student_id = '2021-CS-001' WHERE id = 3;
UPDATE users SET student_id = '2021-CS-002' WHERE id = 4;
UPDATE users SET student_id = '2021-EEE-001' WHERE id = 5;

-- Quizzes
INSERT INTO quizzes (course_id, created_by, title, description, time_limit_minutes, total_marks, pass_mark, quiz_type, status, available_from, available_until) VALUES
(1, 2, 'Week 2 Practice — Arrays & Linked Lists', 'Practice quiz on arrays and linked list basics', 30, 10, 6, 'practice', 'draft', NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY)),
(1, 1, 'Midterm Quiz — Sorting Algorithms',        'Covers bubble, merge, and quick sort',          60, 20, 12, 'graded',   'published', NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY));

-- Questions for quiz 1
INSERT INTO questions (quiz_id, question_text, marks, order_index, created_by) VALUES
(1, 'What is the time complexity of accessing an element in an array by index?', 1, 1, 2),
(1, 'Which data structure follows the LIFO (Last In First Out) principle?',      1, 2, 2);

-- Options for Q1
INSERT INTO options (question_id, option_text, is_correct) VALUES
(1, 'O(n)',    0),
(1, 'O(1)',    1),
(1, 'O(log n)',0),
(1, 'O(n²)',   0);

-- Options for Q2
INSERT INTO options (question_id, option_text, is_correct) VALUES
(2, 'Queue',   0),
(2, 'Stack',   1),
(2, 'Tree',    0),
(2, 'Heap',    0);

-- Attempts (for results/at-risk demo)
INSERT INTO attempts (quiz_id, student_id, score, started_at, completed_at, is_graded) VALUES
(2, 3, 18.0, DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), 1),
(2, 4,  7.5, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), 1),
(2, 5,  9.0, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 1);

-- Answers for attempt 1
INSERT INTO answers (attempt_id, question_id, selected_option_id) VALUES (1, 1, 2), (1, 2, 6);

-- Announcements
INSERT INTO announcements (course_id, author_id, title, body) VALUES
(1, 1, 'Welcome to CSE 301!', 'Hello everyone, welcome to Data Structures. Please complete the pre-reading before our first session.'),
(1, 2, '[From TA] Practice Quiz Available', 'Hi all, I have added a practice quiz on Arrays and Linked Lists. Please attempt it before the tutorial session this Friday.');

-- Q&A Questions
INSERT INTO qa_questions (course_id, student_id, title, body, is_resolved) VALUES
(1, 3, 'Difference between ArrayList and LinkedList?', 'Can someone explain when to use ArrayList vs LinkedList in real applications? I am confused about performance.', 0),
(1, 4, 'How does merge sort divide the array?',        'In the lecture, the professor mentioned divide and conquer. How exactly does merge sort split the array at each step?', 1);

-- Q&A Answers
INSERT INTO qa_answers (qa_question_id, author_id, body, is_endorsed) VALUES
(1, 2, 'Great question! ArrayList is backed by a dynamic array, so random access is O(1) but insertion/deletion in the middle is O(n). LinkedList is better for frequent insertions/deletions at arbitrary positions.', 1),
(2, 2, 'Merge sort always splits the array into two equal halves (left and right) recursively until each sub-array has one element, then merges them back in sorted order.', 1);

-- Course Materials
INSERT INTO course_materials (course_id, uploaded_by, title, file_path, material_type) VALUES
(1, 2, 'Arrays Cheat Sheet (Week 2)',     '/uploads/arrays_cheatsheet.pdf', 'document'),
(1, 2, 'Sorting Visualizer Tool',          'https://visualgo.net/en/sorting',  'link'),
(2, 2, 'SQL JOIN Reference Card',          '/uploads/sql_joins.pdf',           'document');

-- Doubt Sessions
INSERT INTO doubt_sessions (course_id, ta_id, title, scheduled_at, duration_minutes, location_or_link, max_attendees) VALUES
(1, 2, 'Arrays & Pointers Doubt Session',   DATE_ADD(NOW(), INTERVAL 3 DAY),  60, 'Room 204, CS Building',           15),
(1, 2, 'Sorting Algorithms Pre-Exam Help',  DATE_ADD(NOW(), INTERVAL 7 DAY),  90, 'https://meet.google.com/demo-abc', 20),
(2, 2, 'SQL Queries Practice Session',      DATE_ADD(NOW(), INTERVAL 5 DAY),  60, 'Room 305, CS Building',           12);

-- Bookings
INSERT INTO doubt_session_bookings (doubt_session_id, student_id) VALUES
(1, 3), (1, 4), (1, 5),
(2, 3), (2, 4),
(3, 3);
