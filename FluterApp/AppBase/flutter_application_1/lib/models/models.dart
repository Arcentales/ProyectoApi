// lib/models/models.dart
// ============================================================
//  Modelos de datos BaseApp
// ============================================================

// ── CLIENTE ───────────────────────────────────────────────
class Cliente {
  final int idcliente;
  final String nombres;
  final String apellidos;
  final String? dni;
  final String? telefono1;
  final String? telefono2;
  final String? direccion;
  final String nomdistrito;
  final String nomestado;
  final int idestado;
  final int iddistrito;
  final double? latitud;
  final double? longitud;
  final String? createdAt;

  Cliente({
    required this.idcliente,
    required this.nombres,
    required this.apellidos,
    this.dni,
    this.telefono1,
    this.telefono2,
    this.direccion,
    required this.nomdistrito,
    required this.nomestado,
    required this.idestado,
    required this.iddistrito,
    this.latitud,
    this.longitud,
    this.createdAt,
  });

  String get nombreCompleto => '$nombres $apellidos';

  String get iniciales {
    final n = nombres.isNotEmpty ? nombres[0] : '';
    final a = apellidos.isNotEmpty ? apellidos[0] : '';
    return (n + a).toUpperCase();
  }

  factory Cliente.fromJson(Map<String, dynamic> j) => Cliente(
        idcliente:    int.parse(j['idcliente'].toString()),
        nombres:      j['nombres']     ?? '',
        apellidos:    j['apellidos']   ?? '',
        dni:          j['dni'],
        telefono1:    j['telefono1'],
        telefono2:    j['telefono2'],
        direccion:    j['direccion'],
        nomdistrito:  j['nomdistrito'] ?? '',
        nomestado:    j['nomestado']   ?? '',
        idestado:     int.parse((j['idestado']   ?? 0).toString()),
        iddistrito:   int.parse((j['iddistrito'] ?? 0).toString()),
        latitud:      j['latitud']  != null ? double.tryParse(j['latitud'].toString()) : null,
        longitud:     j['longitud'] != null ? double.tryParse(j['longitud'].toString()) : null,
        createdAt:    j['created_at'],
      );

  Map<String, dynamic> toJson() => {
        'idcliente':  idcliente,
        'nombres':    nombres,
        'apellidos':  apellidos,
        'dni':        dni ?? '',
        'telefono1':  telefono1 ?? '',
        'telefono2':  telefono2 ?? '',
        'direccion':  direccion ?? '',
        'iddistrito': iddistrito,
        'idestado':   idestado,
        'latitud':    latitud,
        'longitud':   longitud,
      };
}

// ── VISITA ────────────────────────────────────────────────
class Visita {
  final int idvisita;
  final String fechaVisita;
  final String cliente;
  final String? distrito;
  final String? producto;
  final String estado;
  final String vendedor;
  final String? observacion;

  Visita({
    required this.idvisita,
    required this.fechaVisita,
    required this.cliente,
    this.distrito,
    this.producto,
    required this.estado,
    required this.vendedor,
    this.observacion,
  });

  factory Visita.fromJson(Map<String, dynamic> j) => Visita(
        idvisita:     int.parse(j['idvisita'].toString()),
        fechaVisita:  j['fecha_visita'] ?? '',
        cliente:      j['cliente']      ?? '',
        distrito:     j['distrito'],
        producto:     j['producto'],
        estado:       j['estado']       ?? '',
        vendedor:     j['vendedor']     ?? '',
        observacion:  j['observacion'],
      );
}

// ── CATALOGO ITEM ─────────────────────────────────────────
class CatalogoItem {
  final int id;
  final String nombre;
  CatalogoItem({required this.id, required this.nombre});

  factory CatalogoItem.fromJson(Map<String, dynamic> j) => CatalogoItem(
        id:     int.parse(j['id'].toString()),
        nombre: j['nombre'] ?? '',
      );
  @override
  String toString() => nombre;
}

// ── DASHBOARD STATS ───────────────────────────────────────
class DashboardStats {
  final int totalClientes;
  final int totalVisitas;
  final int desembolsados;
  final int interesados;
  final int visitasHoy;
  final int visitasSemana;
  final List<ReporteItem> reporteEstados;
  final List<ReporteItem> reporteDistritos;

  DashboardStats({
    required this.totalClientes,
    required this.totalVisitas,
    required this.desembolsados,
    required this.interesados,
    required this.visitasHoy,
    required this.visitasSemana,
    required this.reporteEstados,
    required this.reporteDistritos,
  });

  factory DashboardStats.fromJson(Map<String, dynamic> j) => DashboardStats(
        totalClientes:    int.parse((j['total_clientes']  ?? 0).toString()),
        totalVisitas:     int.parse((j['total_visitas']   ?? 0).toString()),
        desembolsados:    int.parse((j['desembolsados']   ?? 0).toString()),
        interesados:      int.parse((j['interesados']     ?? 0).toString()),
        visitasHoy:       int.parse((j['visitas_hoy']     ?? 0).toString()),
        visitasSemana:    int.parse((j['visitas_semana']  ?? 0).toString()),
        reporteEstados:   (j['reporte_estados']   as List? ?? []).map((e) => ReporteItem.fromJson(e)).toList(),
        reporteDistritos: (j['reporte_distritos'] as List? ?? []).map((e) => ReporteItem.fromJson(e)).toList(),
      );
}

// ── REPORTE ITEM ──────────────────────────────────────────
class ReporteItem {
  final String label;
  final int valor;
  ReporteItem({required this.label, required this.valor});

  factory ReporteItem.fromJson(Map<String, dynamic> j) => ReporteItem(
        label: j['label'] ?? '',
        valor: int.parse((j['valor'] ?? 0).toString()),
      );
}

// ── USUARIO (sesión) ──────────────────────────────────────
class Usuario {
  final int idusuario;
  final String nombres;
  final String apellidos;
  final String email;
  final String rol;
  final String token;

  Usuario({
    required this.idusuario,
    required this.nombres,
    required this.apellidos,
    required this.email,
    required this.rol,
    required this.token,
  });

  String get nombreCompleto => '$nombres $apellidos';

  factory Usuario.fromJson(Map<String, dynamic> j) => Usuario(
        idusuario: int.parse(j['idusuario'].toString()),
        nombres:   j['nombres']  ?? '',
        apellidos: j['apellidos'] ?? '',
        email:     j['email']    ?? '',
        rol:       j['nomrol']   ?? '',
        token:     j['token']    ?? '',
      );
}
