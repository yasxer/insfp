import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

/// Design tokens shared with the web platform: institutional navy, teal and
/// gold, cool grays, IBM Plex Sans for text and Source Serif 4 for titles.
///
/// The historical names (primary, purple, green…) are kept so every screen
/// picks up the new palette; they now point to the institute's colors.
class AppColors {
  // Navy — main brand color
  static const Color navy900 = Color(0xFF0A2443);
  static const Color navy800 = Color(0xFF0C2B52);
  static const Color navy = Color(0xFF0F3460);
  static const Color navy500 = Color(0xFF2F5A92);
  static const Color navy100 = Color(0xFFE6EDF7);
  static const Color navy50 = Color(0xFFF2F6FB);

  // Teal — secondary accent
  static const Color teal = Color(0xFF0E7C7B);
  static const Color teal100 = Color(0xFFDDF1F0);

  // Gold — highlights
  static const Color gold = Color(0xFFC9971C);
  static const Color gold100 = Color(0xFFFBF3DC);

  // Legacy names, mapped onto the palette
  static const Color primary = navy;
  static const Color primaryLight = navy500;
  static const Color primaryDark = navy800;
  static const Color primaryHover = navy800;
  static const Color purple = teal;
  static const Color pink = teal;
  static const Color green = Color(0xFF15803D);
  static const Color orange = Color(0xFFB45309);
  static const Color yellow = gold;
  static const Color red = Color(0xFFB91C1C);

  // Cool grays
  static const Color black = Color(0xFF0F172A);
  static const Color gray800 = Color(0xFF1E293B);
  static const Color gray700 = Color(0xFF334155);
  static const Color gray600 = Color(0xFF475569);
  static const Color gray500 = Color(0xFF64748B);
  static const Color gray400 = Color(0xFF94A3B8);
  static const Color gray300 = Color(0xFFCBD5E1);
  static const Color gray200 = Color(0xFFE2E8F0);
  static const Color gray100 = Color(0xFFF1F5F9);
  static const Color gray50 = Color(0xFFF8FAFC);
  static const Color white = Color(0xFFFFFFFF);

  // Semantic
  static const Color success = green;
  static const Color error = red;
  static const Color warning = orange;
  static const Color info = navy;

  // Backgrounds
  static const Color scaffoldBg = Color(0xFFF5F7FA);
  static const Color cardBg = white;
  static const Color inputBg = white;
  static const Color inputBorder = gray300;

  // Tints behind icons and badges
  static const Color blueTint = navy100;
  static const Color greenTint = Color(0xFFE7F5EC);
  static const Color purpleTint = teal100;
  static const Color orangeTint = Color(0xFFFDF0E1);
  static const Color redTint = Color(0xFFFCEBEB);
  static const Color yellowTint = gold100;

  /// Header gradient used on the login and dashboard screens.
  static const LinearGradient headerGradient = LinearGradient(
    colors: [navy900, navy, Color(0xFF134A6E)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );
}

class AppTextStyles {
  static TextStyle _sans(double size, FontWeight weight, Color color, {double? height, double? spacing}) =>
      GoogleFonts.ibmPlexSans(fontSize: size, fontWeight: weight, color: color, height: height, letterSpacing: spacing);

  static TextStyle _serif(double size, FontWeight weight, Color color) =>
      GoogleFonts.sourceSerif4(fontSize: size, fontWeight: weight, color: color, height: 1.2);

  // Titles: serif, like the web platform
  static TextStyle get displayLarge => _serif(28, FontWeight.w700, AppColors.navy900);
  static TextStyle get displayMedium => _serif(24, FontWeight.w700, AppColors.navy900);
  static TextStyle get headlineLarge => _serif(21, FontWeight.w700, AppColors.navy900);
  static TextStyle get headlineMedium => _serif(18, FontWeight.w700, AppColors.navy900);

  // Text: IBM Plex Sans
  static TextStyle get titleMedium => _sans(16, FontWeight.w600, AppColors.black);
  static TextStyle get bodyLarge => _sans(16, FontWeight.w400, AppColors.gray700, height: 1.5);
  static TextStyle get bodyMedium => _sans(14, FontWeight.w400, AppColors.gray600, height: 1.5);
  static TextStyle get bodySmall => _sans(12, FontWeight.w400, AppColors.gray500);
  static TextStyle get labelLarge => _sans(14, FontWeight.w600, AppColors.black);
  static TextStyle get labelSmall => _sans(11, FontWeight.w600, AppColors.gray500, spacing: 0.6);
  static TextStyle get button => _sans(15, FontWeight.w600, AppColors.white);
  static TextStyle get caption => _sans(12, FontWeight.w500, AppColors.gray400);
}

class AppTheme {
  static ThemeData get lightTheme {
    final base = ThemeData(useMaterial3: true, brightness: Brightness.light);
    return base.copyWith(
      scaffoldBackgroundColor: AppColors.scaffoldBg,
      primaryColor: AppColors.primary,
      textTheme: GoogleFonts.ibmPlexSansTextTheme(base.textTheme).apply(
        bodyColor: AppColors.gray800,
        displayColor: AppColors.navy900,
      ),
      colorScheme: const ColorScheme.light(
        primary: AppColors.navy,
        onPrimary: AppColors.white,
        secondary: AppColors.teal,
        onSecondary: AppColors.white,
        tertiary: AppColors.gold,
        surface: AppColors.white,
        onSurface: AppColors.black,
        error: AppColors.red,
        onError: AppColors.white,
      ),
      appBarTheme: AppBarTheme(
        backgroundColor: AppColors.white,
        foregroundColor: AppColors.navy900,
        elevation: 0,
        scrolledUnderElevation: 0.5,
        surfaceTintColor: Colors.transparent,
        centerTitle: false,
        shape: const Border(bottom: BorderSide(color: AppColors.gray200)),
        titleTextStyle: GoogleFonts.sourceSerif4(
          fontSize: 20,
          fontWeight: FontWeight.w700,
          color: AppColors.navy900,
        ),
        iconTheme: const IconThemeData(color: AppColors.navy),
      ),
      cardTheme: CardThemeData(
        color: AppColors.cardBg,
        elevation: 0,
        surfaceTintColor: Colors.transparent,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(14),
          side: const BorderSide(color: AppColors.gray200),
        ),
        margin: EdgeInsets.zero,
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: AppColors.inputBg,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: const BorderSide(color: AppColors.gray300),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: const BorderSide(color: AppColors.gray300),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: const BorderSide(color: AppColors.teal, width: 1.6),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: const BorderSide(color: AppColors.red),
        ),
        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
        prefixIconColor: AppColors.gray400,
        hintStyle: GoogleFonts.ibmPlexSans(fontSize: 14, color: AppColors.gray400),
        labelStyle: GoogleFonts.ibmPlexSans(
          fontSize: 14,
          fontWeight: FontWeight.w500,
          color: AppColors.gray600,
        ),
        floatingLabelStyle: GoogleFonts.ibmPlexSans(
          fontSize: 14,
          fontWeight: FontWeight.w600,
          color: AppColors.teal,
        ),
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: AppColors.navy,
          foregroundColor: AppColors.white,
          elevation: 0,
          padding: const EdgeInsets.symmetric(horizontal: 22, vertical: 14),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
          textStyle: GoogleFonts.ibmPlexSans(fontSize: 15, fontWeight: FontWeight.w600),
        ),
      ),
      outlinedButtonTheme: OutlinedButtonThemeData(
        style: OutlinedButton.styleFrom(
          foregroundColor: AppColors.navy,
          side: const BorderSide(color: AppColors.gray300),
          padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 12),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
          textStyle: GoogleFonts.ibmPlexSans(fontSize: 14, fontWeight: FontWeight.w600),
        ),
      ),
      textButtonTheme: TextButtonThemeData(
        style: TextButton.styleFrom(
          foregroundColor: AppColors.teal,
          textStyle: GoogleFonts.ibmPlexSans(fontSize: 14, fontWeight: FontWeight.w600),
        ),
      ),
      tabBarTheme: TabBarThemeData(
        labelColor: AppColors.navy,
        unselectedLabelColor: AppColors.gray500,
        indicatorColor: AppColors.gold,
        indicatorSize: TabBarIndicatorSize.label,
        dividerColor: AppColors.gray200,
        labelStyle: GoogleFonts.ibmPlexSans(fontSize: 14, fontWeight: FontWeight.w600),
        unselectedLabelStyle: GoogleFonts.ibmPlexSans(fontSize: 14, fontWeight: FontWeight.w500),
      ),
      bottomNavigationBarTheme: BottomNavigationBarThemeData(
        backgroundColor: AppColors.white,
        selectedItemColor: AppColors.navy,
        unselectedItemColor: AppColors.gray400,
        type: BottomNavigationBarType.fixed,
        elevation: 8,
        selectedLabelStyle: GoogleFonts.ibmPlexSans(fontSize: 12, fontWeight: FontWeight.w600),
        unselectedLabelStyle: GoogleFonts.ibmPlexSans(fontSize: 12, fontWeight: FontWeight.w400),
      ),
      progressIndicatorTheme: const ProgressIndicatorThemeData(color: AppColors.teal),
      checkboxTheme: CheckboxThemeData(
        fillColor: WidgetStateProperty.resolveWith(
          (states) => states.contains(WidgetState.selected) ? AppColors.teal : null,
        ),
      ),
      dividerTheme: const DividerThemeData(color: AppColors.gray200, thickness: 1, space: 0),
      chipTheme: ChipThemeData(
        backgroundColor: AppColors.navy50,
        selectedColor: AppColors.navy,
        labelStyle: GoogleFonts.ibmPlexSans(fontSize: 12, fontWeight: FontWeight.w500),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(999)),
        side: const BorderSide(color: AppColors.gray200),
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      ),
      snackBarTheme: SnackBarThemeData(
        backgroundColor: AppColors.navy900,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        contentTextStyle: GoogleFonts.ibmPlexSans(fontSize: 14, color: AppColors.white),
      ),
    );
  }
}

/// Soft, low shadows (the web cards use a 1px border plus a light shadow).
class AppShadows {
  static List<BoxShadow> get small => [
        BoxShadow(
          offset: const Offset(0, 1),
          blurRadius: 3,
          color: AppColors.navy900.withValues(alpha: 0.06),
        ),
      ];

  static List<BoxShadow> get medium => [
        BoxShadow(
          offset: const Offset(0, 8),
          blurRadius: 24,
          color: AppColors.navy900.withValues(alpha: 0.08),
        ),
        BoxShadow(
          offset: const Offset(0, 1),
          blurRadius: 3,
          color: AppColors.navy900.withValues(alpha: 0.06),
        ),
      ];

  static List<BoxShadow> get large => [
        BoxShadow(
          offset: const Offset(0, 24),
          blurRadius: 48,
          color: AppColors.navy900.withValues(alpha: 0.16),
        ),
        BoxShadow(
          offset: const Offset(0, 2),
          blurRadius: 6,
          color: AppColors.navy900.withValues(alpha: 0.08),
        ),
      ];
}
