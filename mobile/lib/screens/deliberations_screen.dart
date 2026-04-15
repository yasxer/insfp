import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../config/theme.dart';
import '../config/api_config.dart';
import '../services/api_service.dart';
import '../widgets/loading_indicator.dart';

class DeliberationsScreen extends StatefulWidget {
  const DeliberationsScreen({super.key});

  @override
  State<DeliberationsScreen> createState() => _DeliberationsScreenState();
}

class _DeliberationsScreenState extends State<DeliberationsScreen> {
  bool _isLoading = true;
  String? _error;
  List<dynamic> _deliberations = [];

  @override
  void initState() {
    super.initState();
    _loadDeliberations();
  }

  Future<void> _loadDeliberations() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final api = context.read<ApiService>();
      final data = await api.get(ApiConfig.deliberations);
      setState(() {
        _deliberations = data['deliberations'] ?? data ?? [];
      });
    } catch (e) {
      setState(() => _error = e.toString());
    } finally {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.scaffoldBg,
      appBar: AppBar(
        title: Text('Deliberations', style: AppTextStyles.titleMedium),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: _isLoading
          ? const LoadingIndicator(message: 'Loading deliberations...')
          : _error != null
              ? _buildError()
              : _deliberations.isEmpty
                  ? _buildEmpty()
                  : RefreshIndicator(
                      onRefresh: _loadDeliberations,
                      color: AppColors.primary,
                      child: ListView.separated(
                        padding: const EdgeInsets.all(16),
                        itemCount: _deliberations.length,
                        separatorBuilder: (_, _) => const SizedBox(height: 12),
                        itemBuilder: (context, index) =>
                            _buildDeliberationCard(_deliberations[index]),
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
            onPressed: _loadDeliberations,
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
          Icon(Icons.gavel_outlined, size: 64, color: AppColors.gray300),
          const SizedBox(height: 16),
          Text('No deliberation results',
              style:
                  AppTextStyles.headlineMedium.copyWith(color: AppColors.gray500)),
          const SizedBox(height: 8),
          Text('Results will appear after deliberation sessions',
              style: AppTextStyles.bodyMedium),
        ],
      ),
    );
  }

  Widget _buildDeliberationCard(Map<String, dynamic> delib) {
    final status = (delib['status'] ?? '').toString().toLowerCase();
    final isAdmis = status == 'admis' || status == 'admitted' || status == 'passed';
    final average = delib['average'] ?? delib['moyenne'] ?? 0;

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.gray200),
        boxShadow: AppShadows.small,
      ),
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
                      delib['session_name'] ?? delib['semester'] ?? 'Semester',
                      style: AppTextStyles.headlineMedium,
                    ),
                    const SizedBox(height: 4),
                    if (delib['specialty_name'] != null)
                      Text(
                        delib['specialty_name'],
                        style: AppTextStyles.bodySmall.copyWith(
                          color: AppColors.purple,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                  ],
                ),
              ),
              Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                decoration: BoxDecoration(
                  color: isAdmis ? AppColors.greenTint : AppColors.redTint,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(
                    color: isAdmis
                        ? AppColors.green.withValues(alpha: 0.3)
                        : AppColors.red.withValues(alpha: 0.3),
                  ),
                ),
                child: Text(
                  isAdmis ? 'Admis' : 'Ajourn\u00e9',
                  style: AppTextStyles.labelLarge.copyWith(
                    fontSize: 13,
                    color: isAdmis ? AppColors.green : AppColors.red,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          // Average
          Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              color: AppColors.gray50,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Row(
                  children: [
                    Icon(
                      Icons.analytics_outlined,
                      size: 22,
                      color: isAdmis ? AppColors.green : AppColors.red,
                    ),
                    const SizedBox(width: 8),
                    Text('Average', style: AppTextStyles.bodyMedium),
                  ],
                ),
                Text(
                  '${average is double ? average.toStringAsFixed(2) : average} / 20',
                  style: AppTextStyles.headlineMedium.copyWith(
                    color: isAdmis ? AppColors.green : AppColors.red,
                  ),
                ),
              ],
            ),
          ),

          // Module results if available
          if (delib['modules'] != null && (delib['modules'] as List).isNotEmpty) ...[
            const SizedBox(height: 16),
            Text('Module Results',
                style: AppTextStyles.labelSmall.copyWith(
                    letterSpacing: 1, fontWeight: FontWeight.w600)),
            const SizedBox(height: 8),
            ...(delib['modules'] as List).map<Widget>((m) => Padding(
                  padding: const EdgeInsets.symmetric(vertical: 4),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(
                        child: Text(
                          m['name'] ?? '',
                          style: AppTextStyles.bodySmall
                              .copyWith(color: AppColors.gray700),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                      Text(
                        '${m['grade'] ?? '-'} / 20',
                        style: AppTextStyles.labelLarge.copyWith(
                          fontSize: 13,
                          color: ((m['grade'] ?? 0) as num) >= 10
                              ? AppColors.green
                              : AppColors.red,
                        ),
                      ),
                    ],
                  ),
                )),
          ],
        ],
      ),
    );
  }
}
