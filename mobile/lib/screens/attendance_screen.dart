import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../config/theme.dart';
import '../config/api_config.dart';
import '../services/api_service.dart';
import '../widgets/loading_indicator.dart';

class AttendanceScreen extends StatefulWidget {
  const AttendanceScreen({super.key});

  @override
  State<AttendanceScreen> createState() => _AttendanceScreenState();
}

class _AttendanceScreenState extends State<AttendanceScreen> {
  bool _isLoading = true;
  String? _error;
  Map<String, dynamic> _attendanceData = {};
  List<dynamic> _records = [];

  @override
  void initState() {
    super.initState();
    _loadAttendance();
  }

  Future<void> _loadAttendance() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final api = context.read<ApiService>();
      final data = await api.get(ApiConfig.attendance);
      setState(() {
        _attendanceData = data['summary'] ?? data['statistics'] ?? {};
        _records = data['records'] ?? data['attendance'] ?? [];
      });
    } catch (e) {
      setState(() => _error = e.toString());
    } finally {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final total = _attendanceData['total'] ?? 0;
    final present = _attendanceData['present'] ?? 0;
    final late_ = _attendanceData['late'] ?? 0;
    final absent = _attendanceData['absent'] ?? 0;
    final excused = _attendanceData['excused'] ?? 0;

    return Scaffold(
      backgroundColor: AppColors.scaffoldBg,
      appBar: AppBar(
        title: Text('Attendance', style: AppTextStyles.titleMedium),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: _isLoading
          ? const LoadingIndicator(message: 'Loading attendance...')
          : _error != null
              ? _buildError()
              : RefreshIndicator(
                  onRefresh: _loadAttendance,
                  color: AppColors.primary,
                  child: SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Summary Cards
                        _buildSummaryGrid(total, present, late_, absent, excused),
                        const SizedBox(height: 24),

                        // Attendance Rate Bar
                        if (total > 0) ...[
                          _buildAttendanceBar(total, present, late_, absent, excused),
                          const SizedBox(height: 24),
                        ],

                        // Records List
                        Text(
                          'ATTENDANCE RECORDS',
                          style: AppTextStyles.labelSmall.copyWith(
                            letterSpacing: 1.5,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                        const SizedBox(height: 12),
                        if (_records.isEmpty)
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
                                  Icon(Icons.fact_check_outlined,
                                      size: 48, color: AppColors.gray300),
                                  const SizedBox(height: 12),
                                  Text('No attendance records',
                                      style: AppTextStyles.bodyMedium),
                                ],
                              ),
                            ),
                          )
                        else
                          ..._records.map((r) => _buildRecordItem(r)),
                      ],
                    ),
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
            onPressed: _loadAttendance,
            icon: const Icon(Icons.refresh, size: 18),
            label: const Text('Retry'),
          ),
        ],
      ),
    );
  }

  Widget _buildSummaryGrid(int total, int present, int late_, int absent, int excused) {
    return GridView.count(
      crossAxisCount: 2,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      mainAxisSpacing: 10,
      crossAxisSpacing: 10,
      childAspectRatio: 2.0,
      children: [
        _buildSummaryCard('Total', '$total', Icons.calendar_today_outlined,
            AppColors.primary, AppColors.blueTint),
        _buildSummaryCard('Present', '$present', Icons.check_circle_outline,
            AppColors.green, AppColors.greenTint),
        _buildSummaryCard('Late', '$late_', Icons.access_time,
            AppColors.orange, AppColors.orangeTint),
        _buildSummaryCard('Absent', '$absent', Icons.cancel_outlined,
            AppColors.red, AppColors.redTint),
      ],
    );
  }

  Widget _buildSummaryCard(
      String label, String value, IconData icon, Color color, Color bg) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.gray200),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: bg,
              borderRadius: BorderRadius.circular(8),
            ),
            child: Icon(icon, size: 20, color: color),
          ),
          const SizedBox(width: 10),
          Column(
            mainAxisAlignment: MainAxisAlignment.center,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(value,
                  style: AppTextStyles.headlineMedium
                      .copyWith(color: color)),
              Text(label, style: AppTextStyles.caption),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildAttendanceBar(int total, int present, int late_, int absent, int excused) {
    final rate = total > 0 ? ((present + late_) / total * 100) : 0.0;

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
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('Attendance Rate', style: AppTextStyles.labelLarge),
              Text(
                '${rate.toStringAsFixed(1)}%',
                style: AppTextStyles.headlineMedium.copyWith(
                  color: rate >= 80 ? AppColors.green : AppColors.orange,
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          ClipRRect(
            borderRadius: BorderRadius.circular(6),
            child: SizedBox(
              height: 12,
              child: Row(
                children: [
                  if (present > 0)
                    Expanded(
                      flex: present,
                      child: Container(color: AppColors.green),
                    ),
                  if (late_ > 0)
                    Expanded(
                      flex: late_,
                      child: Container(color: AppColors.orange),
                    ),
                  if (absent > 0)
                    Expanded(
                      flex: absent,
                      child: Container(color: AppColors.red),
                    ),
                  if (excused > 0)
                    Expanded(
                      flex: excused,
                      child: Container(color: AppColors.purple),
                    ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 12),
          Wrap(
            spacing: 16,
            runSpacing: 8,
            children: [
              _legendDot('Present', AppColors.green),
              _legendDot('Late', AppColors.orange),
              _legendDot('Absent', AppColors.red),
              _legendDot('Excused', AppColors.purple),
            ],
          ),
        ],
      ),
    );
  }

  Widget _legendDot(String label, Color color) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(
          width: 10,
          height: 10,
          decoration: BoxDecoration(color: color, borderRadius: BorderRadius.circular(3)),
        ),
        const SizedBox(width: 4),
        Text(label, style: AppTextStyles.caption.copyWith(fontSize: 11)),
      ],
    );
  }

  Widget _buildRecordItem(dynamic record) {
    final status = (record['status'] ?? '').toString().toLowerCase();
    Color statusColor;
    IconData statusIcon;

    switch (status) {
      case 'present':
        statusColor = AppColors.green;
        statusIcon = Icons.check_circle_outline;
        break;
      case 'late':
        statusColor = AppColors.orange;
        statusIcon = Icons.access_time;
        break;
      case 'absent':
        statusColor = AppColors.red;
        statusIcon = Icons.cancel_outlined;
        break;
      case 'excused':
        statusColor = AppColors.purple;
        statusIcon = Icons.info_outline;
        break;
      default:
        statusColor = AppColors.gray500;
        statusIcon = Icons.help_outline;
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: AppColors.gray200),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: statusColor.withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Icon(statusIcon, size: 20, color: statusColor),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  record['module_name'] ?? record['module']?['name'] ?? 'Module',
                  style: AppTextStyles.labelLarge.copyWith(fontSize: 13),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 2),
                Text(
                  record['date'] ?? '',
                  style: AppTextStyles.caption,
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            decoration: BoxDecoration(
              color: statusColor.withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Text(
              status[0].toUpperCase() + status.substring(1),
              style: AppTextStyles.caption.copyWith(
                color: statusColor,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
