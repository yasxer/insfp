import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../config/theme.dart';
import '../config/api_config.dart';
import '../services/api_service.dart';
import '../widgets/loading_indicator.dart';

class CoursesScreen extends StatefulWidget {
  const CoursesScreen({super.key});

  @override
  State<CoursesScreen> createState() => _CoursesScreenState();
}

class _CoursesScreenState extends State<CoursesScreen> {
  bool _isLoading = true;
  String? _error;
  List<dynamic> _modules = [];
  int? _expandedModuleId;
  final Map<int, List<dynamic>> _moduleLessons = {};
  final Map<int, bool> _loadingLessons = {};

  @override
  void initState() {
    super.initState();
    _loadModules();
  }

  Future<void> _loadModules() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final api = context.read<ApiService>();
      final data = await api.get(ApiConfig.lessonModules);
      setState(() {
        _modules = data['modules'] ?? data ?? [];
      });
    } catch (e) {
      setState(() => _error = e.toString());
    } finally {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _loadLessons(int moduleId) async {
    if (_moduleLessons.containsKey(moduleId)) return;

    setState(() => _loadingLessons[moduleId] = true);

    try {
      final api = context.read<ApiService>();
      final data = await api.get(ApiConfig.moduleLessons(moduleId));
      setState(() {
        _moduleLessons[moduleId] = data['lessons'] ?? data ?? [];
      });
    } catch (_) {
      setState(() => _moduleLessons[moduleId] = []);
    } finally {
      setState(() => _loadingLessons[moduleId] = false);
    }
  }

  void _toggleModule(int moduleId) {
    setState(() {
      if (_expandedModuleId == moduleId) {
        _expandedModuleId = null;
      } else {
        _expandedModuleId = moduleId;
        _loadLessons(moduleId);
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.scaffoldBg,
      appBar: AppBar(
        title: Text('Courses', style: AppTextStyles.titleMedium),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: _isLoading
          ? const LoadingIndicator(message: 'Loading courses...')
          : _error != null
              ? _buildError()
              : _modules.isEmpty
                  ? _buildEmpty()
                  : RefreshIndicator(
                      onRefresh: _loadModules,
                      color: AppColors.primary,
                      child: ListView.separated(
                        padding: const EdgeInsets.all(16),
                        itemCount: _modules.length,
                        separatorBuilder: (_, _) => const SizedBox(height: 10),
                        itemBuilder: (context, index) =>
                            _buildModuleCard(_modules[index]),
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
            onPressed: _loadModules,
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
          Icon(Icons.menu_book_outlined, size: 64, color: AppColors.gray300),
          const SizedBox(height: 16),
          Text('No courses available',
              style: AppTextStyles.headlineMedium
                  .copyWith(color: AppColors.gray500)),
        ],
      ),
    );
  }

  Widget _buildModuleCard(Map<String, dynamic> module) {
    final moduleId = module['id'];
    final isExpanded = _expandedModuleId == moduleId;
    final lessons = _moduleLessons[moduleId] ?? [];
    final isLoadingLessons = _loadingLessons[moduleId] ?? false;

    return Container(
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: isExpanded ? AppColors.primary.withValues(alpha: 0.3) : AppColors.gray200,
        ),
        boxShadow: isExpanded ? AppShadows.small : [],
      ),
      child: Column(
        children: [
          // Module Header
          InkWell(
            onTap: () => _toggleModule(moduleId),
            borderRadius: BorderRadius.circular(12),
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(10),
                    decoration: BoxDecoration(
                      color: AppColors.purpleTint,
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: const Icon(Icons.menu_book_outlined,
                        size: 24, color: AppColors.purple),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          module['name'] ?? '',
                          style: AppTextStyles.labelLarge,
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                        if (module['code'] != null) ...[
                          const SizedBox(height: 2),
                          Text(
                            module['code'],
                            style: AppTextStyles.caption.copyWith(
                              color: AppColors.purple,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ],
                      ],
                    ),
                  ),
                  AnimatedRotation(
                    turns: isExpanded ? 0.25 : 0,
                    duration: const Duration(milliseconds: 200),
                    child: const Icon(Icons.chevron_right,
                        color: AppColors.gray400),
                  ),
                ],
              ),
            ),
          ),

          // Lessons (expanded)
          if (isExpanded)
            AnimatedSize(
              duration: const Duration(milliseconds: 200),
              child: Column(
                children: [
                  const Divider(height: 1),
                  if (isLoadingLessons)
                    const Padding(
                      padding: EdgeInsets.all(24),
                      child: CircularProgressIndicator(
                          strokeWidth: 2, color: AppColors.primary),
                    )
                  else if (lessons.isEmpty)
                    Padding(
                      padding: const EdgeInsets.all(20),
                      child: Text('No lessons uploaded yet',
                          style: AppTextStyles.bodySmall),
                    )
                  else
                    ...lessons.map((lesson) => _buildLessonItem(lesson)),
                ],
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildLessonItem(Map<String, dynamic> lesson) {
    return InkWell(
      onTap: () {
        // Download lesson
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Downloading ${lesson['title'] ?? 'file'}...'),
            backgroundColor: AppColors.primary,
          ),
        );
      },
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: AppColors.orangeTint,
                borderRadius: BorderRadius.circular(8),
              ),
              child: const Icon(Icons.picture_as_pdf_outlined,
                  size: 20, color: AppColors.orange),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    lesson['title'] ?? lesson['original_name'] ?? 'Lesson',
                    style: AppTextStyles.bodyMedium.copyWith(
                      fontWeight: FontWeight.w500,
                      color: AppColors.gray800,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                  if (lesson['created_at'] != null)
                    Text(
                      lesson['created_at'],
                      style: AppTextStyles.caption,
                    ),
                ],
              ),
            ),
            const Icon(Icons.download_outlined,
                size: 22, color: AppColors.primary),
          ],
        ),
      ),
    );
  }
}
