import 'package:shared_preferences/shared_preferences.dart';

class StorageService {
  static const String _tokenKey = 'auth_token';
  static const String _userKey = 'user_data';
  static const String _rememberKey = 'remember_me';

  late SharedPreferences _prefs;

  Future<void> init() async {
    _prefs = await SharedPreferences.getInstance();
  }

  // Token
  Future<void> saveToken(String token) async {
    await _prefs.setString(_tokenKey, token);
  }

  String? getToken() {
    return _prefs.getString(_tokenKey);
  }

  Future<void> removeToken() async {
    await _prefs.remove(_tokenKey);
  }

  // User data (JSON string)
  Future<void> saveUser(String userJson) async {
    await _prefs.setString(_userKey, userJson);
  }

  String? getUser() {
    return _prefs.getString(_userKey);
  }

  Future<void> removeUser() async {
    await _prefs.remove(_userKey);
  }

  // Remember me
  Future<void> setRememberMe(bool value) async {
    await _prefs.setBool(_rememberKey, value);
  }

  bool getRememberMe() {
    return _prefs.getBool(_rememberKey) ?? false;
  }

  // Clear all
  Future<void> clearAll() async {
    await _prefs.remove(_tokenKey);
    await _prefs.remove(_userKey);
  }
}
