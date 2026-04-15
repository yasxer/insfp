import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../config/theme.dart';
import '../config/api_config.dart';
import '../services/api_service.dart';
import '../widgets/loading_indicator.dart';
import '../widgets/stat_card.dart';

class ExamsScreen extends StatefulWidget {
  const ExamsScreen({super.key});

  @override
  State<ExamsScreen> createState() => _ExamsScreenState();
}

class _ExamsScreenState extends State<ExamsScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;
  bool _isLoading = true;
  String? _error;
  List<dynamic> _results = [];
  List<dynamic> _upcoming = [];

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    _loadExams();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadExams() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final api = context.read<ApiService>();
      final data = await Future.wait([
        api.get(ApiConfig.examResults),
        api.get(ApiConfig.upcomingExams),
      ]);
      setState(() {
        _results = data[0]['results'] ?? [];
        _upcoming = data[1]['exams'] ?? [];
      });
    } catch (e) {
      setState(() => _error = e.toString());
    } finally {
      setState(() => _isLoading = false);
    }
  }

  double get _averageGrade {
    if (_results.isEmpty) return 0;
    final sum = _results.fold<double>(
        0, (prev, e) => prev + ((e['grade'] ?? 0) as num).toDouble());
    return sum / _results.length;
  }

  int get _passedCount =>
      _results.where((e) => ((e['grade'] ?? 0) as num) >= 10).length;
  int get _failedCount =>
      _results.where((e) => ((e['grade'] ?? 0) as num) < 10).length;

  Color _gradeColor(num grade) {
    if (grade >= 16) return AppColors.green;
    if (grade >= 14) return AppColors.primary;
    if (grade >= 10) return AppColors.orange;
    return AppColors.red;
  }

  String _gradeBadge(num grade) {
    if (grade >= 16) return 'Excellent';
    if (grade >= 14) return 'Very Good';
    if (grade >= 12) return 'Good';
    if (grade >= 10) return 'Pass';
    return 'Failed';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.scaffoldBg,
      appBar: AppBar(
        title: Text('Exams & Results', style: AppTextStyles.titleMedium),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
        bottom: TabBar(
          controller: _tabController,
          labelColor: AppColors.primary,
          unselectedLabelColor: AppColors.gray400,
          indicatorColor: AppColors.primary,
          indicatorWeight: 3,
          labelStyle: AppTextStyles.labelLarge,
          unselectedLabelStyle: AppTextStyles.bodyMedium,
          tabs: [
            const Tab(text: 'Results'),
            Tab(
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Text('Upcoming'),
                  if (_upcoming.isNotEmpty) ...[
                    const SizedBox(width: 6),
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 6, vertical: 2),
                      decoration: BoxDecoration(
                        color: AppColors.primary,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        '${_upcoming.length}',
                        style: AppTextStyles.caption.copyWith(
                          color: AppColors.white,
                          fontSize: 10,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                    ),
                  ],
                ],
              ),
            ),
          ],
        ),
      ),
      body: _isLoading
          ? const LoadingIndicator(message: 'Loading exams...')
          : _error != null
              ? Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const Icon(Icons.error_outline,
                          size: 48, color: AppColors.gray400),
                      const SizedBox(height: 16),
                      Text(_error!, style: AppTextStyles.bodyMedium),
                      const SizedBox(height: 16),
                      ElevatedButton.icon(
                        onPressed: _loadExams,
                        icon: const Icon(Icons.refresh, size: 18),
                        label: const Text('Retry'),
                      ),
                    ],
                  ),
                )
              : TabBarView(
                  controller: _tabController,
                  children: [
                    _buildResultsTab(),
                    _buildUpcomingTab(),
                  ],
                ),
    );
  }

  Widget _buildResultsTab() {
    return RefreshIndicator(
      onRefresh: _loadExams,
      color: AppColors.primary,
      child: SingleChildScrollView(
        physics: const AlwaysScrollableScrollPhysics(),
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            // Stats
            GridView.count(
              crossAxisCount: 2,
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              mainAxisSpacing: 10,
              crossAxisSpacing: 10,
              childAspectRatio: 1.6,
              children: [
                StatCard(
                  title: 'Average',
                  value: '${_averageGrade.toStringAsFixed(1)} / 20',
                  icon: Icons.analytics_outlined,
                  iconColor: AppColors.purple,
                  backgroundColor: AppColors.purpleTint,
                ),
                StatCard(
                  title: 'Total Exams',
                  value: '${_results.length}',
                  icon: Icons.quiz_outlined,
                  iconColor: AppColors.primary,
                  backgroundColor: AppColors.blueTint,
                ),
                StatCard(
                  title: 'Passed',
                  value: '$_passedCount',
                  icon: Icons.check_circle_outline,
                  iconColor: AppColors.green,
                  backgroundColor: AppColors.greenTint,
                ),
                StatCard(
                  title: 'Failed',
                  value: '$_failedCount',
                  icon: Icons.cancel_outlined,
                  iconColor: AppColors.red,
                  backgroundColor: AppColors.redTint,
                ),
              ],
            ),
            const SizedBox(height: 20),

            // Results list
            if (_results.isEmpty)
              Container(
                padding: const EdgeInsets.all(32),
                decoration: BoxDecoration(
                  color: AppColors.white,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: AppColors.gray200),
                ),
                child: Center(
                  child: Column(
                    children: [
                      Icon(Icons.quiz_outlined,
                          size: 48, color: AppColors.gray300),
                      const SizedBox(height: 12),
                      Text('No exam results yet',
                          style: AppTextStyles.bodyMedium),
                    ],
                  ),
                ),
              )
            else
              ..._results.map((exam) => _buildResultCard(exam)),
          ],
        ),
      ),
    );
  }

  Widget _buildResultCard(Map<String, dynamic> exam) {
    final grade = ((exam['grade'] ?? 0) as num);
    final color = _gradeColor(grade);

    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.gray200),
      ),
      child: Row(
        children: [
          // Grade circle
          Container(
            width: 52,
            height: 52,
            decoration: BoxDecoration(
              color: color.withValues(alpha: 0.1),
              shape: BoxShape.circle,
              border: Border.all(color: color.withValues(alpha: 0.3), width: 2),
            ),
            alignment: Alignment.center,
            child: Text(
              grade.toStringAsFixed(grade.truncateToDouble() == grade ? 0 : 1),
              style: AppTextStyles.headlineMedium.copyWith(
                color: color,
                fontSize: 16,
              ),
            ),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  exam['subject'] ?? exam['module_name'] ?? 'Exam',
                  style: AppTextStyles.labelLarge,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 4),
                Row(
                  children: [
                    if (exam['type'] != null) ...[
                      Text(exam['type'],
                          style: AppTextStyles.caption
                              .copyWith(color: AppColors.gray500)),
                      const SizedBox(width: 8),
                    ],
                    if (exam['date'] != null)
                      Text(exam['date'], style: AppTextStyles.caption),
                  ],
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            decoration: BoxDecoration(
              color: color.withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Text(
              _gradeBadge(grade),
              style: AppTextStyles.caption.copyWith(
                color: color,
                fontWeight: FontWeight.w600,
                fontSize: 11,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildUpcomingTab() {
    if (_upcoming.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.event_available_outlined,
                size: 64, color: AppColors.gray300),
            const SizedBox(height: 16),
            Text('No upcoming exams',
                style: AppTextStyles.headlineMedium
                    .copyWith(color: AppColors.gray500)),
          ],
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _loadExams,
      color: AppColors.primary,
      child: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: _upcoming.length,
        separatorBuilder: (_, _) => const SizedBox(height: 10),
        itemBuilder: (context, index) =>
            _buildUpcomingCard(_upcoming[index]),
      ),
    );
  }

  Widget _buildUpcomingCard(Map<String, dynamic> exam) {
    final isSoon = _isExamSoon(exam['date']);

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: isSoon
              ? AppColors.red.withValues(alpha: 0.3)
              : AppColors.gray200,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Expanded(
                child: Text(
                  exam['subject'] ?? exam['module_name'] ?? 'Exam',
                  style: AppTextStyles.headlineMedium,
                ),
              ),
              if (isSoon)
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  decoration: BoxDecoration(
                    color: AppColors.redTint,
                    borderRadius: BorderRadius.circular(6),
                  ),
                  child: Text('SOON',
                      style: AppTextStyles.caption.copyWith(
                        color: AppColors.red,
                        fontWeight: FontWeight.w700,
                        fontSize: 10,
                      )),
                ),
            ],
          ),
          const SizedBox(height: 12),
          _infoRow(Icons.calendar_today_outlined, exam['date'] ?? ''),
          const SizedBox(height: 6),
          _infoRow(Icons.access_time, exam['time'] ?? ''),
          if (exam['room'] != null) ...[
            const SizedBox(height: 6),
            _infoRow(Icons.location_on_outlined, exam['room']),
          ],
          if (exam['type'] != null) ...[
            const SizedBox(height: 6),
            _infoRow(Icons.label_outline, exam['type']),
          ],
          if (exam['duration'] != null) ...[
            const SizedBox(height: 6),
            _infoRow(Icons.timelapse_outlined, 'Duration: ${exam['duration']}'),
          ],
        ],
      ),
    );
  }

  Widget _infoRow(IconData icon, String text) {
    return Row(
      children: [
        Icon(icon, size: 16, color: AppColors.gray400),
        const SizedBox(width: 8),
        Text(text, style: AppTextStyles.bodySmall.copyWith(color: AppColors.gray700)),
      ],
    );
  }

  bool _isExamSoon(String? dateStr) {
    if (dateStr == null) return false;
    try {
      final examDate = DateTime.parse(dateStr);
      final diff = examDate.difference(DateTime.now()).inDays;
      return diff <= 7 && diff >= 0;
    } catch (_) {
      return false;
    }
  }
}
