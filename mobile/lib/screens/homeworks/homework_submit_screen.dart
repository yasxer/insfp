import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:file_picker/file_picker.dart';
import 'package:dio/dio.dart';
import '../../config/theme.dart';
import '../../config/api_config.dart';
import '../../services/api_service.dart';

class HomeworkSubmitScreen extends StatefulWidget {
  const HomeworkSubmitScreen({super.key});

  @override
  State<HomeworkSubmitScreen> createState() => _HomeworkSubmitScreenState();
}

class _HomeworkSubmitScreenState extends State<HomeworkSubmitScreen> {
  PlatformFile? _selectedFile;
  bool _isSubmitting = false;
  String? _error;
  String? _successMessage;

  Future<void> _pickFile() async {
    try {
      final result = await FilePicker.platform.pickFiles(
        type: FileType.custom,
        allowedExtensions: ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'rar'],
      );
      if (result != null && result.files.isNotEmpty) {
        setState(() {
          _selectedFile = result.files.first;
          _error = null;
        });
      }
    } catch (e) {
      setState(() => _error = 'Failed to pick file');
    }
  }

  Future<void> _submit() async {
    if (_selectedFile == null) {
      setState(() => _error = 'Please select a file');
      return;
    }

    final hw = ModalRoute.of(context)?.settings.arguments as Map<String, dynamic>?;
    if (hw == null) return;

    setState(() {
      _isSubmitting = true;
      _error = null;
    });

    try {
      final api = context.read<ApiService>();
      final formData = FormData.fromMap({
        'file': await MultipartFile.fromFile(
          _selectedFile!.path!,
          filename: _selectedFile!.name,
        ),
      });

      await api.postFormData(
        ApiConfig.submitHomework(hw['id']),
        formData,
      );

      setState(() {
        _successMessage = 'Homework submitted successfully!';
        _isSubmitting = false;
      });

      // Go back after delay
      Future.delayed(const Duration(seconds: 2), () {
        if (mounted) Navigator.pop(context, true);
      });
    } catch (e) {
      setState(() {
        _error = e.toString();
        _isSubmitting = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final hw =
        ModalRoute.of(context)?.settings.arguments as Map<String, dynamic>? ??
            {};

    return Scaffold(
      backgroundColor: AppColors.scaffoldBg,
      appBar: AppBar(
        title: Text('Submit Homework', style: AppTextStyles.titleMedium),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Homework info
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: AppColors.white,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: AppColors.gray200),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(hw['title'] ?? 'Homework',
                      style: AppTextStyles.headlineMedium),
                  if (hw['description'] != null) ...[
                    const SizedBox(height: 8),
                    Text(hw['description'],
                        style: AppTextStyles.bodyMedium),
                  ],
                  if (hw['due_date'] != null) ...[
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        const Icon(Icons.schedule_outlined,
                            size: 16, color: AppColors.orange),
                        const SizedBox(width: 6),
                        Text('Due: ${hw['due_date']}',
                            style: AppTextStyles.bodySmall
                                .copyWith(color: AppColors.orange)),
                      ],
                    ),
                  ],
                ],
              ),
            ),
            const SizedBox(height: 24),

            // File picker
            GestureDetector(
              onTap: _pickFile,
              child: Container(
                padding: const EdgeInsets.all(32),
                decoration: BoxDecoration(
                  color: AppColors.white,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(
                    color: _selectedFile != null
                        ? AppColors.primary
                        : AppColors.gray300,
                    style: BorderStyle.solid,
                    width: _selectedFile != null ? 2 : 1,
                  ),
                ),
                child: Column(
                  children: [
                    Icon(
                      _selectedFile != null
                          ? Icons.check_circle_outline
                          : Icons.cloud_upload_outlined,
                      size: 48,
                      color: _selectedFile != null
                          ? AppColors.primary
                          : AppColors.gray400,
                    ),
                    const SizedBox(height: 12),
                    Text(
                      _selectedFile != null
                          ? _selectedFile!.name
                          : 'Tap to select file',
                      style: AppTextStyles.labelLarge.copyWith(
                        color: _selectedFile != null
                            ? AppColors.primary
                            : AppColors.gray500,
                      ),
                      textAlign: TextAlign.center,
                    ),
                    if (_selectedFile != null) ...[
                      const SizedBox(height: 4),
                      Text(
                        '${(_selectedFile!.size / 1024).toStringAsFixed(1)} KB',
                        style: AppTextStyles.caption,
                      ),
                    ] else ...[
                      const SizedBox(height: 4),
                      Text(
                        'PDF, DOC, DOCX, PPT, ZIP',
                        style: AppTextStyles.caption,
                      ),
                    ],
                  ],
                ),
              ),
            ),
            const SizedBox(height: 24),

            // Error / Success
            if (_error != null)
              Container(
                padding: const EdgeInsets.all(12),
                margin: const EdgeInsets.only(bottom: 16),
                decoration: BoxDecoration(
                  color: AppColors.redTint,
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Row(
                  children: [
                    const Icon(Icons.error_outline,
                        color: AppColors.red, size: 20),
                    const SizedBox(width: 8),
                    Expanded(
                        child: Text(_error!,
                            style: AppTextStyles.bodySmall
                                .copyWith(color: AppColors.red))),
                  ],
                ),
              ),

            if (_successMessage != null)
              Container(
                padding: const EdgeInsets.all(12),
                margin: const EdgeInsets.only(bottom: 16),
                decoration: BoxDecoration(
                  color: AppColors.greenTint,
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Row(
                  children: [
                    const Icon(Icons.check_circle_outline,
                        color: AppColors.green, size: 20),
                    const SizedBox(width: 8),
                    Expanded(
                        child: Text(_successMessage!,
                            style: AppTextStyles.bodySmall
                                .copyWith(color: AppColors.green))),
                  ],
                ),
              ),

            // Submit Button
            SizedBox(
              height: 52,
              child: ElevatedButton.icon(
                onPressed:
                    _isSubmitting || _successMessage != null ? null : _submit,
                icon: _isSubmitting
                    ? const SizedBox(
                        width: 20,
                        height: 20,
                        child: CircularProgressIndicator(
                            strokeWidth: 2, color: AppColors.white),
                      )
                    : const Icon(Icons.send_outlined, size: 20),
                label: Text(
                  _isSubmitting ? 'Submitting...' : 'Submit Homework',
                  style: AppTextStyles.button,
                ),
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.primary,
                  foregroundColor: AppColors.white,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
