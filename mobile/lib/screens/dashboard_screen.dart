import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../config/theme.dart';
import '../config/api_config.dart';
import '../services/auth_service.dart';
import '../services/api_service.dart';

import '../widgets/nav_grid_item.dart';
import '../widgets/loading_indicator.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  bool _isLoading = true;
  String? _error;


  int _unreadMessages = 0;
  int _newLessons = 0;
  int _newDocuments = 0;

  @override
  void initState() {
    super.initState();
    _loadDashboard();
  }

  Future<void> _loadDashboard() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final api = context.read<ApiService>();

      // Load dashboard data and badge counts in parallel
      final results = await Future.wait([
        api.get(ApiConfig.dashboard),
        api.get(ApiConfig.unreadCount).catchError((_) => {'count': 0}),
        api.get(ApiConfig.newLessonsCount).catchError((_) => {'count': 0}),
        api.get(ApiConfig.newDocumentsCount).catchError((_) => {'count': 0}),
      ]);

      final dashData = results[0];
      final stats = dashData['statistics'] ?? {};

      setState(() {

        _unreadMessages = (results[1] as Map)['count'] ?? 0;
        _newLessons = (results[2] as Map)['count'] ?? 0;
        _newDocuments = (results[3] as Map)['count'] ?? 0;
      });

      // Update auth user data if available
      if (dashData['student'] != null && mounted) {
        context.read<AuthService>().updateUser(dashData['student']);
      }
    } catch (e) {
      setState(() {
        _error = e.toString();
      });
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthService>();

    return Scaffold(
      backgroundColor: AppColors.scaffoldBg,
      body: _isLoading
          ? const LoadingIndicator(message: 'Loading dashboard...')
          : RefreshIndicator(
              onRefresh: _loadDashboard,
              color: AppColors.primary,
              child: CustomScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                slivers: [
                  // Beautiful Custom Header
                  SliverAppBar(
                    expandedHeight: 190.0,
                    floating: false,
                    pinned: true,
                    backgroundColor: AppColors.primary,
                    elevation: 0,
                    flexibleSpace: FlexibleSpaceBar(
                      background: Container(
                        decoration: const BoxDecoration(
                          gradient: LinearGradient(
                            colors: [AppColors.primary, AppColors.purple],
                            begin: Alignment.topLeft,
                            end: Alignment.bottomRight,
                          ),
                        ),
                        child: SafeArea(
                          child: Padding(
                            padding: const EdgeInsets.fromLTRB(20, 20, 20, 36),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            'INSFP Portal',
                                            style: AppTextStyles.labelSmall.copyWith(
                                              color: AppColors.white.withValues(alpha: 0.8),
                                              letterSpacing: 1.5,
                                            ),
                                          ),
                                          const SizedBox(height: 4),
                                          Text(
                                            'Hello, ${auth.userName.split(' ').first}',
                                            style: AppTextStyles.headlineLarge.copyWith(
                                              color: AppColors.white,
                                              fontWeight: FontWeight.w700,
                                            ),
                                            maxLines: 1,
                                            overflow: TextOverflow.ellipsis,
                                          ),
                                        ],
                                      ),
                                    ),
                                    const SizedBox(width: 16),
                                    Container(
                                      decoration: BoxDecoration(
                                        color: AppColors.white.withValues(alpha: 0.2),
                                        shape: BoxShape.circle,
                                      ),
                                      child: IconButton(
                                        icon: Stack(
                                          children: [
                                            const Icon(Icons.notifications_none_rounded, color: AppColors.white),
                                            if (_unreadMessages > 0)
                                              Positioned(
                                                right: 2,
                                                top: 2,
                                                child: Container(
                                                  width: 8,
                                                  height: 8,
                                                  decoration: const BoxDecoration(
                                                    color: AppColors.red,
                                                    shape: BoxShape.circle,
                                                  ),
                                                ),
                                              ),
                                          ],
                                        ),
                                        onPressed: () => Navigator.pushNamed(context, '/messages'),
                                      ),
                                    ),
                                  ],
                                ),
                                const Spacer(),
                                if (auth.specialty != null)
                                  Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                                    decoration: BoxDecoration(
                                      color: AppColors.white.withValues(alpha: 0.15),
                                      borderRadius: BorderRadius.circular(20),
                                    ),
                                    child: Row(
                                      mainAxisSize: MainAxisSize.min,
                                      children: [
                                        const Icon(Icons.school_rounded, size: 16, color: AppColors.white),
                                        const SizedBox(width: 8),
                                        Flexible(
                                          child: Text(
                                            '${auth.specialty!['name']} - S${auth.currentSemester ?? ''}',
                                            style: AppTextStyles.labelLarge.copyWith(color: AppColors.white),
                                            overflow: TextOverflow.ellipsis,
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                              ],
                            ),
                          ),
                        ),
                      ),
                    ),
                    bottom: PreferredSize(
                      preferredSize: const Size.fromHeight(24),
                      child: Container(
                        height: 24,
                        decoration: const BoxDecoration(
                          color: AppColors.scaffoldBg,
                          borderRadius: BorderRadius.only(
                            topLeft: Radius.circular(24),
                            topRight: Radius.circular(24),
                          ),
                        ),
                      ),
                    ),
                  ),

                  // Body Content
                  SliverToBoxAdapter(
                    child: Padding(
                      padding: const EdgeInsets.all(20),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Error section
                          if (_error != null)
                            Container(
                              margin: const EdgeInsets.only(bottom: 24),
                              padding: const EdgeInsets.all(12),
                              decoration: BoxDecoration(
                                color: AppColors.redTint,
                                borderRadius: BorderRadius.circular(12),
                              ),
                              child: Row(
                                children: [
                                  const Icon(Icons.error_outline, color: AppColors.red, size: 20),
                                  const SizedBox(width: 8),
                                  Expanded(
                                    child: Text(
                                      _error!,
                                      style: AppTextStyles.bodyMedium.copyWith(color: AppColors.red),
                                    ),
                                  ),
                                ],
                              ),
                            ),

                          // Quick Access Section
                          Text(
                            'QUICK ACCESS',
                            style: AppTextStyles.labelSmall.copyWith(
                              letterSpacing: 1.5,
                              fontWeight: FontWeight.w700,
                              color: AppColors.gray500,
                            ),
                          ),
                          const SizedBox(height: 16),

                          // Navigation Grid (2 columns)
                          GridView.count(
                            crossAxisCount: 2,
                            shrinkWrap: true,
                            physics: const NeverScrollableScrollPhysics(),
                            mainAxisSpacing: 16,
                            crossAxisSpacing: 16,
                            childAspectRatio: 1.1,
                            children: [
                              NavGridItem(
                                label: 'Messages',
                                icon: Icons.email_outlined,
                                iconColor: AppColors.primary,
                                backgroundColor: AppColors.blueTint,
                                badgeCount: _unreadMessages,
                                onTap: () => Navigator.pushNamed(context, '/messages'),
                              ),
                              NavGridItem(
                                label: 'Courses',
                                icon: Icons.menu_book_outlined,
                                iconColor: AppColors.purple,
                                backgroundColor: AppColors.purpleTint,
                                badgeCount: _newLessons,
                                onTap: () => Navigator.pushNamed(context, '/courses'),
                              ),
                              NavGridItem(
                                label: 'Documents',
                                icon: Icons.description_outlined,
                                iconColor: AppColors.orange,
                                backgroundColor: AppColors.orangeTint,
                                badgeCount: _newDocuments,
                                onTap: () => Navigator.pushNamed(context, '/documents'),
                              ),
                              NavGridItem(
                                label: 'Deliberations',
                                icon: Icons.gavel_outlined,
                                iconColor: AppColors.green,
                                backgroundColor: AppColors.greenTint,
                                onTap: () => Navigator.pushNamed(context, '/deliberations'),
                              ),
                              NavGridItem(
                                label: 'Homeworks',
                                icon: Icons.assignment_outlined,
                                iconColor: AppColors.red,
                                backgroundColor: AppColors.redTint,
                                onTap: () => Navigator.pushNamed(context, '/homeworks'),
                              ),
                              NavGridItem(
                                label: 'Schedule',
                                icon: Icons.calendar_today_outlined,
                                iconColor: AppColors.primary,
                                backgroundColor: AppColors.blueTint,
                                onTap: () => Navigator.pushNamed(context, '/schedule'),
                              ),
                              NavGridItem(
                                label: 'Attendance',
                                icon: Icons.fact_check_outlined,
                                iconColor: const Color(0xFF0891B2),
                                backgroundColor: const Color(0xFFECFEFF),
                                onTap: () => Navigator.pushNamed(context, '/attendance'),
                              ),
                              NavGridItem(
                                label: 'Exams',
                                icon: Icons.quiz_outlined,
                                iconColor: AppColors.yellow,
                                backgroundColor: AppColors.yellowTint,
                                onTap: () => Navigator.pushNamed(context, '/exams'),
                              ),
                              NavGridItem(
                                label: 'Profile',
                                icon: Icons.person_outline,
                                iconColor: AppColors.gray600,
                                backgroundColor: AppColors.gray100,
                                onTap: () => Navigator.pushNamed(context, '/profile'),
                              ),
                            ],
                          ),
                          const SizedBox(height: 40),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
    );
  }
}
