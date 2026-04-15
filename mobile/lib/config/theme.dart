import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

/// Design tokens matching the Webflow-inspired web frontend
class AppColors {
  // Primary
  static const Color primary = Color(0xFF146EF5);
  static const Color primaryLight = Color(0xFF3B89FF);
  static const Color primaryDark = Color(0xFF006ACC);
  static const Color primaryHover = Color(0xFF0055D4);

  // Secondary accents
  static const Color purple = Color(0xFF7A3DFF);
  static const Color pink = Color(0xFFED52CB);
  static const Color green = Color(0xFF00D722);
  static const Color orange = Color(0xFFFF6B00);
  static const Color yellow = Color(0xFFFFAE13);
  static const Color red = Color(0xFFEE1D36);

  // Neutrals
  static const Color black = Color(0xFF080808);
  static const Color gray800 = Color(0xFF222222);
  static const Color gray700 = Color(0xFF363636);
  static const Color gray600 = Color(0xFF5A5A5A);
  static const Color gray500 = Color(0xFF898989);
  static const Color gray400 = Color(0xFFABABAB);
  static const Color gray300 = Color(0xFFD8D8D8);
  static const Color gray200 = Color(0xFFE5E5E5);
  static const Color gray100 = Color(0xFFF3F3F3);
  static const Color gray50 = Color(0xFFF9F9F9);
  static const Color white = Color(0xFFFFFFFF);

  // Semantic
  static const Color success = green;
  static const Color error = red;
  static const Color warning = orange;
  static const Color info = primary;

  // Backgrounds
  static const Color scaffoldBg = gray50;
  static const Color cardBg = white;
  static const Color inputBg = white;
  static const Color inputBorder = gray300;

  // Stat card background tints
  static const Color blueTint = Color(0xFFEFF6FF);
  static const Color greenTint = Color(0xFFECFDF5);
  static const Color purpleTint = Color(0xFFF3E8FF);
  static const Color orangeTint = Color(0xFFFFF7ED);
  static const Color redTint = Color(0xFFFEF2F2);
  static const Color yellowTint = Color(0xFFFFFBEB);
}

class AppTextStyles {
  static TextStyle get displayLarge => GoogleFonts.inter(
        fontSize: 28,
        fontWeight: FontWeight.w700,
        color: AppColors.black,
        letterSpacing: -0.8,
      );

  static TextStyle get displayMedium => GoogleFonts.inter(
        fontSize: 24,
        fontWeight: FontWeight.w600,
        color: AppColors.black,
      );

  static TextStyle get headlineLarge => GoogleFonts.inter(
        fontSize: 20,
        fontWeight: FontWeight.w600,
        color: AppColors.black,
      );

  static TextStyle get headlineMedium => GoogleFonts.inter(
        fontSize: 18,
        fontWeight: FontWeight.w600,
        color: AppColors.black,
      );

  static TextStyle get titleMedium => GoogleFonts.inter(
        fontSize: 16,
        fontWeight: FontWeight.w600,
        color: AppColors.black,
      );

  static TextStyle get bodyLarge => GoogleFonts.inter(
        fontSize: 16,
        fontWeight: FontWeight.w400,
        color: AppColors.gray700,
        height: 1.5,
      );

  static TextStyle get bodyMedium => GoogleFonts.inter(
        fontSize: 14,
        fontWeight: FontWeight.w400,
        color: AppColors.gray600,
        height: 1.5,
      );

  static TextStyle get bodySmall => GoogleFonts.inter(
        fontSize: 12,
        fontWeight: FontWeight.w400,
        color: AppColors.gray500,
      );

  static TextStyle get labelLarge => GoogleFonts.inter(
        fontSize: 14,
        fontWeight: FontWeight.w600,
        color: AppColors.black,
      );

  static TextStyle get labelSmall => GoogleFonts.inter(
        fontSize: 11,
        fontWeight: FontWeight.w500,
        color: AppColors.gray500,
        letterSpacing: 0.5,
      );

  static TextStyle get button => GoogleFonts.inter(
        fontSize: 16,
        fontWeight: FontWeight.w600,
        color: AppColors.white,
      );

  static TextStyle get caption => GoogleFonts.inter(
        fontSize: 12,
        fontWeight: FontWeight.w500,
        color: AppColors.gray400,
      );
}

class AppTheme {
  static ThemeData get lightTheme {
    return ThemeData(
      useMaterial3: true,
      brightness: Brightness.light,
      scaffoldBackgroundColor: AppColors.scaffoldBg,
      primaryColor: AppColors.primary,
      colorScheme: const ColorScheme.light(
        primary: AppColors.primary,
        onPrimary: AppColors.white,
        secondary: AppColors.purple,
        onSecondary: AppColors.white,
        surface: AppColors.white,
        onSurface: AppColors.black,
        error: AppColors.red,
        onError: AppColors.white,
      ),
      appBarTheme: AppBarTheme(
        backgroundColor: AppColors.white,
        foregroundColor: AppColors.black,
        elevation: 0,
        scrolledUnderElevation: 1,
        titleTextStyle: GoogleFonts.inter(
          fontSize: 18,
          fontWeight: FontWeight.w600,
          color: AppColors.black,
        ),
        iconTheme: const IconThemeData(color: AppColors.black),
      ),
      cardTheme: CardThemeData(
        color: AppColors.cardBg,
        elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(12),
          side: const BorderSide(color: AppColors.gray200, width: 1),
        ),
        margin: EdgeInsets.zero,
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: AppColors.inputBg,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(8),
          borderSide: const BorderSide(color: AppColors.gray300),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(8),
          borderSide: const BorderSide(color: AppColors.gray300),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(8),
          borderSide: const BorderSide(color: AppColors.primary, width: 2),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(8),
          borderSide: const BorderSide(color: AppColors.red),
        ),
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        hintStyle: GoogleFonts.inter(
          fontSize: 14,
          color: AppColors.gray400,
        ),
        labelStyle: GoogleFonts.inter(
          fontSize: 14,
          fontWeight: FontWeight.w500,
          color: AppColors.gray600,
        ),
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: AppColors.primary,
          foregroundColor: AppColors.white,
          elevation: 0,
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(8),
          ),
          textStyle: GoogleFonts.inter(
            fontSize: 16,
            fontWeight: FontWeight.w600,
          ),
        ),
      ),
      textButtonTheme: TextButtonThemeData(
        style: TextButton.styleFrom(
          foregroundColor: AppColors.primary,
          textStyle: GoogleFonts.inter(
            fontSize: 14,
            fontWeight: FontWeight.w600,
          ),
        ),
      ),
      bottomNavigationBarTheme: BottomNavigationBarThemeData(
        backgroundColor: AppColors.white,
        selectedItemColor: AppColors.primary,
        unselectedItemColor: AppColors.gray400,
        type: BottomNavigationBarType.fixed,
        elevation: 8,
        selectedLabelStyle: GoogleFonts.inter(
          fontSize: 12,
          fontWeight: FontWeight.w600,
        ),
        unselectedLabelStyle: GoogleFonts.inter(
          fontSize: 12,
          fontWeight: FontWeight.w400,
        ),
      ),
      dividerTheme: const DividerThemeData(
        color: AppColors.gray200,
        thickness: 1,
        space: 0,
      ),
      chipTheme: ChipThemeData(
        backgroundColor: AppColors.blueTint,
        labelStyle: GoogleFonts.inter(
          fontSize: 12,
          fontWeight: FontWeight.w500,
        ),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(6),
        ),
        side: BorderSide.none,
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      ),
    );
  }
}

class AppShadows {
  static List<BoxShadow> get small => [
        BoxShadow(
          offset: const Offset(0, 3),
          blurRadius: 7,
          color: Colors.black.withValues(alpha: 0.09),
        ),
      ];

  static List<BoxShadow> get medium => [
        BoxShadow(
          offset: const Offset(0, 13),
          blurRadius: 13,
          color: Colors.black.withValues(alpha: 0.08),
        ),
        BoxShadow(
          offset: const Offset(0, 3),
          blurRadius: 7,
          color: Colors.black.withValues(alpha: 0.09),
        ),
      ];

  static List<BoxShadow> get large => [
        BoxShadow(
          offset: const Offset(0, 54),
          blurRadius: 22,
          color: Colors.black.withValues(alpha: 0.01),
        ),
        BoxShadow(
          offset: const Offset(0, 30),
          blurRadius: 18,
          color: Colors.black.withValues(alpha: 0.04),
        ),
        BoxShadow(
          offset: const Offset(0, 13),
          blurRadius: 13,
          color: Colors.black.withValues(alpha: 0.08),
        ),
        BoxShadow(
          offset: const Offset(0, 3),
          blurRadius: 7,
          color: Colors.black.withValues(alpha: 0.09),
        ),
      ];
}
