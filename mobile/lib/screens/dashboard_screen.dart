import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
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
  Map<String, dynamic> _stats = {};
  List<dynamic> _recentGrades = [];
  List<dynamic> _upcomingExams = [];

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

      setState(() {
        _stats = Map<String, dynamic>.from(dashData['statistics'] ?? {});
        _recentGrades = List<dynamic>.from(dashData['recent_grades'] ?? []);
        _upcomingExams = List<dynamic>.from(dashData['upcoming_exams'] ?? []);
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

    // White status bar icons over the navy header
    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: SystemUiOverlayStyle.light.copyWith(
        statusBarColor: Colors.transparent,
      ),
      child: Scaffold(
        backgroundColor: AppColors.scaffoldBg,
        body: _isLoading
            ? const LoadingIndicator(message: 'Loading dashboard...')
            : RefreshIndicator(
                onRefresh: _loadDashboard,
                color: AppColors.teal,
                child: ListView(
                  padding: EdgeInsets.zero,
                  physics: const AlwaysScrollableScrollPhysics(),
                  children: [
                    _buildHeader(auth),
                    // Summary card overlapping the header
                    Transform.translate(
                      offset: const Offset(0, -44),
                      child: Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                        child: _buildSummaryCard(),
                      ),
                    ),
                    Transform.translate(
                      offset: const Offset(0, -24),
                      child: Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            if (_error != null) _buildError(),
                            _sectionTitle('Quick access'),
                            _buildGrid(),
                            if (_recentGrades.isNotEmpty) ...[
                              const SizedBox(height: 24),
                              _sectionTitle(
                                'Latest grades',
                                action: 'See all',
                                route: '/exams',
                              ),
                              _buildRecentGrades(),
                            ],
                            if (_upcomingExams.isNotEmpty) ...[
                              const SizedBox(height: 24),
                              _sectionTitle(
                                'Upcoming exams',
                                action: 'See all',
                                route: '/exams',
                              ),
                              _buildUpcomingExams(),
                            ],
                            const SizedBox(height: 16),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
              ),
      ),
    );
  }

  // ── Header ────────────────────────────────────────────────────────────────
  Widget _buildHeader(AuthService auth) {
    final firstName = auth.userName.split(' ').first;
    final initials = auth.userName
        .split(' ')
        .where((p) => p.isNotEmpty)
        .take(2)
        .map((p) => p[0].toUpperCase())
        .join();

    return Container(
      decoration: const BoxDecoration(gradient: AppColors.headerGradient),
      child: Stack(
        children: [
          Positioned.fill(child: CustomPaint(painter: _ArcsPainter())),
          SafeArea(
            bottom: false,
            child: Padding(
              padding: const EdgeInsets.fromLTRB(20, 12, 16, 68),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Container(
                        width: 36,
                        height: 36,
                        padding: const EdgeInsets.all(5),
                        decoration: BoxDecoration(
                          color: AppColors.white,
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: Image.asset('assets/images/logo.png'),
                      ),
                      const SizedBox(width: 10),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'INSFP',
                              style: AppTextStyles.headlineMedium.copyWith(
                                color: AppColors.white,
                                fontSize: 16,
                                letterSpacing: 1.2,
                              ),
                            ),
                            Text(
                              'Student space',
                              style: AppTextStyles.caption.copyWith(
                                color: AppColors.white.withValues(alpha: 0.7),
                                fontSize: 11,
                              ),
                            ),
                          ],
                        ),
                      ),
                      _HeaderIconButton(
                        icon: Icons.notifications_none_rounded,
                        showDot: _unreadMessages > 0,
                        onTap: () => Navigator.pushNamed(context, '/messages'),
                      ),
                      const SizedBox(width: 8),
                      GestureDetector(
                        onTap: () => Navigator.pushNamed(context, '/profile'),
                        child: CircleAvatar(
                          radius: 19,
                          backgroundColor: AppColors.gold,
                          child: Text(
                            initials,
                            style: AppTextStyles.labelLarge.copyWith(
                              color: AppColors.navy900,
                              fontSize: 13,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 26),
                  Text(
                    'Hello, $firstName',
                    style: AppTextStyles.displayMedium.copyWith(
                      color: AppColors.white,
                      fontSize: 26,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 10),
                  if (auth.specialty != null)
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 12,
                        vertical: 6,
                      ),
                      decoration: BoxDecoration(
                        color: AppColors.white.withValues(alpha: 0.12),
                        borderRadius: BorderRadius.circular(999),
                        border: Border.all(
                          color: AppColors.white.withValues(alpha: 0.18),
                        ),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          const Icon(
                            Icons.school_outlined,
                            size: 15,
                            color: AppColors.gold,
                          ),
                          const SizedBox(width: 6),
                          Flexible(
                            child: Text(
                              '${auth.specialty!['name']} · Semester ${auth.currentSemester ?? ''}',
                              style: AppTextStyles.bodySmall.copyWith(
                                color: AppColors.white,
                                fontWeight: FontWeight.w500,
                              ),
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
        ],
      ),
    );
  }

  // ── Summary ───────────────────────────────────────────────────────────────
  Widget _buildSummaryCard() {
    final avg = _stats['semester_average'];
    final avgValue = avg == null ? null : double.tryParse(avg.toString());
    final rate = double.tryParse(
      (_stats['attendance']?['rate'] ?? '').toString(),
    );
    final pending = _stats['pending_homeworks'] ?? 0;
    final classes = _stats['classes_this_week'] ?? 0;

    return Container(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 14),
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: AppColors.gray200),
        boxShadow: AppShadows.medium,
      ),
      child: Column(
        children: [
          Row(
            children: [
              // Semester average with a gold progress ring
              SizedBox(
                width: 64,
                height: 64,
                child: Stack(
                  alignment: Alignment.center,
                  children: [
                    SizedBox.expand(
                      child: CircularProgressIndicator(
                        value: avgValue == null
                            ? 0
                            : (avgValue / 20).clamp(0.0, 1.0),
                        strokeWidth: 5,
                        backgroundColor: AppColors.gray100,
                        color: avgValue != null && avgValue < 10
                            ? AppColors.red
                            : AppColors.gold,
                        strokeCap: StrokeCap.round,
                      ),
                    ),
                    Text(
                      avgValue == null ? '—' : avgValue.toStringAsFixed(2),
                      style: AppTextStyles.headlineMedium.copyWith(
                        fontSize: 15,
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('SEMESTER AVERAGE', style: AppTextStyles.labelSmall),
                    const SizedBox(height: 4),
                    Text(
                      avgValue == null
                          ? 'No published grade yet'
                          : avgValue >= 10
                          ? 'Above 10/20 — keep it up!'
                          : 'Below 10/20',
                      style: AppTextStyles.bodyMedium.copyWith(
                        color: avgValue == null
                            ? AppColors.gray500
                            : avgValue >= 10
                            ? AppColors.teal
                            : AppColors.red,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const Padding(
            padding: EdgeInsets.symmetric(vertical: 14),
            child: Divider(),
          ),
          Row(
            children: [
              _MiniStat(
                label: 'Attendance',
                value: rate == null ? '—' : '${rate.round()}%',
                color: rate != null && rate < 75
                    ? AppColors.red
                    : AppColors.navy,
              ),
              _divider(),
              _MiniStat(
                label: 'Classes / week',
                value: '$classes',
                color: AppColors.navy,
              ),
              _divider(),
              _MiniStat(
                label: 'Homework due',
                value: '$pending',
                color: pending > 0 ? AppColors.orange : AppColors.navy,
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _divider() =>
      Container(width: 1, height: 32, color: AppColors.gray200);

  // ── Quick access ──────────────────────────────────────────────────────────
  Widget _buildGrid() {
    final items = [
      ('Schedule', Icons.calendar_month_outlined, '/schedule', 0, false),
      ('Exams', Icons.fact_check_outlined, '/exams', 0, true),
      ('Courses', Icons.menu_book_outlined, '/courses', _newLessons, false),
      ('Homeworks', Icons.assignment_outlined, '/homeworks', 0, true),
      ('Attendance', Icons.how_to_reg_outlined, '/attendance', 0, false),
      ('Results', Icons.workspace_premium_outlined, '/deliberations', 0, true),
      (
        'Messages',
        Icons.mail_outline_rounded,
        '/messages',
        _unreadMessages,
        false,
      ),
      ('Documents', Icons.folder_outlined, '/documents', _newDocuments, true),
      ('Profile', Icons.person_outline_rounded, '/profile', 0, false),
    ];

    return GridView.count(
      crossAxisCount: 3,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      mainAxisSpacing: 12,
      crossAxisSpacing: 12,
      childAspectRatio: 0.98,
      children: [
        for (final (label, icon, route, badge, teal) in items)
          NavGridItem(
            label: label,
            icon: icon,
            iconColor: teal ? AppColors.teal : AppColors.navy,
            backgroundColor: teal ? AppColors.teal100 : AppColors.navy100,
            badgeCount: badge,
            onTap: () => Navigator.pushNamed(context, route),
          ),
      ],
    );
  }

  // ── Lists ─────────────────────────────────────────────────────────────────
  Widget _buildRecentGrades() {
    return _card(
      children: [
        for (final (i, g) in _recentGrades.take(3).indexed) ...[
          if (i > 0) const Divider(),
          _ListRow(
            leading: _gradeBadge(double.tryParse('${g['grade']}') ?? 0),
            title: g['module']?['name'] ?? '',
            subtitle: _examTypeLabel(g['exam_type']),
          ),
        ],
      ],
    );
  }

  Widget _buildUpcomingExams() {
    return _card(
      children: [
        for (final (i, e) in _upcomingExams.take(3).indexed) ...[
          if (i > 0) const Divider(),
          _ListRow(
            leading: _dateBadge(e['date']?.toString() ?? ''),
            title: e['module']?['name'] ?? '',
            subtitle:
                '${_examTypeLabel(e['type'])} · ${e['start_time'] ?? ''}${e['room'] != null ? ' · ${e['room']}' : ''}',
          ),
        ],
      ],
    );
  }

  Widget _card({required List<Widget> children}) => Container(
    decoration: BoxDecoration(
      color: AppColors.white,
      borderRadius: BorderRadius.circular(14),
      border: Border.all(color: AppColors.gray200),
      boxShadow: AppShadows.small,
    ),
    child: Column(children: children),
  );

  Widget _gradeBadge(double grade) {
    final ok = grade >= 10;
    return Container(
      width: 52,
      padding: const EdgeInsets.symmetric(vertical: 8),
      decoration: BoxDecoration(
        color: ok ? AppColors.teal100 : AppColors.redTint,
        borderRadius: BorderRadius.circular(10),
      ),
      child: Text(
        grade.toStringAsFixed(grade == grade.roundToDouble() ? 0 : 2),
        textAlign: TextAlign.center,
        style: AppTextStyles.labelLarge.copyWith(
          color: ok ? AppColors.teal : AppColors.red,
          fontSize: 15,
        ),
      ),
    );
  }

  Widget _dateBadge(String date) {
    const months = [
      'JAN',
      'FEB',
      'MAR',
      'APR',
      'MAY',
      'JUN',
      'JUL',
      'AUG',
      'SEP',
      'OCT',
      'NOV',
      'DEC',
    ];
    final d = DateTime.tryParse(date);
    return Container(
      width: 52,
      padding: const EdgeInsets.symmetric(vertical: 6),
      decoration: BoxDecoration(
        color: AppColors.gold100,
        borderRadius: BorderRadius.circular(10),
      ),
      child: Column(
        children: [
          Text(
            d == null ? '' : '${d.day}',
            style: AppTextStyles.headlineMedium.copyWith(
              fontSize: 17,
              height: 1,
            ),
          ),
          Text(
            d == null ? '' : months[d.month - 1],
            style: AppTextStyles.labelSmall.copyWith(
              color: AppColors.gold,
              fontSize: 10,
            ),
          ),
        ],
      ),
    );
  }

  String _examTypeLabel(dynamic type) => switch (type) {
    'controle' => 'Test',
    'examen' => 'Final exam',
    'rattrapage' => 'Retake',
    _ => '$type',
  };

  Widget _sectionTitle(String title, {String? action, String? route}) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(
        children: [
          Container(
            width: 3,
            height: 16,
            decoration: BoxDecoration(
              color: AppColors.gold,
              borderRadius: BorderRadius.circular(2),
            ),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: Text(
              title,
              style: AppTextStyles.headlineMedium.copyWith(fontSize: 17),
            ),
          ),
          if (action != null)
            GestureDetector(
              onTap: () => Navigator.pushNamed(context, route!),
              child: Text(
                action,
                style: AppTextStyles.labelLarge.copyWith(
                  color: AppColors.teal,
                  fontSize: 13,
                ),
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildError() {
    return Container(
      margin: const EdgeInsets.only(bottom: 20),
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
    );
  }
}

class _MiniStat extends StatelessWidget {
  final String label;
  final String value;
  final Color color;

  const _MiniStat({
    required this.label,
    required this.value,
    required this.color,
  });

  @override
  Widget build(BuildContext context) {
    return Expanded(
      child: Column(
        children: [
          Text(
            value,
            style: AppTextStyles.headlineMedium.copyWith(
              color: color,
              fontSize: 19,
            ),
          ),
          const SizedBox(height: 2),
          Text(
            label,
            style: AppTextStyles.bodySmall.copyWith(fontSize: 11),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }
}

class _ListRow extends StatelessWidget {
  final Widget leading;
  final String title;
  final String subtitle;

  const _ListRow({
    required this.leading,
    required this.title,
    required this.subtitle,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(12),
      child: Row(
        children: [
          leading,
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: AppTextStyles.labelLarge,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 2),
                Text(
                  subtitle,
                  style: AppTextStyles.bodySmall,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _HeaderIconButton extends StatelessWidget {
  final IconData icon;
  final bool showDot;
  final VoidCallback onTap;

  const _HeaderIconButton({
    required this.icon,
    required this.onTap,
    this.showDot = false,
  });

  @override
  Widget build(BuildContext context) {
    return Material(
      color: AppColors.white.withValues(alpha: 0.12),
      shape: const CircleBorder(),
      child: InkWell(
        customBorder: const CircleBorder(),
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.all(8),
          child: Stack(
            clipBehavior: Clip.none,
            children: [
              Icon(icon, color: AppColors.white, size: 22),
              if (showDot)
                Positioned(
                  right: 0,
                  top: 0,
                  child: Container(
                    width: 9,
                    height: 9,
                    decoration: BoxDecoration(
                      color: AppColors.gold,
                      shape: BoxShape.circle,
                      border: Border.all(color: AppColors.navy, width: 1.5),
                    ),
                  ),
                ),
            ],
          ),
        ),
      ),
    );
  }
}

/// Faint concentric arcs on the header, echoing the web hero pattern.
class _ArcsPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..style = PaintingStyle.stroke
      ..strokeWidth = 1
      ..color = Colors.white.withValues(alpha: 0.06);
    final center = Offset(size.width * 0.95, size.height * 0.1);
    for (var r = 40.0; r < size.width; r += 34) {
      canvas.drawCircle(center, r, paint);
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
