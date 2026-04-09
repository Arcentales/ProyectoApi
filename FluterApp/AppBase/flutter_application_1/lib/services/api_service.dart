// lib/services/api_service.dart
// ============================================================
//  BaseApp - Servicio API
//  Cambia BASE_URL según donde corras la app:
//    Emulador Android  → http://10.0.2.2/SitioWeb/api_flutter.php
//    Dispositivo físico → http://192.168.X.X/SitioWeb/api_flutter.php
//    (usa `ipconfig` en Windows para ver tu IP local)
// ============================================================

import 'dart:convert';
import 'package:http/http.dart' as http;

class ApiService {
  // ── CAMBIA ESTA URL ────────────────────────────────────────
  static const String BASE_URL =
      'http://10.0.2.2/SitioWeb/api_flutter.php';
  // Si usas dispositivo físico, reemplaza por tu IP local:
  // static const String BASE_URL = 'http://192.168.1.X/SitioWeb/api_flutter.php';

  static const Duration _timeout = Duration(seconds: 15);
  static String? _token;

  static void setToken(String token) => _token = token;

  static Map<String, String> get _headers => {
        'Content-Type': 'application/json',
        if (_token != null) 'Authorization': 'Bearer $_token',
      };

  // ── GET ──────────────────────────────────────────────────
  static Future<Map<String, dynamic>> get(
    String action, {
    Map<String, String>? params,
  }) async {
    final uri = Uri.parse(BASE_URL).replace(queryParameters: {
      'action': action,
      ...?params,
    });
    final res = await http.get(uri, headers: _headers).timeout(_timeout);
    return _parse(res);
  }

  // ── POST ─────────────────────────────────────────────────
  static Future<Map<String, dynamic>> post(
    String action,
    Map<String, dynamic> body,
  ) async {
    final uri = Uri.parse('$BASE_URL?action=$action');
    final res = await http
        .post(uri, headers: _headers, body: jsonEncode(body))
        .timeout(_timeout);
    return _parse(res);
  }

  // ── DELETE ───────────────────────────────────────────────
  static Future<Map<String, dynamic>> delete(
    String action,
    Map<String, dynamic> body,
  ) async {
    final uri = Uri.parse('$BASE_URL?action=$action');
    final res = await http
        .delete(uri, headers: _headers, body: jsonEncode(body))
        .timeout(_timeout);
    return _parse(res);
  }

  // ── PARSER ───────────────────────────────────────────────
  static Map<String, dynamic> _parse(http.Response res) {
    final body = jsonDecode(res.body) as Map<String, dynamic>;
    if (body['success'] == false) {
      throw ApiException(body['error'] ?? 'Error desconocido', res.statusCode);
    }
    return body;
  }
}

// ── EXCEPCIÓN PERSONALIZADA ───────────────────────────────
class ApiException implements Exception {
  final String message;
  final int? statusCode;
  ApiException(this.message, [this.statusCode]);

  @override
  String toString() => message;
}
