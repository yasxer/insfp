class ApiConfig {
  // Change this to your server IP when testing on a physical device
  // Android Emulator → http://10.0.2.2:8000
  // Physical device  → http://<your-local-ip>:8000
  static const String baseUrl = 'http://10.0.2.2:8000';

  // Auth
  static const String login = '/api/login';
  static const String logout = '/api/logout';
  static const String me = '/api/me';

  // Student endpoints
  static const String dashboard = '/api/student/dashboard';
  static const String profile = '/api/student/profile';
  static const String profilePassword = '/api/student/profile/password';
  static const String modules = '/api/student/modules';
  static const String grades = '/api/student/grades';
  static const String attendance = '/api/student/attendance';
  static const String schedule = '/api/student/schedule';
  static const String examResults = '/api/student/exams/results';
  static const String upcomingExams = '/api/student/exams/upcoming';
  static const String messages = '/api/student/messages';
  static const String unreadCount = '/api/student/messages/unread/count';
  static const String lessonModules = '/api/student/lessons/modules';
  static const String documents = '/api/student/documents';
  static const String newDocumentsCount = '/api/student/documents/new/count';
  static const String newLessonsCount = '/api/student/lessons/new/count';
  static const String homeworks = '/api/student/homeworks';
  static const String deliberations = '/api/student/deliberations';
  static const String completeProfile = '/api/student/complete-profile';

  // Dynamic endpoints
  static String messageDetail(int id) => '/api/student/messages/$id';
  static String moduleLessons(int moduleId) =>
      '/api/student/lessons/modules/$moduleId';
  static String downloadLesson(int id) =>
      '/api/student/lessons/$id/download';
  static String downloadDocument(int id) =>
      '/api/student/documents/$id/download';
  static String submitHomework(int id) =>
      '/api/student/homeworks/$id/submit';
}
