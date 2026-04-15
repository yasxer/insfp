import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';

import 'config/theme.dart';
import 'services/storage_service.dart';
import 'services/api_service.dart';
import 'services/auth_service.dart';

import 'screens/login_screen.dart';
import 'screens/dashboard_screen.dart';
import 'screens/messages/messages_screen.dart';
import 'screens/messages/message_detail_screen.dart';
import 'screens/courses_screen.dart';
import 'screens/documents_screen.dart';
import 'screens/deliberations_screen.dart';
import 'screens/homeworks/homeworks_screen.dart';
import 'screens/homeworks/homework_submit_screen.dart';
import 'screens/schedule_screen.dart';
import 'screens/attendance_screen.dart';
import 'screens/exams_screen.dart';
import 'screens/profile_screen.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Lock orientation to portrait
  await SystemChrome.setPreferredOrientations([
    DeviceOrientation.portraitUp,
    DeviceOrientation.portraitDown,
  ]);

  // Set system UI overlay style
  SystemChrome.setSystemUIOverlayStyle(const SystemUiOverlayStyle(
    statusBarColor: Colors.transparent,
    statusBarIconBrightness: Brightness.dark,
    systemNavigationBarColor: AppColors.white,
    systemNavigationBarIconBrightness: Brightness.dark,
  ));

  // Initialize storage
  final storage = StorageService();
  await storage.init();

  // Initialize API service
  final api = ApiService(storage);

  // Initialize auth service
  final auth = AuthService(api, storage);

  runApp(
    MultiProvider(
      providers: [
        Provider<StorageService>.value(value: storage),
        Provider<ApiService>.value(value: api),
        ChangeNotifierProvider<AuthService>.value(value: auth),
      ],
      child: const InsfpApp(),
    ),
  );
}

class InsfpApp extends StatelessWidget {
  const InsfpApp({super.key});

  @override
  Widget build(BuildContext context) {
    return Consumer<AuthService>(
      builder: (context, auth, _) {
        return MaterialApp(
          title: 'INSFP Student',
          debugShowCheckedModeBanner: false,
          theme: AppTheme.lightTheme,
          initialRoute: auth.isLoggedIn ? '/dashboard' : '/login',
          routes: {
            '/login': (context) => const LoginScreen(),
            '/dashboard': (context) => const DashboardScreen(),
            '/messages': (context) => const MessagesScreen(),
            '/message-detail': (context) => const MessageDetailScreen(),
            '/courses': (context) => const CoursesScreen(),
            '/documents': (context) => const DocumentsScreen(),
            '/deliberations': (context) => const DeliberationsScreen(),
            '/homeworks': (context) => const HomeworksScreen(),
            '/homework-submit': (context) => const HomeworkSubmitScreen(),
            '/schedule': (context) => const ScheduleScreen(),
            '/attendance': (context) => const AttendanceScreen(),
            '/exams': (context) => const ExamsScreen(),
            '/profile': (context) => const ProfileScreen(),
          },
        );
      },
    );
  }
}
