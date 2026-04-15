import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../config/theme.dart';
import '../config/api_config.dart';
import '../services/api_service.dart';
import '../widgets/loading_indicator.dart';

class DocumentsScreen extends StatefulWidget {
  const DocumentsScreen({super.key});

  @override
  State<DocumentsScreen> createState() => _DocumentsScreenState();
}

class _DocumentsScreenState extends State<DocumentsScreen> {
  bool _isLoading = true;
  String? _error;
  List<dynamic> _documents = [];

  @override
  void initState() {
    super.initState();
    _loadDocuments();
  }

  Future<void> _loadDocuments() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final api = context.read<ApiService>();
      final data = await api.get(ApiConfig.documents);
      setState(() {
        _documents = data['data'] ?? data ?? [];
      });
    } catch (e) {
      setState(() => _error = e.toString());
    } finally {
      setState(() => _isLoading = false);
    }
  }

  IconData _getDocIcon(String? type) {
    final ext = (type ?? '').toLowerCase();
    if (ext.contains('pdf')) return Icons.picture_as_pdf_outlined;
    if (ext.contains('doc') || ext.contains('word')) {
      return Icons.article_outlined;
    }
    if (ext.contains('xls') || ext.contains('sheet')) {
      return Icons.table_chart_outlined;
    }
    if (ext.contains('ppt') || ext.contains('presentation')) {
      return Icons.slideshow_outlined;
    }
    if (ext.contains('image') || ext.contains('jpg') || ext.contains('png')) {
      return Icons.image_outlined;
    }
    return Icons.description_outlined;
  }

  Color _getDocColor(String? type) {
    final ext = (type ?? '').toLowerCase();
    if (ext.contains('pdf')) return AppColors.red;
    if (ext.contains('doc') || ext.contains('word')) return AppColors.primary;
    if (ext.contains('xls') || ext.contains('sheet')) return AppColors.green;
    if (ext.contains('ppt')) return AppColors.orange;
    return AppColors.gray600;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.scaffoldBg,
      appBar: AppBar(
        title: Text('Documents', style: AppTextStyles.titleMedium),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: _isLoading
          ? const LoadingIndicator(message: 'Loading documents...')
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
                        onPressed: _loadDocuments,
                        icon: const Icon(Icons.refresh, size: 18),
                        label: const Text('Retry'),
                      ),
                    ],
                  ),
                )
              : _documents.isEmpty
                  ? Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(Icons.folder_open_outlined,
                              size: 64, color: AppColors.gray300),
                          const SizedBox(height: 16),
                          Text('No documents available',
                              style: AppTextStyles.headlineMedium
                                  .copyWith(color: AppColors.gray500)),
                        ],
                      ),
                    )
                  : RefreshIndicator(
                      onRefresh: _loadDocuments,
                      color: AppColors.primary,
                      child: ListView.separated(
                        padding: const EdgeInsets.all(16),
                        itemCount: _documents.length,
                        separatorBuilder: (_, _) => const SizedBox(height: 8),
                        itemBuilder: (context, index) {
                          final doc = _documents[index];
                          final docType =
                              doc['mime_type'] ?? doc['type'] ?? '';
                          return Container(
                            decoration: BoxDecoration(
                              color: AppColors.white,
                              borderRadius: BorderRadius.circular(12),
                              border: Border.all(color: AppColors.gray200),
                            ),
                            child: ListTile(
                              contentPadding: const EdgeInsets.symmetric(
                                  horizontal: 16, vertical: 8),
                              leading: Container(
                                padding: const EdgeInsets.all(10),
                                decoration: BoxDecoration(
                                  color: _getDocColor(docType)
                                      .withValues(alpha: 0.1),
                                  borderRadius: BorderRadius.circular(10),
                                ),
                                child: Icon(
                                  _getDocIcon(docType),
                                  color: _getDocColor(docType),
                                  size: 24,
                                ),
                              ),
                              title: Text(
                                doc['title'] ??
                                    doc['original_name'] ??
                                    'Document',
                                style: AppTextStyles.labelLarge,
                                maxLines: 2,
                                overflow: TextOverflow.ellipsis,
                              ),
                              subtitle: Text(
                                doc['created_at'] ?? '',
                                style: AppTextStyles.caption,
                              ),
                              trailing: IconButton(
                                icon: const Icon(Icons.download_outlined,
                                    color: AppColors.primary),
                                onPressed: () {
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    SnackBar(
                                      content: Text(
                                          'Downloading ${doc['title'] ?? 'document'}...'),
                                      backgroundColor: AppColors.primary,
                                    ),
                                  );
                                },
                              ),
                            ),
                          );
                        },
                      ),
                    ),
    );
  }
}
