# INSFP Project - Complete Functions Overview

## 📋 BACKEND (Laravel API)

### Authentication & Authorization
- **AuthController**
  - User login (with JWT tokens)
  - User registration
  - Password management
  - Role-based access control (Admin, Teacher, Student, Administrative Staff)

### User Management
- **User Model & Admin Operations**
  - User creation/update/deletion
  - Role assignment
  - Approval workflow (CheckApproved middleware)
  - User status management

### Administrative Features
- **AdminController**
  - Dashboard statistics
  - System-wide management
  - Approval of pending users
  - Configuration management

- **AdminDocumentController**
  - Document upload/storage
  - Document distribution to users
  - Document management

- **AdminMessageController**
  - Broadcast messages to students/teachers
  - System notifications
  - Announcement distribution

- **AdminDeliberationController**
  - Grade deliberations management
  - Final grades validation
  - Academic result processing

### Student Management
- **StudentController**
  - Student profile management
  - Student enrollment tracking
  - Student status updates
  - Student listing and filtering

- **StudentHomeworkController**
  - Homework submission tracking
  - Homework status monitoring
  - Grade tracking for homework

- **StudentDeliberationController**
  - Student final grades access
  - Academic transcript retrieval
  - Result history

### Teacher Management
- **TeacherController**
  - Teacher profile management
  - Teacher availability tracking
  - Teacher subject assignments

- **TeacherAttendanceController**
  - Attendance marking for classes
  - Attendance records management
  - Attendance reports

- **TeacherGradesController**
  - Grade entry for students
  - Grade modification
  - Grade reporting

- **TeacherHomeworkController**
  - Homework assignment creation
  - Homework submission review
  - Homework grading

- **TeacherLessonController**
  - Lesson content management
  - Lesson scheduling
  - Lesson updates

### Academic Management
- **ScheduleController**
  - Timetable creation and management
  - Class scheduling
  - Room assignment
  - Conflict prevention
  - Schedule distribution to users

- **SessionController**
  - Training session management
  - Session creation/update
  - Session status tracking
  - Multiple sessions handling

- **SpecialtyController**
  - Specialty/specialization management
  - Module assignment to specialties
  - Specialty course structure
  - Specialty details management

- **LessonController**
  - Lesson content management
  - Lesson duration tracking
  - Lesson assignments to teachers
  - Lesson scheduling

- **ExamController** (via models)
  - Exam scheduling
  - Exam type management
  - Exam date/time management
  - Exam room assignment

### Grades & Evaluation
- **Grade Model**
  - Student grades storage
  - Grade by module/course
  - Grade history tracking
  - Grade calculations

- **Deliberation Model**
  - Final grade deliberations
  - Academic status determination
  - Pass/Fail decisions
  - Grade validation workflow

### Communication
- **MessageController**
  - Internal messaging system
  - Message sending between users
  - Message history tracking
  - Message status management

- **AdminMessageController**
  - Broadcast messages
  - System announcements
  - Mass notifications

### Attendance & Homework
- **Attendance Model**
  - Student attendance records
  - Absence tracking
  - Attendance reports
  - Attendance statistics

- **HomeworkController & HomeworkSubmissionController**
  - Homework creation by teachers
  - Homework submission by students
  - Submission deadline tracking
  - Homework grading
  - Submission status management

### Documents & Files
- **DocumentController**
  - Document upload/download
  - Document management
  - File storage
  - Document access control

- **AdminDocumentController**
  - System document distribution
  - Official document management

### Additional Features
- **ChatbotController**
  - Gemini API integration
  - Automated Q&A support
  - Student assistance
  - FAQ automation

- **Notification Model**
  - Push notifications
  - In-app notifications
  - Grade update alerts
  - Schedule change alerts
  - Homework reminders

- **Holiday Model**
  - Holiday calendar management
  - Schedule exclusions
  - Day-off management

---

## 📱 MOBILE APP (Flutter)

### Authentication
- **login_screen.dart**
  - User login interface
  - Credential validation
  - Token storage
  - Session management

### Main Navigation
- **dashboard_screen.dart**
  - Dashboard overview
  - Quick stats
  - Recent activities
  - Navigation hub

### Student Features
- **schedule_screen.dart**
  - Personal timetable view
  - Class schedule
  - Room information
  - Time notifications

- **profile_screen.dart**
  - Student profile display
  - Profile editing
  - Contact information
  - Avatar management

- **courses_screen.dart**
  - Enrolled courses list
  - Course details
  - Course materials
  - Course status

- **attendance_screen.dart**
  - Attendance history
  - Absence records
  - Attendance statistics
  - Attendance percentage

- **exams_screen.dart**
  - Exam schedule
  - Exam results
  - Exam details
  - Exam location/time

- **homeworks_screen.dart & homework_submit_screen.dart**
  - Homework list
  - Homework details
  - Homework submission
  - File upload capability
  - Submission status

- **documents_screen.dart**
  - Document download
  - Official documents
  - Certificates
  - Academic records

- **deliberations_screen.dart**
  - Final grades display
  - Academic transcript
  - Deliberation results
  - Grade history

- **messages_screen.dart & message_detail_screen.dart**
  - Messaging interface
  - Message history
  - Conversation view
  - New message creation

### Services
- **auth_service.dart**
  - Authentication logic
  - Token management
  - User session handling
  - Login/logout operations

- **api_service.dart**
  - HTTP requests to backend
  - Request/response handling
  - Error management
  - API configuration

- **storage_service.dart**
  - Local data storage
  - Token persistence
  - User preferences
  - Offline data caching

### UI Components
- **stat_card.dart** - Dashboard statistics cards
- **loading_indicator.dart** - Loading states
- **badge_dot.dart** - Status indicators
- **nav_grid_item.dart** - Navigation grid items

### Theme & Configuration
- **theme.dart**
  - App color scheme
  - Typography
  - Component styling
  - Dark/Light mode support

- **api_config.dart**
  - API endpoint configuration
  - Base URL management
  - API version control
  - Windows/Android/iOS support

---

## 🌐 FRONTEND WEB (Vue.js + Tailwind CSS)

### Authentication
- **Login.vue** - User login page
- **Register.vue** - User registration page
- **Auth Endpoints** - API authentication

### Admin Dashboard
- **Dashboard.vue**
  - Dashboard overview
  - Statistical charts
  - Key metrics display
  - Activity monitoring

- **StatCard.vue** - Statistics display
- **DistributionChart.vue** - Data visualization
- **StudentBarChart.vue** - Student statistics
- **TeacherBarChart.vue** - Teacher statistics

### Student Management
- **Students.vue**
  - Complete student listing
  - Student filtering
  - Student search
  - Student actions

- **StudentDetails.vue**
  - Individual student profile
  - Enrollment history
  - Academic progress
  - Communication options

- **PendingStudentsTable.vue**
  - Pending approval students
  - Approval workflow
  - Student validation

- **ActiveStudentsTable.vue**
  - Currently enrolled students
  - Active status tracking

- **GraduatedStudentsTable.vue**
  - Graduated student records
  - Alumni tracking

- **StudentForm.vue**
  - Student registration form
  - Profile creation
  - Data validation

- **StudentFilters.vue**
  - Filter by specialty
  - Filter by session
  - Filter by status
  - Advanced search

- **MessageComposer.vue**
  - Send messages to students
  - Broadcast messages
  - Message templates

### Teacher Management
- **Teachers.vue**
  - Teacher listing
  - Teacher search
  - Teacher management

- **TeacherDetails.vue**
  - Individual teacher profile
  - Subject assignments
  - Schedule view
  - Performance metrics

- **TeachersTable.vue**
  - Formatted teacher list
  - Quick actions
  - Status display

- **MessageComposer.vue**
  - Message teachers
  - Broadcast to all teachers
  - Announcement sending

- **TeacherFilters.vue**
  - Filter by department
  - Filter by specialty
  - Advanced search

- **AssignTeacherModal.vue**
  - Assign teachers to modules
  - Subject assignment
  - Workload management

### Academic Management
- **Specialties.vue**
  - Specialties listing
  - Specialty management
  - Specialty creation

- **SpecialtyDetails.vue**
  - Specialty information
  - Module listing
  - Course structure

- **SpecialtyForm.vue**
  - Create/edit specialties
  - Specialty configuration

- **ModuleForm.vue**
  - Create/edit modules
  - Module details
  - Learning objectives

- **Sessions.vue**
  - Training sessions listing
  - Session management
  - Session status tracking

- **RegistrationGenerator.vue**
  - Generate registration numbers
  - Student registration automation
  - Batch processing

### Schedule Management
- **Schedule.vue**
  - Overall schedule view
  - Schedule creation
  - Schedule management

- **FullSessionTimetable.vue**
  - Complete session timetable
  - All classes view
  - Conflict visualization

- **SpecialtyTimetable.vue**
  - Specialty-specific schedule
  - Class timetables
  - Resource allocation

- **ScheduleModal.vue**
  - Schedule entry creation
  - Session selection
  - Class configuration

- **ScheduleEntryModal.vue**
  - Individual schedule entry
  - Time slot management
  - Room assignment

### Grades & Evaluation
- **ExamGrades.vue**
  - Exam grade management
  - Grade entry
  - Grade validation

- **Exams.vue**
  - Exam listing
  - Exam scheduling
  - Exam creation

- **Deliberations.vue**
  - Grade deliberations
  - Final grade validation
  - Academic decision making

### File & Document Management
- **Files.vue**
  - Document management
  - File upload
  - File distribution
  - Archive management

### Admin Profile
- **Profile.vue**
  - Admin profile management
  - Account settings
  - Personal information
  - Password management

### Layout & Navigation
- **DashboardLayout.vue** - Main layout container
- **Sidebar.vue** - Navigation sidebar
- **Topbar.vue** - Top navigation bar
- **Card.vue** - Reusable card component
- **LoadingSpinner.vue** - Loading state
- **ThemeToggle.vue** - Light/Dark mode toggle

### Student Portal
- **Dashboard.vue** - Student home
- **Courses.vue** - Course listing
- **Schedule.vue** - Personal timetable
- **Attendance.vue** - Attendance view
- **Exams.vue** - Exam results
- **Messages.vue** - Student messaging
- **Documents.vue** - Document download
- **Homeworks.vue** - Homework listing
- **Deliberations.vue** - Final grades
- **CompleteProfile.vue** - Profile completion
- **Profile.vue** - Student profile

### Teacher Portal
- **Dashboard.vue** - Teacher home
- **Modules.vue** - Module listing
- **ModuleStudents.vue** - Students per module
- **Grading.vue** - Grade entry interface
- **MarkAttendance.vue** - Attendance marking
- **Attendance.vue** - Attendance view
- **Documents.vue** - Document management
- **Exams.vue** - Exam management
- **Messages.vue** - Teacher messaging
- **Homeworks.vue** - Homework management
- **HomeworkDetail.vue** - Individual homework
- **Courses.vue** - Course management
- **Schedule.vue** - Personal schedule
- **Profile.vue** - Teacher profile

### State Management (Pinia Stores)
- **auth.js** - Authentication state
- **adminDashboard.js** - Admin dashboard data
- **sessions.js** - Training sessions
- **specialties.js** - Specialty data
- **students.js** - Student data
- **teachers.js** - Teacher data
- **theme.js** - Theme preferences

### API Endpoints
- **auth.js** - Authentication endpoints
- **modules.js** - Module management
- **schedules.js** - Schedule endpoints
- **sessions.js** - Session endpoints
- **teacher.js** - Teacher specific endpoints
- **homework.js** - Homework endpoints
- **admin.js** - Admin endpoints
- **student.js** - Student endpoints
- **teacherPortal.js** - Teacher portal endpoints

### Utilities
- **axios.js** - HTTP client configuration
- **useTheme.js** - Theme composable

### Routing
- **router/index.js** - Route configuration

---

## 🎯 Summary of Core Modules

| Module | Backend | Mobile | Frontend |
|--------|---------|--------|----------|
| **Authentication** | ✅ | ✅ | ✅ |
| **User Management** | ✅ | ✅ | ✅ |
| **Schedules** | ✅ | ✅ | ✅ |
| **Grades** | ✅ | ✅ | ✅ |
| **Attendance** | ✅ | ✅ | ✅ |
| **Messages** | ✅ | ✅ | ✅ |
| **Documents** | ✅ | ✅ | ✅ |
| **Homeworks** | ✅ | ✅ | ✅ |
| **Exams** | ✅ | ✅ | ✅ |
| **Deliberations** | ✅ | ✅ | ✅ |
| **Chatbot** | ✅ | ❌ | ❌ |
| **Notifications** | ✅ | ✅ | ❌ |

---

## 🔧 Technical Implementation

### Backend APIs Available
- RESTful endpoints for all modules
- JWT authentication
- Role-based middleware
- Request validation
- Error handling
- Database models with relationships

### Mobile Implementation
- Cross-platform Flutter
- Native Android/iOS build
- Windows support
- Local storage management
- API integration
- Offline-capable screens

### Frontend Implementation
- Single Page Application (Vue 3)
- Tailwind CSS styling
- Responsive design
- Dark/Light theme
- Pinia state management
- Vue Router navigation
- Axios HTTP client
