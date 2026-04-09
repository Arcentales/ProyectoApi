// lib/main.dart
// ============================================================
//  BaseApp Flutter — UI completa
//  Pantallas: Login → Dashboard → Clientes → Detalle → Visitas
// ============================================================

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'dart:async';




Future<void> login() async {
  var url = Uri.parse('http://192.168.1.10/SitioWeb/login.php');

  var response = await http.post(url, body: {
    'email': 'admin@baseapp.pe',
    'password': '170524',
  });

  print(response.body);
}

void main() => runApp(const BaseApp());

// ── COLORES ───────────────────────────────────────────────
const kNavy   = Color(0xFF0F2744);
const kNavy2  = Color(0xFF1A3558);
const kBlue   = Color(0xFF3B82F6);
const kGreen  = Color(0xFF17B26A);
const kAmber  = Color(0xFFF59E0B);
const kRed    = Color(0xFFEF4444);
const kPurple = Color(0xFF7C3AED);
const kSlate  = Color(0xFFF4F6FA);
const kBorder = Color(0xFFDDE4F0);
const kMuted  = Color(0xFF6B7A99);

// ── API URL ────────────────────────────────────────────────
// Emulador Android:  http://10.0.2.2/SitioWeb/api_flutter.php
// Dispositivo físico: http://TU_IP_LOCAL/SitioWeb/api_flutter.php
const kApiUrl = 'http://10.0.2.2/SitioWeb/api_flutter.php';

// ── APP ───────────────────────────────────────────────────
class BaseApp extends StatelessWidget {
  const BaseApp({super.key});
  @override
  Widget build(BuildContext context) => MaterialApp(
        title: 'BaseApp',
        debugShowCheckedModeBanner: false,
        theme: ThemeData(
          colorScheme: ColorScheme.fromSeed(seedColor: kNavy),
          useMaterial3: true,
          fontFamily: 'sans-serif',
          scaffoldBackgroundColor: kSlate,
          appBarTheme: const AppBarTheme(
            backgroundColor: kNavy,
            foregroundColor: Colors.white,
            elevation: 0,
          ),
        ),
        home: const LoginPage(),
      );
}

// ============================================================
//  API HELPER
// ============================================================
Future<Map<String, dynamic>> apiGet(String action,
    {Map<String, String>? params}) async {
  final uri = Uri.parse(kApiUrl)
      .replace(queryParameters: {'action': action, ...?params});
  final res =
      await http.get(uri).timeout(const Duration(seconds: 15));
  final body = jsonDecode(res.body) as Map<String, dynamic>;
  if (body['success'] == false) throw body['error'] ?? 'Error';
  return body;
}

Future<Map<String, dynamic>> apiPost(
    String action, Map<String, dynamic> data) async {
  final uri = Uri.parse('$kApiUrl?action=$action');
  final res = await http
      .post(uri,
          headers: {'Content-Type': 'application/json'},
          body: jsonEncode(data))
      .timeout(const Duration(seconds: 15));
  final body = jsonDecode(res.body) as Map<String, dynamic>;
  if (body['success'] == false) throw body['error'] ?? 'Error';
  return body;
}

// ============================================================
//  LOGIN
// ============================================================
class LoginPage extends StatefulWidget {
  const LoginPage({super.key});
  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final _email = TextEditingController(text: 'admin@baseapp.pe');
  final _pass  = TextEditingController();
  bool _loading = false;
  bool _obscure = true;
  String? _error;

  Future<void> _login() async {
    setState(() { _loading = true; _error = null; });
    try {
      final res = await apiPost('login', {
        'email':    _email.text.trim(),
        'password': _pass.text,
      });
      final usuario = res['data'] as Map<String, dynamic>;
      if (!mounted) return;
      Navigator.pushReplacement(context,
          MaterialPageRoute(builder: (_) => HomePage(usuario: usuario)));
    } catch (e) {
      setState(() { _error = e.toString(); });
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        backgroundColor: kNavy,
        body: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(32),
            child: Column(mainAxisSize: MainAxisSize.min, children: [
              // Logo
              Container(
                width: 64, height: 64,
                decoration: BoxDecoration(
                    color: kBlue, borderRadius: BorderRadius.circular(16)),
                child: const Icon(Icons.layers, color: Colors.white, size: 32),
              ),
              const SizedBox(height: 16),
              const Text('BaseApp',
                  style: TextStyle(color: Colors.white, fontSize: 24,
                      fontWeight: FontWeight.w700, letterSpacing: .5)),
              const Text('Sistema de Gestión',
                  style: TextStyle(color: Colors.white54, fontSize: 13)),
              const SizedBox(height: 36),

              // Card
              Container(
                padding: const EdgeInsets.all(24),
                decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(16)),
                child: Column(crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                  const Text('Iniciar Sesión',
                      style: TextStyle(fontSize: 17, fontWeight: FontWeight.w600,
                          color: kNavy)),
                  const SizedBox(height: 20),
                  _field('Correo electrónico', _email,
                      icon: Icons.email_outlined,
                      keyboard: TextInputType.emailAddress),
                  const SizedBox(height: 12),
                  TextField(
                    controller: _pass,
                    obscureText: _obscure,
                    decoration: InputDecoration(
                      labelText: 'Contraseña',
                      prefixIcon: const Icon(Icons.lock_outline, color: kMuted, size: 18),
                      suffixIcon: IconButton(
                        icon: Icon(_obscure ? Icons.visibility_off : Icons.visibility,
                            color: kMuted, size: 18),
                        onPressed: () => setState(() => _obscure = !_obscure),
                      ),
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
                      focusedBorder: OutlineInputBorder(
                          borderSide: const BorderSide(color: kBlue),
                          borderRadius: BorderRadius.circular(10)),
                      contentPadding: const EdgeInsets.symmetric(
                          horizontal: 14, vertical: 12),
                    ),
                    onSubmitted: (_) => _login(),
                  ),
                  if (_error != null) ...[
                    const SizedBox(height: 10),
                    Container(
                      padding: const EdgeInsets.all(10),
                      decoration: BoxDecoration(
                          color: const Color(0xFFFEE2E2),
                          borderRadius: BorderRadius.circular(8)),
                      child: Text(_error!,
                          style: const TextStyle(color: kRed, fontSize: 13)),
                    ),
                  ],
                  const SizedBox(height: 18),
                  SizedBox(
                    height: 44,
                    child: ElevatedButton(
                      style: ElevatedButton.styleFrom(
                          backgroundColor: kNavy,
                          shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(10))),
                      onPressed: _loading ? null : _login,
                      child: _loading
                          ? const SizedBox(width: 20, height: 20,
                              child: CircularProgressIndicator(
                                  color: Colors.white, strokeWidth: 2))
                          : const Text('Ingresar',
                              style: TextStyle(color: Colors.white,
                                  fontWeight: FontWeight.w600)),
                    ),
                  ),
                ]),
              ),
              const SizedBox(height: 16),
              const Text('v1.0 • BaseApp Enterprise',
                  style: TextStyle(color: Colors.white38, fontSize: 11)),
            ]),
          ),
        ),
      );

  Widget _field(String label, TextEditingController ctrl,
      {IconData? icon, TextInputType? keyboard}) =>
      TextField(
        controller: ctrl,
        keyboardType: keyboard,
        decoration: InputDecoration(
          labelText: label,
          prefixIcon: icon != null ? Icon(icon, color: kMuted, size: 18) : null,
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
          focusedBorder: OutlineInputBorder(
              borderSide: const BorderSide(color: kBlue),
              borderRadius: BorderRadius.circular(10)),
          contentPadding:
              const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
        ),
      );
}

// ============================================================
//  HOME (Bottom Navigation)
// ============================================================
class HomePage extends StatefulWidget {
  final Map<String, dynamic> usuario;
  const HomePage({super.key, required this.usuario});
  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  int _tab = 0;
  late final List<Widget> _pages;

  @override
  void initState() {
    super.initState();
    _pages = [
      DashboardPage(usuario: widget.usuario),
      const ClientesPage(),
      const VisitasPage(),
    ];
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        body: _pages[_tab],
        bottomNavigationBar: NavigationBar(
          selectedIndex: _tab,
          onDestinationSelected: (i) => setState(() => _tab = i),
          backgroundColor: Colors.white,
          indicatorColor: const Color(0xFFDBEAFE),
          destinations: const [
            NavigationDestination(
                icon: Icon(Icons.dashboard_outlined),
                selectedIcon: Icon(Icons.dashboard, color: kNavy),
                label: 'Dashboard'),
            NavigationDestination(
                icon: Icon(Icons.people_outline),
                selectedIcon: Icon(Icons.people, color: kNavy),
                label: 'Clientes'),
            NavigationDestination(
                icon: Icon(Icons.place_outlined),
                selectedIcon: Icon(Icons.place, color: kNavy),
                label: 'Visitas'),
          ],
        ),
      );
}

// ============================================================
//  DASHBOARD
// ============================================================
class DashboardPage extends StatefulWidget {
  final Map<String, dynamic> usuario;
  const DashboardPage({super.key, required this.usuario});
  @override
  State<DashboardPage> createState() => _DashboardPageState();
}

class _DashboardPageState extends State<DashboardPage> {
  Map<String, dynamic>? _stats;
  bool _loading = true;
  String? _error;

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final res = await apiGet('dashboard');
      setState(() { _stats = res['data']; });
    } catch (e) { setState(() => _error = e.toString()); }
    finally { if (mounted) setState(() => _loading = false); }
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        backgroundColor: kSlate,
        appBar: AppBar(
          title: const Text('Dashboard'),
          actions: [
            IconButton(icon: const Icon(Icons.refresh), onPressed: _load),
            Padding(
              padding: const EdgeInsets.only(right: 12),
              child: CircleAvatar(
                backgroundColor: kBlue,
                radius: 16,
                child: Text(
                  (widget.usuario['nombres'] ?? 'A')[0].toUpperCase(),
                  style: const TextStyle(color: Colors.white, fontSize: 13,
                      fontWeight: FontWeight.w600),
                ),
              ),
            ),
          ],
        ),
        body: _loading
            ? const Center(child: CircularProgressIndicator())
            : _error != null
                ? _errorWidget(_error!, _load)
                : RefreshIndicator(
                    onRefresh: _load,
                    child: ListView(padding: const EdgeInsets.all(16), children: [
                      Text('Bienvenido, ${widget.usuario['nombres'] ?? ''}',
                          style: const TextStyle(fontSize: 16,
                              fontWeight: FontWeight.w600, color: kNavy)),
                      const SizedBox(height: 4),
                      Text(widget.usuario['nomrol'] ?? '',
                          style: const TextStyle(color: kMuted, fontSize: 13)),
                      const SizedBox(height: 20),

                      // Stats grid
                      GridView.count(
                        crossAxisCount: 2, shrinkWrap: true,
                        physics: const NeverScrollableScrollPhysics(),
                        crossAxisSpacing: 12, mainAxisSpacing: 12,
                        childAspectRatio: 1.6,
                        children: [
                          _statCard('Clientes',    '${_stats?['total_clientes'] ?? 0}',    kBlue,   Icons.people),
                          _statCard('Visitas',     '${_stats?['total_visitas'] ?? 0}',     kGreen,  Icons.place),
                          _statCard('Desembolsados','${_stats?['desembolsados'] ?? 0}',   kAmber,  Icons.check_circle),
                          _statCard('Hoy',         '${_stats?['visitas_hoy'] ?? 0}',       kPurple, Icons.today),
                        ],
                      ),
                      const SizedBox(height: 20),

                      // Reporte estados
                      _sectionTitle('Clientes por Estado'),
                      const SizedBox(height: 8),
                      ...(_stats?['reporte_estados'] as List? ?? [])
                          .take(6)
                          .map((e) => _barRow(
                                e['label'] ?? '',
                                int.tryParse(e['valor'].toString()) ?? 0,
                                (_stats?['total_clientes'] ?? 1) as int,
                              )),

                      const SizedBox(height: 20),
                      _sectionTitle('Top Distritos'),
                      const SizedBox(height: 8),
                      ...(_stats?['reporte_distritos'] as List? ?? [])
                          .take(5)
                          .map((e) => _barRow(
                                e['label'] ?? '',
                                int.tryParse(e['valor'].toString()) ?? 0,
                                (_stats?['total_clientes'] ?? 1) as int,
                              )),
                    ]),
                  ),
      );

  Widget _statCard(String label, String value, Color color, IconData icon) =>
      Container(
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: kBorder)),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Row(children: [
            Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                  color: color.withOpacity(.12),
                  borderRadius: BorderRadius.circular(7)),
              child: Icon(icon, color: color, size: 16),
            ),
            const Spacer(),
          ]),
          const SizedBox(height: 8),
          Text(value,
              style: TextStyle(fontSize: 22, fontWeight: FontWeight.w700,
                  color: kNavy)),
          Text(label,
              style: const TextStyle(fontSize: 11, color: kMuted,
                  fontWeight: FontWeight.w500)),
        ]),
      );

  Widget _barRow(String label, int valor, int total) {
    final pct = total > 0 ? (valor / total).clamp(0.0, 1.0) : 0.0;
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Row(children: [
          Expanded(child: Text(label,
              style: const TextStyle(fontSize: 12, color: kNavy),
              overflow: TextOverflow.ellipsis)),
          Text('$valor',
              style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600,
                  color: kNavy)),
        ]),
        const SizedBox(height: 4),
        ClipRRect(
          borderRadius: BorderRadius.circular(4),
          child: LinearProgressIndicator(
            value: pct.toDouble(),
            minHeight: 6,
            backgroundColor: kBorder,
            valueColor: const AlwaysStoppedAnimation<Color>(kNavy),
          ),
        ),
      ]),
    );
  }

  Widget _sectionTitle(String t) => Text(t,
      style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600,
          color: kNavy));
}

// ============================================================
//  CLIENTES
// ============================================================
class ClientesPage extends StatefulWidget {
  const ClientesPage({super.key});
  @override
  State<ClientesPage> createState() => _ClientesPageState();
}

class _ClientesPageState extends State<ClientesPage> {
  List<dynamic> _clientes = [];
  List<dynamic> _filtrados = [];
  bool _loading = true;
  String? _error;
  final _search = TextEditingController();

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() { _loading = true; _error = null; });
    try {
      final res = await apiGet('clientes');
      final list = res['data'] as List;
      setState(() { _clientes = list; _filtrados = list; });
    } catch (e) { setState(() => _error = e.toString()); }
    finally { if (mounted) setState(() => _loading = false); }
  }

  void _filter(String q) {
    final query = q.toLowerCase();
    setState(() {
      _filtrados = _clientes.where((c) {
        final nombre = '${c['nombres']} ${c['apellidos']} ${c['dni'] ?? ''}'.toLowerCase();
        return nombre.contains(query);
      }).toList();
    });
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        backgroundColor: kSlate,
        appBar: AppBar(
          title: const Text('Clientes'),
          actions: [
            IconButton(icon: const Icon(Icons.refresh), onPressed: _load),
            IconButton(
              icon: const Icon(Icons.add),
              onPressed: () => Navigator.push(context,
                  MaterialPageRoute(
                      builder: (_) => ClienteFormPage(onSaved: _load))),
            ),
          ],
        ),
        body: _loading
            ? const Center(child: CircularProgressIndicator())
            : _error != null
                ? _errorWidget(_error!, _load)
                : Column(children: [
                    // Buscador
                    Padding(
                      padding: const EdgeInsets.all(12),
                      child: TextField(
                        controller: _search,
                        onChanged: _filter,
                        decoration: InputDecoration(
                          hintText: 'Buscar por nombre, DNI…',
                          prefixIcon: const Icon(Icons.search, color: kMuted, size: 18),
                          filled: true, fillColor: Colors.white,
                          border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(10),
                              borderSide: const BorderSide(color: kBorder)),
                          enabledBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(10),
                              borderSide: const BorderSide(color: kBorder)),
                          contentPadding: const EdgeInsets.symmetric(vertical: 10),
                          suffixIcon: _search.text.isNotEmpty
                              ? IconButton(
                                  icon: const Icon(Icons.close, size: 16),
                                  onPressed: () { _search.clear(); _filter(''); })
                              : null,
                        ),
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 2),
                      child: Row(children: [
                        Text('${_filtrados.length} registros',
                            style: const TextStyle(fontSize: 12, color: kMuted)),
                      ]),
                    ),
                    Expanded(
                      child: RefreshIndicator(
                        onRefresh: _load,
                        child: ListView.builder(
                          padding: const EdgeInsets.all(12),
                          itemCount: _filtrados.length,
                          itemBuilder: (ctx, i) =>
                              _clienteCard(_filtrados[i]),
                        ),
                      ),
                    ),
                  ]),
      );

  Widget _clienteCard(Map<String, dynamic> c) {
    final colores = [kNavy, kBlue, kGreen, kPurple, kAmber];
    final idx = (c['idcliente'] as int? ?? 0) % colores.length;
    final ini = '${(c['nombres'] ?? ' ')[0]}${(c['apellidos'] ?? ' ')[0]}'.toUpperCase();
    return Card(
      color: Colors.white,
      elevation: 0,
      shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(10),
          side: const BorderSide(color: kBorder)),
      margin: const EdgeInsets.only(bottom: 8),
      child: ListTile(
        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
        leading: CircleAvatar(
          backgroundColor: colores[idx].withOpacity(.15),
          child: Text(ini,
              style: TextStyle(color: colores[idx],
                  fontWeight: FontWeight.w700, fontSize: 13)),
        ),
        title: Text('${c['nombres']} ${c['apellidos']}',
            style: const TextStyle(fontWeight: FontWeight.w600,
                fontSize: 14, color: kNavy)),
        subtitle: Column(crossAxisAlignment: CrossAxisAlignment.start,
            children: [
          if (c['dni'] != null)
            Text('DNI: ${c['dni']}',
                style: const TextStyle(fontSize: 12, color: kMuted)),
          Row(children: [
            Icon(Icons.place_outlined, size: 12, color: kMuted),
            const SizedBox(width: 3),
            Expanded(child: Text(c['nomdistrito'] ?? '',
                style: const TextStyle(fontSize: 12, color: kMuted),
                overflow: TextOverflow.ellipsis)),
            _badgeEstado(c['nomestado'] ?? ''),
          ]),
        ]),
        onTap: () => Navigator.push(context,
            MaterialPageRoute(
                builder: (_) => ClienteDetallePage(
                    idcliente: c['idcliente'], onChanged: _load))),
      ),
    );
  }
}

// ── BADGE ESTADO ──────────────────────────────────────────
Widget _badgeEstado(String estado) {
  final map = {
    'Desembolsado':          [kGreen,  const Color(0xFFD1FAE5)],
    'Interesado':            [kBlue,   const Color(0xFFDBEAFE)],
    'Volver a visitar':      [kAmber,  const Color(0xFFFEF3C7)],
    'No desea oferta':       [kRed,    const Color(0xFFFEE2E2)],
    'Falleció':              [kRed,    const Color(0xFFFEE2E2)],
    'Sin gestión':           [kMuted,  const Color(0xFFF1F5F9)],
    'Teléfonos errados':     [kPurple, const Color(0xFFEDE9FE)],
    'No se encontró dirección': [kAmber, const Color(0xFFFEF3C7)],
  };
  final colors = map[estado] ?? [kMuted, const Color(0xFFF1F5F9)];
  return Container(
    padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
    decoration: BoxDecoration(
        color: colors[1] as Color,
        borderRadius: BorderRadius.circular(5)),
    child: Text(estado,
        style: TextStyle(
            fontSize: 10, fontWeight: FontWeight.w600,
            color: colors[0] as Color)),
  );
}

// ============================================================
//  DETALLE CLIENTE
// ============================================================
class ClienteDetallePage extends StatefulWidget {
  final int idcliente;
  final VoidCallback? onChanged;
  const ClienteDetallePage({super.key, required this.idcliente, this.onChanged});
  @override
  State<ClienteDetallePage> createState() => _ClienteDetallePageState();
}

class _ClienteDetallePageState extends State<ClienteDetallePage>
    with SingleTickerProviderStateMixin {
  Map<String, dynamic>? _cliente;
  List<dynamic> _visitas = [];
  bool _loading = true;
  late TabController _tabs;

  @override
  void initState() {
    super.initState();
    _tabs = TabController(length: 2, vsync: this);
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final [resC, resV] = await Future.wait([
        apiGet('cliente.detalle', params: {'id': '${widget.idcliente}'}),
        apiGet('visitas.cliente', params: {'id': '${widget.idcliente}'}),
      ]);
      setState(() {
        _cliente = resC['data'] as Map<String, dynamic>;
        _visitas  = resV['data'] as List;
      });
    } catch (e) {
      _showSnack(e.toString(), isError: true);
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _eliminar() async {
    final ok = await showDialog<bool>(
        context: context,
        builder: (_) => AlertDialog(
              title: const Text('Confirmar eliminación'),
              content: const Text('¿Eliminar este cliente y todas sus visitas?'),
              actions: [
                TextButton(onPressed: () => Navigator.pop(context, false),
                    child: const Text('Cancelar')),
                TextButton(
                    style: TextButton.styleFrom(foregroundColor: kRed),
                    onPressed: () => Navigator.pop(context, true),
                    child: const Text('Eliminar')),
              ],
            ));
    if (ok != true) return;
    try {
      await apiPost('cliente.eliminar', {'idcliente': widget.idcliente});
      widget.onChanged?.call();
      if (mounted) Navigator.pop(context);
    } catch (e) { _showSnack(e.toString(), isError: true); }
  }

  void _showSnack(String msg, {bool isError = false}) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        content: Text(msg),
        backgroundColor: isError ? kRed : kGreen));
  }

  @override
  Widget build(BuildContext context) {
    final c = _cliente;
    return Scaffold(
      backgroundColor: kSlate,
      appBar: AppBar(
        title: Text(c == null ? 'Detalle' : '${c['nombres']} ${c['apellidos']}'),
        actions: [
          if (c != null) ...[
            IconButton(
              icon: const Icon(Icons.edit_outlined),
              onPressed: () => Navigator.push(
                  context,
                  MaterialPageRoute(
                      builder: (_) => ClienteFormPage(
                          cliente: c, onSaved: () { _load(); widget.onChanged?.call(); }))),
            ),
            IconButton(
                icon: const Icon(Icons.delete_outline),
                onPressed: _eliminar),
          ],
        ],
        bottom: TabBar(
          controller: _tabs,
          labelColor: Colors.white,
          unselectedLabelColor: Colors.white54,
          indicatorColor: Colors.white,
          tabs: const [Tab(text: 'Información'), Tab(text: 'Visitas')],
        ),
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : TabBarView(controller: _tabs, children: [
              // ── TAB INFO ──────────────────────────────
              ListView(padding: const EdgeInsets.all(16), children: [
                _infoCard('Datos Personales', [
                  _infoRow(Icons.person_outline, 'Nombres', '${c?['nombres']} ${c?['apellidos']}'),
                  _infoRow(Icons.badge_outlined, 'DNI', c?['dni'] ?? '—'),
                  _infoRow(Icons.phone_outlined, 'Teléfono 1', c?['telefono1'] ?? '—'),
                  _infoRow(Icons.phone_outlined, 'Teléfono 2', c?['telefono2'] ?? '—'),
                ]),
                const SizedBox(height: 12),
                _infoCard('Ubicación', [
                  _infoRow(Icons.place_outlined, 'Distrito', c?['nomdistrito'] ?? '—'),
                  _infoRow(Icons.home_outlined, 'Dirección', c?['direccion'] ?? '—'),
                  if (c?['latitud'] != null)
                    _infoRow(Icons.gps_fixed, 'GPS', '${c?['latitud']}, ${c?['longitud']}'),
                ]),
                const SizedBox(height: 12),
                _infoCard('Estado', [
                  _infoRow(Icons.info_outline, 'Estado actual',
                      c?['nomestado'] ?? '—', badge: true),
                  _infoRow(Icons.timeline, 'Total visitas',
                      '${c?['total_visitas'] ?? 0}'),
                  _infoRow(Icons.event, 'Registrado',
                      (c?['created_at'] ?? '—').toString().split(' ')[0]),
                ]),
              ]),

              // ── TAB VISITAS ───────────────────────────
              _visitas.isEmpty
                  ? const Center(child: Text('Sin visitas registradas',
                      style: TextStyle(color: kMuted)))
                  : ListView.builder(
                      padding: const EdgeInsets.all(12),
                      itemCount: _visitas.length,
                      itemBuilder: (ctx, i) => _visitaCard(_visitas[i])),
            ]),
      floatingActionButton: FloatingActionButton.extended(
        backgroundColor: kNavy,
        icon: const Icon(Icons.add_location_alt_outlined, color: Colors.white),
        label: const Text('Nueva Visita', style: TextStyle(color: Colors.white)),
        onPressed: () => Navigator.push(
            context,
            MaterialPageRoute(
                builder: (_) => VisitaFormPage(
                    idcliente: widget.idcliente,
                    onSaved: _load))),
      ),
    );
  }

  Widget _infoCard(String title, List<Widget> rows) => Container(
        decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: kBorder)),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 14, 16, 10),
            child: Text(title,
                style: const TextStyle(fontWeight: FontWeight.w600,
                    fontSize: 13, color: kNavy)),
          ),
          const Divider(height: 1, color: kBorder),
          ...rows,
        ]),
      );

  Widget _infoRow(IconData icon, String label, String value,
      {bool badge = false}) =>
      Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
        child: Row(children: [
          Icon(icon, size: 16, color: kMuted),
          const SizedBox(width: 10),
          Expanded(
              child: Text(label,
                  style: const TextStyle(fontSize: 12, color: kMuted))),
          badge ? _badgeEstado(value) : Text(value,
              style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w500,
                  color: kNavy)),
        ]),
      );

  Widget _visitaCard(Map<String, dynamic> v) => Container(
        margin: const EdgeInsets.only(bottom: 8),
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(10),
            border: Border.all(color: kBorder)),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Row(children: [
            Text((v['fecha_visita'] ?? '').toString().split(' ')[0],
                style: const TextStyle(fontWeight: FontWeight.w600,
                    fontSize: 13, color: kNavy)),
            const Spacer(),
            _badgeEstado(v['estado'] ?? '—'),
          ]),
          if (v['producto'] != null) ...[
            const SizedBox(height: 4),
            Text('Producto: ${v['producto']}',
                style: const TextStyle(fontSize: 12, color: kMuted)),
          ],
          if (v['observacion'] != null && v['observacion'].toString().isNotEmpty) ...[
            const SizedBox(height: 4),
            Text(v['observacion'],
                style: const TextStyle(fontSize: 12, color: Colors.black87)),
          ],
          const SizedBox(height: 4),
          Text('Vendedor: ${v['vendedor']}',
              style: const TextStyle(fontSize: 11, color: kMuted)),
        ]),
      );
}

// ============================================================
//  VISITAS PAGE
// ============================================================
class VisitasPage extends StatefulWidget {
  const VisitasPage({super.key});
  @override
  State<VisitasPage> createState() => _VisitasPageState();
}

class _VisitasPageState extends State<VisitasPage> {
  List<dynamic> _visitas = [];
  bool _loading = true;

  @override
  void initState() { super.initState(); _load(); }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final res = await apiGet('visitas', params: {'limit': '100'});
      setState(() => _visitas = res['data'] as List);
    } catch (e) {
      ScaffoldMessenger.of(context)
          .showSnackBar(SnackBar(content: Text(e.toString()), backgroundColor: kRed));
    } finally { if (mounted) setState(() => _loading = false); }
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        backgroundColor: kSlate,
        appBar: AppBar(
          title: const Text('Visitas'),
          actions: [IconButton(icon: const Icon(Icons.refresh), onPressed: _load)],
        ),
        body: _loading
            ? const Center(child: CircularProgressIndicator())
            : RefreshIndicator(
                onRefresh: _load,
                child: ListView.builder(
                  padding: const EdgeInsets.all(12),
                  itemCount: _visitas.length,
                  itemBuilder: (ctx, i) {
                    final v = _visitas[i] as Map<String, dynamic>;
                    return Card(
                      color: Colors.white,
                      elevation: 0,
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10),
                          side: const BorderSide(color: kBorder)),
                      margin: const EdgeInsets.only(bottom: 8),
                      child: ListTile(
                        contentPadding:
                            const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                        leading: Container(
                          width: 36, height: 36,
                          decoration: BoxDecoration(
                              color: kNavy.withOpacity(.08),
                              borderRadius: BorderRadius.circular(8)),
                          child: const Icon(Icons.place, color: kNavy, size: 18),
                        ),
                        title: Text(v['cliente'] ?? '—',
                            style: const TextStyle(fontWeight: FontWeight.w600,
                                fontSize: 13, color: kNavy)),
                        subtitle: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                          Text(v['distrito'] ?? '—',
                              style: const TextStyle(fontSize: 12, color: kMuted)),
                          Row(children: [
                            Text((v['fecha_visita'] ?? '').toString().split(' ')[0],
                                style: const TextStyle(fontSize: 11, color: kMuted)),
                            const Spacer(),
                            _badgeEstado(v['estado'] ?? '—'),
                          ]),
                        ]),
                      ),
                    );
                  },
                ),
              ),
      );
}

// ============================================================
//  FORMULARIO CLIENTE (Crear / Editar)
// ============================================================
class ClienteFormPage extends StatefulWidget {
  final Map<String, dynamic>? cliente;
  final VoidCallback? onSaved;
  const ClienteFormPage({super.key, this.cliente, this.onSaved});
  @override
  State<ClienteFormPage> createState() => _ClienteFormPageState();
}

class _ClienteFormPageState extends State<ClienteFormPage> {
  final _nombres   = TextEditingController();
  final _apellidos = TextEditingController();
  final _dni       = TextEditingController();
  final _tel1      = TextEditingController();
  final _tel2      = TextEditingController();
  final _dir       = TextEditingController();
  final _ref       = TextEditingController();
  int? _distId, _estId;
  List<dynamic> _distritos = [];
  List<dynamic> _estados = [];
  bool _loading = false;
  bool _loadingCat = true;

  bool get _editing => widget.cliente != null;

  @override
  void initState() {
    super.initState();
    _loadCatalogos();
    if (_editing) {
      final c = widget.cliente!;
      _nombres.text   = c['nombres']   ?? '';
      _apellidos.text = c['apellidos'] ?? '';
      _dni.text       = c['dni']       ?? '';
      _tel1.text      = c['telefono1'] ?? '';
      _tel2.text      = c['telefono2'] ?? '';
      _dir.text       = c['direccion'] ?? '';
      _ref.text       = c['referencia'] ?? '';
      _distId         = int.tryParse(c['iddistrito'].toString());
      _estId          = int.tryParse(c['idestado'].toString());
    }
  }

  Future<void> _loadCatalogos() async {
    try {
      final res = await apiGet('catalogos');
      final d = res['data'] as Map<String, dynamic>;
      setState(() {
        _distritos = d['distritos'] as List;
        _estados   = d['estados']   as List;
        _loadingCat = false;
      });
    } catch (e) { setState(() => _loadingCat = false); }
  }

  Future<void> _guardar() async {
    if (_nombres.text.isEmpty || _apellidos.text.isEmpty ||
        _distId == null || _estId == null) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
          content: Text('Completa los campos obligatorios'),
          backgroundColor: kRed));
      return;
    }
    setState(() => _loading = true);
    try {
      final payload = {
        if (_editing) 'idcliente': widget.cliente!['idcliente'],
        'nombres':    _nombres.text.trim(),
        'apellidos':  _apellidos.text.trim(),
        'dni':        _dni.text.trim(),
        'telefono1':  _tel1.text.trim(),
        'telefono2':  _tel2.text.trim(),
        'direccion':  _dir.text.trim(),
        'referencia': _ref.text.trim(),
        'iddistrito': _distId,
        'idestado':   _estId,
      };
      final action = _editing ? 'cliente.actualizar' : 'cliente.crear';
      await apiPost(action, payload);
      widget.onSaved?.call();
      if (mounted) Navigator.pop(context);
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(e.toString()), backgroundColor: kRed));
    } finally { if (mounted) setState(() => _loading = false); }
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        backgroundColor: kSlate,
        appBar: AppBar(
          title: Text(_editing ? 'Editar Cliente' : 'Nuevo Cliente'),
          actions: [
            TextButton(
              onPressed: _loading ? null : _guardar,
              child: _loading
                  ? const SizedBox(width: 18, height: 18,
                      child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                  : const Text('Guardar',
                      style: TextStyle(color: Colors.white,
                          fontWeight: FontWeight.w600)),
            ),
          ],
        ),
        body: _loadingCat
            ? const Center(child: CircularProgressIndicator())
            : ListView(padding: const EdgeInsets.all(16), children: [
                _formCard('Datos Personales', [
                  _tf('Nombres *', _nombres),
                  _tf('Apellidos *', _apellidos),
                  _tf('DNI', _dni, keyboard: TextInputType.number),
                  _tf('Teléfono 1', _tel1, keyboard: TextInputType.phone),
                  _tf('Teléfono 2', _tel2, keyboard: TextInputType.phone),
                ]),
                const SizedBox(height: 12),
                _formCard('Clasificación', [
                  _dropdown('Distrito *', _distritos, _distId,
                      (v) => setState(() => _distId = v)),
                  _dropdown('Estado *', _estados, _estId,
                      (v) => setState(() => _estId = v)),
                ]),
                const SizedBox(height: 12),
                _formCard('Ubicación', [
                  _tf('Dirección', _dir),
                  _tf('Referencia', _ref),
                ]),
                const SizedBox(height: 24),
              ]),
      );

  Widget _formCard(String title, List<Widget> fields) => Container(
        decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: kBorder)),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 14, 16, 0),
            child: Text(title,
                style: const TextStyle(fontWeight: FontWeight.w600,
                    fontSize: 13, color: kNavy)),
          ),
          Padding(
            padding: const EdgeInsets.all(14),
            child: Column(children: fields
                .map((f) => Padding(
                    padding: const EdgeInsets.only(bottom: 10), child: f))
                .toList()),
          ),
        ]),
      );

  Widget _tf(String label, TextEditingController ctrl,
      {TextInputType? keyboard}) =>
      TextField(
        controller: ctrl,
        keyboardType: keyboard,
        decoration: InputDecoration(
          labelText: label,
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
          focusedBorder: OutlineInputBorder(
              borderSide: const BorderSide(color: kBlue),
              borderRadius: BorderRadius.circular(8)),
          contentPadding:
              const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
          isDense: true,
        ),
      );

  Widget _dropdown(String label, List<dynamic> items, int? value,
      ValueChanged<int?> onChanged) =>
      DropdownButtonFormField<int>(
        value: value,
        decoration: InputDecoration(
          labelText: label,
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
          focusedBorder: OutlineInputBorder(
              borderSide: const BorderSide(color: kBlue),
              borderRadius: BorderRadius.circular(8)),
          contentPadding:
              const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
          isDense: true,
        ),
        items: items.map<DropdownMenuItem<int>>((item) {
          return DropdownMenuItem<int>(
            value: int.parse(item['id'].toString()),
            child: Text(item['nombre'].toString(),
                style: const TextStyle(fontSize: 13)),
          );
        }).toList(),
        onChanged: onChanged,
      );
}

// ============================================================
//  FORMULARIO VISITA
// ============================================================
class VisitaFormPage extends StatefulWidget {
  final int idcliente;
  final VoidCallback? onSaved;
  const VisitaFormPage({super.key, required this.idcliente, this.onSaved});
  @override
  State<VisitaFormPage> createState() => _VisitaFormPageState();
}

class _VisitaFormPageState extends State<VisitaFormPage> {
  final _obs   = TextEditingController();
  int? _estId, _prodId;
  List<dynamic> _estados = [], _productos = [];
  bool _loading = false, _loadingCat = true;
  // idusuario fijo = 1 (admin) — cámbialo según la sesión real
  static const int kUserId = 1;

  @override
  void initState() { super.initState(); _loadCatalogos(); }

  Future<void> _loadCatalogos() async {
    try {
      final res = await apiGet('catalogos');
      final d = res['data'] as Map<String, dynamic>;
      setState(() {
        _estados   = d['estados']  as List;
        _productos = d['productos'] as List;
        _loadingCat = false;
      });
    } catch (e) { setState(() => _loadingCat = false); }
  }

  Future<void> _guardar() async {
    if (_estId == null) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
          content: Text('Selecciona un estado'),
          backgroundColor: kRed));
      return;
    }
    setState(() => _loading = true);
    try {
      await apiPost('visita.registrar', {
        'idcliente':   widget.idcliente,
        'idusuario':   kUserId,
        'idestado':    _estId,
        if (_prodId != null) 'idproducto': _prodId,
        'observacion': _obs.text.trim(),
      });
      widget.onSaved?.call();
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
            content: Text('Visita registrada'),
            backgroundColor: kGreen));
        Navigator.pop(context);
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(e.toString()), backgroundColor: kRed));
    } finally { if (mounted) setState(() => _loading = false); }
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        backgroundColor: kSlate,
        appBar: AppBar(
          title: const Text('Registrar Visita'),
          actions: [
            TextButton(
              onPressed: _loading ? null : _guardar,
              child: const Text('Guardar',
                  style: TextStyle(color: Colors.white, fontWeight: FontWeight.w600)),
            ),
          ],
        ),
        body: _loadingCat
            ? const Center(child: CircularProgressIndicator())
            : ListView(padding: const EdgeInsets.all(16), children: [
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(color: kBorder)),
                  child: Column(children: [
                    DropdownButtonFormField<int>(
                      value: _estId,
                      decoration: _deco('Estado de la visita *'),
                      items: _estados.map<DropdownMenuItem<int>>((e) =>
                          DropdownMenuItem<int>(
                              value: int.parse(e['id'].toString()),
                              child: Text(e['nombre'].toString(),
                                  style: const TextStyle(fontSize: 13)))).toList(),
                      onChanged: (v) => setState(() => _estId = v),
                    ),
                    const SizedBox(height: 12),
                    DropdownButtonFormField<int>(
                      value: _prodId,
                      decoration: _deco('Producto ofertado (opcional)'),
                      items: [
                        const DropdownMenuItem<int>(value: null, child: Text('— Ninguno —')),
                        ..._productos.map<DropdownMenuItem<int>>((p) =>
                            DropdownMenuItem<int>(
                                value: int.parse(p['id'].toString()),
                                child: Text(p['nombre'].toString(),
                                    style: const TextStyle(fontSize: 13)))),
                      ],
                      onChanged: (v) => setState(() => _prodId = v),
                    ),
                    const SizedBox(height: 12),
                    TextField(
                      controller: _obs,
                      maxLines: 4,
                      decoration: _deco('Observaciones'),
                    ),
                  ]),
                ),
              ]),
      );

  InputDecoration _deco(String label) => InputDecoration(
        labelText: label,
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
        focusedBorder: OutlineInputBorder(
            borderSide: const BorderSide(color: kBlue),
            borderRadius: BorderRadius.circular(8)),
        contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
        isDense: true,
      );
}

// ── ERROR WIDGET ──────────────────────────────────────────
Widget _errorWidget(String msg, VoidCallback onRetry) => Center(
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(mainAxisSize: MainAxisSize.min, children: [
          const Icon(Icons.wifi_off_outlined, color: kMuted, size: 48),
          const SizedBox(height: 12),
          Text('Error de conexión', style: const TextStyle(
              fontWeight: FontWeight.w600, fontSize: 16, color: kNavy)),
          const SizedBox(height: 6),
          Text(msg, style: const TextStyle(color: kMuted, fontSize: 13),
              textAlign: TextAlign.center),
          const SizedBox(height: 16),
          ElevatedButton.icon(
            style: ElevatedButton.styleFrom(backgroundColor: kNavy),
            onPressed: onRetry,
            icon: const Icon(Icons.refresh, color: Colors.white, size: 16),
            label: const Text('Reintentar',
                style: TextStyle(color: Colors.white)),
          ),
        ]),
      ),
    );
