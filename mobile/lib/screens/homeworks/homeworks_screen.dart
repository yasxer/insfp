import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../config/theme.dart';
import '../../config/api_config.dart';
import '../../services/api_service.dart';
import '../../widgets/loading_indicator.dart';

class HomeworksScreen extends StatefulWidget {
  const HomeworksScreen({super.key});

  @override
  State<HomeworksScreen> createState() => _HomeworksScreenState();
}

class _HomeworksScreenState extends State<HomeworksScreen> {
  bool _isLoading = true;
  String? _error;
  List<dynamic> _homeworks = [];

  @override
  void initState() {
    super.initState();
    _loadHomeworks();
  }

  Future<void> _loadHomeworks() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final api = context.read<ApiService>();
      final data = await api.get(ApiConfig.homeworks);
      setState(() {
        _homeworks = data['homeworks'] ?? data['data'] ?? data ?? [];
      });
    } catch (e) {
      setState(() => _error = e.toString());
    } finally {
      setState(() => _isLoading = false);
    }
  }

  Color _getStatusColor(String? status) {
    switch (status?.toLowerCase()) {
      case 'submitted':
      case 'soumis':
        return AppColors.primary;
      case 'graded':
      case 'not\u00e9':
        return AppColors.green;
      case 'late':
      case 'en retard':
        return AppColors.orange;
      case 'pending':
      case 'en attente':
      default:
        return AppColors.red;
    }
  }

  String _getStatusLabel(String? status) {
    switch (status?.toLowerCase()) {
      case 'submitted':
      case 'soumis':
        return 'Submitted';
      case 'graded':
      case 'not\u00e9':
        return 'Graded';
      case 'late':
      case 'en retard':
        return 'Late';
      case 'pending':
      case 'en attente':
        return 'Pending';
      default:
        return status ?? 'Pending';
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.scaffoldBg,
      appBar: AppBar(
        title: Text('Homeworks', style: AppTextStyles.titleMedium),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: _isLoading
          ? const LoadingIndicator(message: 'Loading homeworks...')
          : _error != null
              ? _buildError()
              : _homeworks.isEmpty
                  ? _buildEmpty()
                  : RefreshIndicator(
                      onRefresh: _loadHomeworks,
                      color: AppColors.primary,
                      child: ListView.separated(
                        padding: const EdgeInsets.all(16),
                        itemCount: _homeworks.length,
                        separatorBuilder: (_, _) => const SizedBox(height: 10),
                        itemBuilder: (context, index) =>
                            _buildHomeworkCard(_homeworks[index]),
                      ),
                    ),
    );
  }

  Widget _buildError() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Icon(Icons.error_outline, size: 48, color: AppColors.gray400),
          const SizedBox(height: 16),
          Text(_error!, style: AppTextStyles.bodyMedium),
          const SizedBox(height: 16),
          ElevatedButton.icon(
            onPressed: _loadHomeworks,
            icon: const Icon(Icons.refresh, size: 18),
            label: const Text('Retry'),
          ),
        ],
      ),
    );
  }

  Widget _buildEmpty() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.assignment_outlined, size: 64, color: AppColors.gray300),
          const SizedBox(height: 16),
          Text('No homeworks assigned',
              style: AppTextStyles.headlineMedium
                  .copyWith(color: AppColors.gray500)),
          const SizedBox(height: 8),
          Text('Homework assignments will appear here',
              style: AppTextStyles.bodyMedium),
        ],
      ),
    );
  }

  Widget _buildHomeworkCard(Map<String, dynamic> hw) {
    final status = hw['submission_status'] ?? hw['status'] ?? 'pending';
    final statusColor = _getStatusColor(status);
    final statusLabel = _getStatusLabel(status);
    final canSubmit =
        status == 'pending' || status == 'en attente' || status == null;
    final grade = hw['grade'];

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.gray200),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: statusColor.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Icon(Icons.assignment_outlined,
                    size: 24, color: statusColor),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      hw['title'] ?? 'Homework',
                      style: AppTextStyles.labelLarge,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 4),
                    if (hw['module_name'] != null || hw['module'] != null)
                      Text(
                        hw['module_name'] ??
                            (hw['module'] is Map
                                ? hw['module']['name']
                                : '') ??
                            '',
                        style: AppTextStyles.caption.copyWith(
                          color: AppColors.purple,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                  ],
                ),
              ),
              // Status chip
              Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: statusColor.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Text(
                  statusLabel,
                  style: AppTextStyles.caption.copyWith(
                    color: statusColor,
                    fontWeight: FontWeight.w600,
                    fontSize: 11,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),

          // Description
          if (hw['description'] != null) ...[
            Text(
              hw['description'],
              style: AppTextStyles.bodySmall.copyWith(color: AppColors.gray600),
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
            ),
            const SizedBox(height: 12),
          ],

          // Due date and grade
          Row(
            children: [
              if (hw['due_date'] != null) ...[
                const Icon(Icons.schedule_outlined,
                    size: 16, color: AppColors.gray400),
                const SizedBox(width: 4),
                Text(
                  'Due: ${hw['due_date']}',
                  style: AppTextStyles.caption,
                ),
              ],
              const Spacer(),
              if (grade != null) ...[
                const Icon(Icons.star_border, size: 16, color: AppColors.orange),
                const SizedBox(width: 4),
                Text(
                  '$grade / 20',
                  style: AppTextStyles.labelLarge.copyWith(
                    fontSize: 13,
                    color: (grade as num) >= 10
                        ? AppColors.green
                        : AppColors.red,
                  ),
                ),
              ],
            ],
          ),

          // Submit Button
          if (canSubmit) ...[
            const SizedBox(height: 12),
            SizedBox(
              width: double.infinity,
              child: OutlinedButton.icon(
                onPressed: () {
                  Navigator.pushNamed(context, '/homework-submit',
                      arguments: hw);
                },
                icon: const Icon(Icons.upload_file_outlined, size: 18),
                label: const Text('Submit'),
                style: OutlinedButton.styleFrom(
                  foregroundColor: AppColors.primary,
                  side: const BorderSide(color: AppColors.primary),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                  padding: const EdgeInsets.symmetric(vertical: 10),
                ),
              ),
            ),
          ],
        ],
      ),
    );
  }
}
