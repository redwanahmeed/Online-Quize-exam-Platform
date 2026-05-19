# Online Quiz & Exam Platform — Teaching Assistant Portal
## Project: Online_Quiz_&_Exam_platform | Role 3: Teaching Assistant

---

## Setup Instructions

### 1. Requirements
- PHP 7.4+ (with mysqli extension)
- MySQL 5.7+
- Apache/Nginx with mod_rewrite
- XAMPP / WAMP / Laragon (recommended for local dev)

### 2. Database Setup
1. Open phpMyAdmin or MySQL CLI
2. Run the schema SQL (provided separately as the main database file)
3. Then run the sample data seeder:
   ```sql
   SOURCE /path/to/ta_project/seed_data.sql;
   ```

### 3. Configure Database Connection
Edit `config/db.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // your MySQL username
define('DB_PASS', '');           // your MySQL password
define('DB_NAME', 'online_quiz_platform');
```

### 4. Place Project
Put the `ta_project/` folder inside your web root:
- XAMPP: `C:/xampp/htdocs/ta_project/`
- WAMP:  `C:/wamp64/www/ta_project/`

### 5. Login Credentials (after seeding)
| Role | Email | Password |
|------|-------|----------|
| Teaching Assistant | ta@university.edu | password |
| Instructor | instructor@university.edu | password |
| Student | student1@university.edu | password |

### 6. Access
Open: `http://localhost/ta_project/`

---

## Project Structure (MVC)

```
ta_project/
├── config/
│   ├── db.php            — Database connection
│   └── auth.php          — Session helpers, role guard
├── models/
│   ├── UserModel.php     — User queries
│   ├── CourseModel.php   — Course / enrollment queries
│   ├── QuizModel.php     — Quiz, question, option queries
│   ├── AttemptModel.php  — Results, at-risk queries
│   ├── AnnouncementModel.php
│   ├── MaterialModel.php
│   ├── QAModel.php
│   └── DoubtSessionModel.php
├── views/
│   ├── auth/login.php
│   ├── layouts/header.php, footer.php
│   └── ta/
│       ├── dashboard.php
│       ├── courses.php
│       ├── course_detail.php
│       ├── quiz_form.php
│       ├── quiz_questions.php
│       ├── question_form.php
│       ├── results.php
│       ├── at_risk.php          — AJAX-powered threshold filter
│       ├── announcements.php
│       ├── materials.php
│       ├── material_form.php
│       ├── qa_list.php
│       ├── qa_thread.php        — AJAX endorse/unendorse
│       ├── doubt_sessions.php
│       ├── doubt_session_form.php
│       ├── doubt_session_bookings.php
│       └── profile.php
├── controllers/
│   ├── AuthController.php
│   ├── DashboardController.php
│   ├── ProfileController.php
│   ├── CourseController.php
│   ├── QuizController.php
│   ├── ResultsController.php
│   ├── AnnouncementController.php
│   ├── MaterialController.php
│   ├── QAController.php
│   └── DoubtSessionController.php
├── ajax/
│   └── api.php           — AJAX JSON endpoint
├── public/
│   ├── css/style.css
│   └── js/main.js
├── index.php             — Entry point
└── seed_data.sql         — Sample data
```

---

## Features Implemented

| Feature | Status |
|---------|--------|
| Login with role-based access (TA only) | ✅ |
| Remember Me cookie (30 days) | ✅ |
| View profile & change password | ✅ |
| View assigned courses | ✅ |
| View course detail: students, quizzes, summary | ✅ |
| Create practice quizzes (pending instructor approval) | ✅ |
| Edit / delete practice quizzes | ✅ |
| Add / edit / delete questions with options | ✅ |
| View all student attempt results | ✅ |
| At-risk student filter (AJAX + threshold slider) | ✅ |
| Post announcements marked "From TA" | ✅ |
| Upload / edit / delete study materials | ✅ |
| Q&A board: list, thread view, post answers | ✅ |
| Endorse / unendorse answers (AJAX) | ✅ |
| Mark Q&A as resolved / unresolved | ✅ |
| Schedule doubt sessions | ✅ |
| Reschedule (edit) doubt sessions | ✅ |
| Cancel doubt sessions | ✅ |
| View booked students per session | ✅ |
| Capacity progress bar | ✅ |
| Course summary report | ✅ |
| PHP prepared statements (no raw SQL injection) | ✅ |
| Server-side validation on all inputs | ✅ |
| Client-side JS validation on all forms | ✅ |
| AJAX endpoint returning JSON | ✅ |
| MVC separation | ✅ |
| Session-based auth with role check on every page | ✅ |
| Clean responsive CSS (no media queries) | ✅ |

---

## Security
- All DB queries use `mysqli` prepared statements with `bind_param`
- All output is escaped with `htmlspecialchars()`
- All input is sanitized server-side before DB writes
- Session role checked on every protected page
- AJAX endpoint checks session before responding
- Passwords stored with `password_hash()` / verified with `password_verify()`
