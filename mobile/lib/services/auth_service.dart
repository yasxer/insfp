import 'dart:convert';
import 'package:flutter/material.dart';
import '../config/api_config.dart';
import 'api_service.dart';
import 'storage_service.dart';

class AuthService extends ChangeNotifier {
  final ApiService _api;
  final StorageService _storage;

  Map<String, dynamic>? _user;
  bool _isLoading = false;
  String? _error;
  bool _isLoggedIn = false;

  AuthService(this._api, this._storage) {
    _loadFromStorage();
  }

  // Getters
  Map<String, dynamic>? get user => _user;
  bool get isLoading => _isLoading;
  String? get error => _error;
  bool get isLoggedIn => _isLoggedIn;
  String get userName => _user?['full_name'] ?? _user?['name'] ?? 'Student';
  String get userEmail => _user?['email'] ?? '';
  String get userRole => _user?['role'] ?? '';
  String get registrationNumber => _user?['registration_number'] ?? '';
  Map<String, dynamic>? get specialty => _user?['specialty'];
  int? get currentSemester => _user?['current_semester'];

  void _loadFromStorage() {
    final token = _storage.getToken();
    final userJson = _storage.getUser();
    if (token != null && userJson != null) {
      _user = jsonDecode(userJson);
      _isLoggedIn = true;
      notifyListeners();
    }
  }

  Future<bool> login(String registrationNumber, String password,
      {bool rememberMe = false}) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _api.post(ApiConfig.login, data: {
        'registration_number': registrationNumber,
        'password': password,
      });

      final token = response['token'];
      final userData = response['user'];

      if (token != null && userData != null) {
        await _storage.saveToken(token);
        await _storage.saveUser(jsonEncode(userData));
        await _storage.setRememberMe(rememberMe);

        _user = userData;
        _isLoggedIn = true;
        _isLoading = false;
        notifyListeners();
        return true;
      } else {
        _error = 'Invalid response from server.';
        _isLoading = false;
        notifyListeners();
        return false;
      }
    } catch (e) {
      _error = e.toString();
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<void> logout() async {
    try {
      await _api.post(ApiConfig.logout);
    } catch (_) {
      // Ignore errors on logout
    }
    await _storage.clearAll();
    _user = null;
    _isLoggedIn = false;
    _error = null;
    notifyListeners();
  }

  Future<void> refreshUser() async {
    try {
      final response = await _api.get(ApiConfig.me);
      _user = response['user'] ?? response;
      await _storage.saveUser(jsonEncode(_user));
      notifyListeners();
    } catch (_) {
      // Silently fail
    }
  }

  void updateUser(Map<String, dynamic> userData) {
    _user = {...?_user, ...userData};
    _storage.saveUser(jsonEncode(_user));
    notifyListeners();
  }

  void clearError() {
    _error = null;
    notifyListeners();
  }
}
