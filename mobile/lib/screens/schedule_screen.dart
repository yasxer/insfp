import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../config/theme.dart';
import '../config/api_config.dart';
import '../services/api_service.dart';
import '../widgets/loading_indicator.dart';

class ScheduleScreen extends StatefulWidget {
  const ScheduleScreen({super.key});

  @override
  State<ScheduleScreen> createState() => _ScheduleScreenState();
}

class _ScheduleScreenState extends State<ScheduleScreen> {
  bool _isLoading = true;
  String? _error;
  List<dynamic> _scheduleData = [];

  // Algerian week: Saturday to Thursday
  final List<String> _days = [
    'Saturday',
    'Sunday',
    'Monday',
    'Tuesday',
    'Wednesday',
    'Thursday'
  ];

  final List<String> _timeSlots = [
    '08:00 - 09:30',
    '09:30 - 11:00',
    '11:00 - 12:30',
    '13:00 - 14:30',
    '14:30 - 16:00',
    '16:00 - 17:30',
  ];

  final Map<String, String> _frenchToEnglish = {
    'lundi': 'Monday',
    'mardi': 'Tuesday',
    'mercredi': 'Wednesday',
    'jeudi': 'Thursday',
    'vendredi': 'Friday',
    'samedi': 'Saturday',
    'dimanche': 'Sunday',
  };

  int _selectedDayIndex = 0;

  @override
  void initState() {
    super.initState();
    // Default to today's index
    final today = DateTime.now().weekday;
    // Map: Mon=1..Sun=7 → our array index (Sat=0..Thu=5)
    final dayMap = {6: 0, 7: 1, 1: 2, 2: 3, 3: 4, 4: 5, 5: 0};
    _selectedDayIndex = dayMap[today] ?? 0;
    _loadSchedule();
  }

  Future<void> _loadSchedule() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final api = context.read<ApiService>();
      final data = await api.get(ApiConfig.schedule);

      List<dynamic> allClasses = [];
      if (data is List) {
        for (var dayData in data) {
          if (dayData['classes'] != null && dayData['classes'] is List) {
            for (var classItem in dayData['classes']) {
              classItem['day_name'] = dayData['day_name'];
              allClasses.add(classItem);
            }
          }
        }
      }
      setState(() {
        _scheduleData = allClasses;
      });
    } catch (e) {
      setState(() => _error = e.toString());
    } finally {
      setState(() => _isLoading = false);
    }
  }

  String _convertDay(String? day) {
    return _frenchToEnglish[day?.toLowerCase()] ?? day ?? '';
  }

  Map<String, dynamic>? _getClassForSlot(String day, String timeSlot) {
    final slotStart = timeSlot.split(' - ')[0];
    return _scheduleData.cast<Map<String, dynamic>>().where((item) {
      final classDay = _convertDay(item['day_name']?.toString());
      var classStart = item['start_time']?.toString() ?? '';
      if (classStart.length > 5) classStart = classStart.substring(0, 5);
      return classDay == day && classStart == slotStart;
    }).firstOrNull;
  }

  final List<Color> _slotColors = [
    AppColors.primary,
    AppColors.purple,
    AppColors.green,
    AppColors.orange,
    const Color(0xFF0891B2),
    AppColors.pink,
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.scaffoldBg,
      appBar: AppBar(
        title: Text('Schedule', style: AppTextStyles.titleMedium),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: _isLoading
          ? const LoadingIndicator(message: 'Loading schedule...')
          : _error != null
              ? _buildError()
              : RefreshIndicator(
                  onRefresh: _loadSchedule,
                  color: AppColors.primary,
                  child: Column(
                    children: [
                      // Day selector
                      _buildDaySelector(),
                      // Time slots
                      Expanded(child: _buildTimeSlots()),
                    ],
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
            onPressed: _loadSchedule,
            icon: const Icon(Icons.refresh, size: 18),
            label: const Text('Retry'),
          ),
        ],
      ),
    );
  }

  Widget _buildDaySelector() {
    return Container(
      height: 64,
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 12),
        itemCount: _days.length,
        itemBuilder: (context, index) {
          final isSelected = _selectedDayIndex == index;
          return Padding(
            padding: const EdgeInsets.symmetric(horizontal: 4),
            child: GestureDetector(
              onTap: () => setState(() => _selectedDayIndex = index),
              child: AnimatedContainer(
                duration: const Duration(milliseconds: 200),
                padding:
                    const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
                decoration: BoxDecoration(
                  color: isSelected ? AppColors.primary : AppColors.white,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(
                    color: isSelected ? AppColors.primary : AppColors.gray200,
                  ),
                  boxShadow: isSelected ? [
                    BoxShadow(
                      color: AppColors.primary.withValues(alpha: 0.3),
                      blurRadius: 8,
                      offset: const Offset(0, 2),
                    ),
                  ] : [],
                ),
                alignment: Alignment.center,
                child: Text(
                  _days[index].substring(0, 3),
                  style: AppTextStyles.labelLarge.copyWith(
                    color: isSelected ? AppColors.white : AppColors.gray600,
                    fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
                  ),
                ),
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildTimeSlots() {
    final selectedDay = _days[_selectedDayIndex];
    final dayClasses = _scheduleData
        .where((c) => _convertDay(c['day_name']?.toString()) == selectedDay)
        .toList();

    if (dayClasses.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.event_available_outlined,
                size: 64, color: AppColors.gray300),
            const SizedBox(height: 16),
            Text('No classes on $selectedDay',
                style: AppTextStyles.headlineMedium
                    .copyWith(color: AppColors.gray500)),
            const SizedBox(height: 8),
            Text('Enjoy your free time!', style: AppTextStyles.bodyMedium),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _timeSlots.length,
      itemBuilder: (context, index) {
        final slot = _timeSlots[index];
        final classData = _getClassForSlot(selectedDay, slot);

        if (classData == null) {
          return Padding(
            padding: const EdgeInsets.only(bottom: 8),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                SizedBox(
                  width: 60,
                  child: Text(
                    slot.split(' - ')[0],
                    style: AppTextStyles.caption.copyWith(fontSize: 11),
                  ),
                ),
                Expanded(
                  child: Container(
                    height: 60,
                    decoration: BoxDecoration(
                      color: AppColors.gray50,
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(
                        color: AppColors.gray200,
                        style: BorderStyle.solid,
                      ),
                    ),
                    alignment: Alignment.center,
                    child: Text('Free', style: AppTextStyles.caption),
                  ),
                ),
              ],
            ),
          );
        }

        final color = _slotColors[index % _slotColors.length];
        final module = classData['module'];

        return Padding(
          padding: const EdgeInsets.only(bottom: 10),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              SizedBox(
                width: 60,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      slot.split(' - ')[0],
                      style: AppTextStyles.labelLarge.copyWith(fontSize: 12),
                    ),
                    Text(
                      slot.split(' - ')[1],
                      style: AppTextStyles.caption.copyWith(fontSize: 10),
                    ),
                  ],
                ),
              ),
              Expanded(
                child: Container(
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: color.withValues(alpha: 0.08),
                    borderRadius: BorderRadius.circular(12),
                    border: Border(
                      left: BorderSide(color: color, width: 4),
                    ),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        module?['name'] ?? 'Class',
                        style: AppTextStyles.labelLarge.copyWith(
                          color: color,
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 6),
                      Row(
                        children: [
                          Icon(Icons.person_outline,
                              size: 14, color: AppColors.gray500),
                          const SizedBox(width: 4),
                          Expanded(
                            child: Text(
                              classData['teacher']?['full_name'] ?? 'TBA',
                              style: AppTextStyles.caption
                                  .copyWith(color: AppColors.gray600),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                        ],
                      ),
                      if (classData['room'] != null) ...[
                        const SizedBox(height: 4),
                        Row(
                          children: [
                            Icon(Icons.location_on_outlined,
                                size: 14, color: AppColors.gray500),
                            const SizedBox(width: 4),
                            Text(
                              'Room ${classData['room']}',
                              style: AppTextStyles.caption
                                  .copyWith(color: AppColors.gray600),
                            ),
                          ],
                        ),
                      ],
                    ],
                  ),
                ),
              ),
            ],
          ),
        );
      },
    );
  }
}
