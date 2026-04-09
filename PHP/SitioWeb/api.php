<?php
// ============================================================
//  BaseApp - API REST (api.php)
// ============================================================

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/config/conexion.php';

// ============================================================
//  FUNCIONES AUXILIARES
// ============================================================

function response($ok, $data = null, $message = '') {
    echo json_encode([
        'ok' => $ok,
        'data' => $data,
        'message' => $message
    ]);
    exit;
}

function getJSON() {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        throw new Exception("JSON inválido");
    }
    return $data;
}

// ============================================================
//  ROUTER
// ============================================================

$action = $_GET['action'] ?? '';

try {

    switch ($action) {

        // ── CLIENTES ──────────────────────────────────────────
        case 'clientes.listar':
            response(true, listar_clientes());
            break;

        case 'clientes.crear':
            $d = getJSON();
            response(true, crear_cliente($d), "Cliente creado");
            break;

        case 'clientes.actualizar':
            $d = getJSON();
            response(true, actualizar_cliente($d), "Cliente actualizado");
            break;

        case 'clientes.eliminar':
            $d = getJSON();
            response(true, eliminar_cliente((int)$d['idcliente']), "Cliente eliminado");
            break;

        // ── VISITAS ──────────────────────────────────────────
        case 'visitas.listar':
            response(true, listar_visitas());
            break;

        // ── CATÁLOGOS ────────────────────────────────────────
        case 'distritos':
            response(true, catalogo('distrito', 'iddistrito', 'nomdistrito'));
            break;

        case 'estados':
            response(true, catalogo('estado', 'idestado', 'nomestado'));
            break;

        case 'productos':
            response(true, catalogo('producto', 'idproducto', 'nomproducto'));
            break;

        // ── DASHBOARD ────────────────────────────────────────
        case 'dashboard.stats':
            response(true, dashboard_stats());
            break;

        // ── REPORTES ─────────────────────────────────────────
        case 'reporte.estados':
            response(true, reporte_estados());
            break;

        case 'reporte.distritos':
            response(true, reporte_distritos());
            break;

        case 'reporte.visitas_mes':
            response(true, reporte_visitas_mes());
            break;

        default:
            http_response_code(404);
            response(false, null, "Acción no encontrada");
    }

} catch (Throwable $e) {
    http_response_code(500);
    error_log($e->getMessage());
    response(false, null, $e->getMessage());
}

// ============================================================
//  CLIENTES
// ============================================================

function listar_clientes(): array {
    $conn = getConexion();

    $sql = "SELECT c.idcliente, c.nombres, c.apellidos, c.dni, c.telefono1,
                   c.direccion, c.latitud, c.longitud, c.created_at,
                   d.nomdistrito, e.nomestado
            FROM cliente c
            JOIN distrito d ON d.iddistrito = c.iddistrito
            JOIN estado e ON e.idestado = c.idestado
            ORDER BY c.updated_at DESC
            LIMIT 200";

    $res = $conn->query($sql);

    $rows = [];
    while ($r = $res->fetch_assoc()) $rows[] = $r;

    return $rows;
}

function crear_cliente(array $d): array {
    $conn = getConexion();

    if (!isset($d['dni'], $d['nombres'])) {
        throw new Exception("Datos incompletos");
    }

    $stmt = $conn->prepare("INSERT INTO cliente
        (iddistrito, idestado, dni, nombres, apellidos, telefono1, telefono2, direccion, referencia, latitud, longitud)
        VALUES (?,?,?,?,?,?,?,?,?,?,?)");

    $stmt->bind_param(
        'iisssssssdd',
        $d['iddistrito'], $d['idestado'], $d['dni'],
        $d['nombres'], $d['apellidos'],
        $d['telefono1'], $d['telefono2'],
        $d['direccion'], $d['referencia'],
        $d['latitud'], $d['longitud']
    );

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    $id = $stmt->insert_id;
    $stmt->close();

    return ['idcliente' => $id];
}

function actualizar_cliente(array $d): array {
    $conn = getConexion();

    if (!isset($d['idcliente'])) {
        throw new Exception("ID requerido");
    }

    $stmt = $conn->prepare("UPDATE cliente SET
        iddistrito=?, idestado=?, dni=?, nombres=?, apellidos=?,
        telefono1=?, telefono2=?, direccion=?, referencia=?,
        latitud=?, longitud=?
        WHERE idcliente=?");

    $stmt->bind_param(
        'iisssssssddi',
        $d['iddistrito'], $d['idestado'], $d['dni'],
        $d['nombres'], $d['apellidos'],
        $d['telefono1'], $d['telefono2'],
        $d['direccion'], $d['referencia'],
        $d['latitud'], $d['longitud'],
        $d['idcliente']
    );

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    $stmt->close();

    return ['ok' => true];
}

function eliminar_cliente(int $id): array {
    $conn = getConexion();

    // eliminar visitas con prepared
    $stmt = $conn->prepare("DELETE FROM visita WHERE idcliente = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM cliente WHERE idcliente = ?");
    $stmt->bind_param('i', $id);

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    $stmt->close();

    return ['ok' => true];
}

// ============================================================
//  VISITAS
// ============================================================

function listar_visitas(): array {
    $conn = getConexion();

    $res = $conn->query("SELECT * FROM vw_visita_detalle ORDER BY fecha_visita DESC LIMIT 100");

    $rows = [];
    while ($r = $res->fetch_assoc()) $rows[] = $r;

    return $rows;
}

// ============================================================
//  CATÁLOGOS
// ============================================================

function catalogo(string $tabla, string $campoId, string $campoNom): array {
    $conn = getConexion();

    $sql = "SELECT $campoId AS id, $campoNom AS nombre FROM $tabla ORDER BY $campoNom";
    $res = $conn->query($sql);

    $rows = [];
    while ($r = $res->fetch_assoc()) $rows[] = $r;

    return $rows;
}

// ============================================================
//  DASHBOARD
// ============================================================

function dashboard_stats(): array {
    $conn = getConexion();
    $stats = [];

    $queries = [
        'total_clientes'   => "SELECT COUNT(*) total FROM cliente",
        'total_visitas'    => "SELECT COUNT(*) total FROM visita",
        'desembolsados'    => "SELECT COUNT(*) total FROM visita WHERE idestado = 3",
        'visitas_hoy'      => "SELECT COUNT(*) total FROM visita WHERE DATE(fecha_visita) = CURDATE()",
        'interesados'      => "SELECT COUNT(*) total FROM cliente WHERE idestado = 6",
        'usuarios_activos' => "SELECT COUNT(*) total FROM usuario WHERE activo = 1"
    ];

    foreach ($queries as $key => $sql) {
        $res = $conn->query($sql);
        $stats[$key] = (int)$res->fetch_assoc()['total'];
    }

    return $stats;
}

// ============================================================
//  REPORTES
// ============================================================

function reporte_estados(): array {
    $conn = getConexion();

    $res = $conn->query(
        "SELECT e.nomestado AS label, COUNT(c.idcliente) AS valor
         FROM estado e
         LEFT JOIN cliente c ON c.idestado = e.idestado
         GROUP BY e.idestado ORDER BY valor DESC"
    );

    $rows = [];
    while ($r = $res->fetch_assoc()) $rows[] = $r;

    return $rows;
}

function reporte_distritos(): array {
    $conn = getConexion();

    $res = $conn->query(
        "SELECT d.nomdistrito AS label, COUNT(c.idcliente) AS valor
         FROM distrito d
         LEFT JOIN cliente c ON c.iddistrito = d.iddistrito
         GROUP BY d.iddistrito ORDER BY valor DESC LIMIT 10"
    );

    $rows = [];
    while ($r = $res->fetch_assoc()) $rows[] = $r;

    return $rows;
}

function reporte_visitas_mes(): array {
    $conn = getConexion();

    $res = $conn->query(
        "SELECT DATE_FORMAT(fecha_visita,'%Y-%m-%d') AS label, COUNT(*) AS valor
         FROM visita
         WHERE fecha_visita >= DATE_SUB(NOW(), INTERVAL 30 DAY)
         GROUP BY DATE(fecha_visita)
         ORDER BY label"
    );

    $rows = [];
    while ($r = $res->fetch_assoc()) $rows[] = $r;

    return $rows;
}