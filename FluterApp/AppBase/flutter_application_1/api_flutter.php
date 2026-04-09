<?php
// ============================================================
//  BaseApp API — Para App Flutter
//  Ruta: C:\xampp\htdocs\SitioWeb\api_flutter.php
//  Desde el emulador Android: http://10.0.2.2/SitioWeb/api_flutter.php
//  Desde dispositivo físico:  http://TU_IP_LOCAL/SitioWeb/api_flutter.php
// ============================================================

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ── CONFIG DB ──────────────────────────────────────────────
define('DB_HOST',    'localhost');
define('DB_PORT',    '3307');
define('DB_USER',    'root');
define('DB_PASS',    '170524');
define('DB_NAME',    'baseapp');

function db(): mysqli {
    static $c = null;
    if ($c === null) {
        $c = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, (int)DB_PORT);
        if ($c->connect_error) respError('DB Error: ' . $c->connect_error, 500);
        $c->set_charset('utf8mb4');
    }
    return $c;
}

// ── ROUTER ────────────────────────────────────────────────
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];
$input  = json_decode(file_get_contents('php://input'), true) ?? [];

try {
    switch ($action) {

        // ── AUTH ─────────────────────────────────────────
        case 'login':
            resp(login($input));
            break;

        // ── CLIENTES ─────────────────────────────────────
        case 'clientes':
            resp(listarClientes());
            break;

        case 'cliente.detalle':
            resp(detalleCliente((int)($_GET['id'] ?? 0)));
            break;

        case 'cliente.crear':
            resp(crearCliente($input));
            break;

        case 'cliente.actualizar':
            resp(actualizarCliente($input));
            break;

        case 'cliente.eliminar':
            resp(eliminarCliente((int)($input['idcliente'] ?? $_GET['id'] ?? 0)));
            break;

        case 'cliente.buscar':
            resp(buscarClientes($_GET['q'] ?? ''));
            break;

        // ── VISITAS ──────────────────────────────────────
        case 'visitas':
            resp(listarVisitas((int)($_GET['limit'] ?? 50)));
            break;

        case 'visitas.cliente':
            resp(visitasCliente((int)($_GET['id'] ?? 0)));
            break;

        case 'visita.registrar':
            resp(registrarVisita($input));
            break;

        // ── CATÁLOGOS ────────────────────────────────────
        case 'catalogos':
            resp(todosLosCatalogos());
            break;

        case 'distritos':
            resp(catalogo('distrito', 'iddistrito', 'nomdistrito'));
            break;

        case 'estados':
            resp(catalogo('estado', 'idestado', 'nomestado'));
            break;

        case 'productos':
            resp(catalogo('producto', 'idproducto', 'nomproducto'));
            break;

        // ── DASHBOARD ────────────────────────────────────
        case 'dashboard':
            resp(dashboard());
            break;

        // ── REPORTES ─────────────────────────────────────
        case 'reporte.estados':
            resp(reporteEstados());
            break;

        case 'reporte.distritos':
            resp(reporteDistritos());
            break;

        case 'reporte.vendedor':
            resp(reporteVendedor((int)($_GET['idusuario'] ?? 0)));
            break;

        default:
            respError("Acción desconocida: '$action'", 404);
    }
} catch (Throwable $e) {
    respError($e->getMessage(), 500);
}

// ============================================================
//  HELPERS DE RESPUESTA
// ============================================================
function resp($data, int $code = 200): void {
    http_response_code($code);
    echo json_encode([
        'success' => true,
        'data'    => $data,
        'ts'      => date('Y-m-d H:i:s'),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

function respError(string $msg, int $code = 400): void {
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'error'   => $msg,
        'ts'      => date('Y-m-d H:i:s'),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ============================================================
//  AUTH
// ============================================================
function login(array $d): array {
    $email = trim($d['email'] ?? '');
    $pass  = $d['password'] ?? '';
    if (!$email || !$pass) respError('Email y contraseña requeridos');

    $stmt = db()->prepare(
        "SELECT u.idusuario, u.nombres, u.apellidos, u.email,
                u.password_hash, u.activo, r.nomrol
         FROM usuario u JOIN rol r ON r.idrol = u.idrol
         WHERE u.email = ? LIMIT 1"
    );
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if (!$res) respError('Credenciales incorrectas', 401);
    if (!$res['activo']) respError('Usuario inactivo', 403);
    if (!password_verify($pass, $res['password_hash'])) respError('Credenciales incorrectas', 401);

    // Actualizar último login
    db()->query("UPDATE usuario SET ultimo_login=NOW() WHERE idusuario={$res['idusuario']}");

    unset($res['password_hash'], $res['activo']);
    $res['token'] = base64_encode($res['idusuario'] . ':' . md5($res['email'] . date('Y-m-d')));
    return $res;
}

// ============================================================
//  CLIENTES
// ============================================================
function listarClientes(): array {
    $sql = "SELECT c.idcliente, c.nombres, c.apellidos, c.dni,
                   c.telefono1, c.telefono2, c.direccion,
                   c.latitud, c.longitud, c.created_at, c.updated_at,
                   d.nomdistrito, e.nomestado, e.idestado, d.iddistrito
            FROM cliente c
            JOIN distrito d ON d.iddistrito = c.iddistrito
            JOIN estado   e ON e.idestado   = c.idestado
            ORDER BY c.updated_at DESC
            LIMIT 500";
    return fetchAll($sql);
}

function detalleCliente(int $id): array {
    if (!$id) respError('ID requerido');
    $stmt = db()->prepare(
        "SELECT c.*, d.nomdistrito, e.nomestado,
                fn_total_visitas_cliente(c.idcliente) AS total_visitas,
                fn_ultimo_estado_cliente(c.idcliente) AS ultimo_estado
         FROM cliente c
         JOIN distrito d ON d.iddistrito = c.iddistrito
         JOIN estado   e ON e.idestado   = c.idestado
         WHERE c.idcliente = ?"
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row) respError('Cliente no encontrado', 404);
    return $row;
}

function buscarClientes(string $q): array {
    if (strlen($q) < 2) return [];
    $like = "%$q%";
    $stmt = db()->prepare(
        "SELECT c.idcliente, c.nombres, c.apellidos, c.dni,
                c.telefono1, c.direccion, d.nomdistrito, e.nomestado
         FROM cliente c
         JOIN distrito d ON d.iddistrito = c.iddistrito
         JOIN estado   e ON e.idestado   = c.idestado
         WHERE c.nombres LIKE ? OR c.apellidos LIKE ? OR c.dni LIKE ?
         ORDER BY c.apellidos LIMIT 30"
    );
    $stmt->bind_param('sss', $like, $like, $like);
    $stmt->execute();
    return fetchResult($stmt->get_result());
}

function crearCliente(array $d): array {
    validar($d, ['nombres','apellidos','iddistrito','idestado']);
    $stmt = db()->prepare(
        "INSERT INTO cliente
         (iddistrito,idestado,dni,nombres,apellidos,telefono1,telefono2,
          direccion,referencia,latitud,longitud)
         VALUES (?,?,?,?,?,?,?,?,?,?,?)"
    );
    $lat = isset($d['latitud'])  ? (float)$d['latitud']  : null;
    $lng = isset($d['longitud']) ? (float)$d['longitud'] : null;
    $stmt->bind_param(
        'iisssssssdd',
        $d['iddistrito'], $d['idestado'],
        $d['dni'], $d['nombres'], $d['apellidos'],
        $d['telefono1'], $d['telefono2'],
        $d['direccion'], $d['referencia'],
        $lat, $lng
    );
    $stmt->execute();
    $id = db()->insert_id;
    return ['idcliente' => $id, 'mensaje' => 'Cliente creado correctamente'];
}

function actualizarCliente(array $d): array {
    validar($d, ['idcliente','nombres','apellidos','iddistrito','idestado']);
    $stmt = db()->prepare(
        "UPDATE cliente SET
         iddistrito=?,idestado=?,dni=?,nombres=?,apellidos=?,
         telefono1=?,telefono2=?,direccion=?,referencia=?,
         latitud=?,longitud=?
         WHERE idcliente=?"
    );
    $lat = isset($d['latitud'])  ? (float)$d['latitud']  : null;
    $lng = isset($d['longitud']) ? (float)$d['longitud'] : null;
    $stmt->bind_param(
        'iisssssssddi',
        $d['iddistrito'], $d['idestado'],
        $d['dni'], $d['nombres'], $d['apellidos'],
        $d['telefono1'], $d['telefono2'],
        $d['direccion'], $d['referencia'],
        $lat, $lng, $d['idcliente']
    );
    $stmt->execute();
    return ['mensaje' => 'Cliente actualizado correctamente'];
}

function eliminarCliente(int $id): array {
    if (!$id) respError('ID requerido');
    db()->query("DELETE FROM visita  WHERE idcliente = $id");
    db()->query("DELETE FROM cliente WHERE idcliente = $id");
    return ['mensaje' => 'Cliente eliminado'];
}

// ============================================================
//  VISITAS
// ============================================================
function listarVisitas(int $limit = 50): array {
    $sql = "SELECT * FROM vw_visita_detalle ORDER BY fecha_visita DESC LIMIT $limit";
    return fetchAll($sql);
}

function visitasCliente(int $id): array {
    if (!$id) respError('ID requerido');
    $stmt = db()->prepare("SELECT * FROM vw_visita_detalle WHERE idcliente=? ORDER BY fecha_visita DESC");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return fetchResult($stmt->get_result());
}

function registrarVisita(array $d): array {
    validar($d, ['idcliente','idusuario','idestado']);
    $idprod = isset($d['idproducto']) ? (int)$d['idproducto'] : null;
    $lat    = isset($d['latitud'])    ? (float)$d['latitud']  : null;
    $lng    = isset($d['longitud'])   ? (float)$d['longitud'] : null;
    $obs    = $d['observacion'] ?? null;

    $stmt = db()->prepare(
        "INSERT INTO visita (idcliente,idusuario,idestado,idproducto,observacion,latitud,longitud)
         VALUES (?,?,?,?,?,?,?)"
    );
    $stmt->bind_param('iiiisdd',
        $d['idcliente'], $d['idusuario'], $d['idestado'],
        $idprod, $obs, $lat, $lng
    );
    $stmt->execute();

    // Actualizar estado del cliente
    db()->query("UPDATE cliente SET idestado={$d['idestado']} WHERE idcliente={$d['idcliente']}");

    return ['idvisita' => db()->insert_id, 'mensaje' => 'Visita registrada correctamente'];
}

// ============================================================
//  CATÁLOGOS
// ============================================================
function todosLosCatalogos(): array {
    return [
        'distritos' => catalogo('distrito', 'iddistrito', 'nomdistrito'),
        'estados'   => catalogo('estado',   'idestado',   'nomestado'),
        'productos' => catalogo('producto', 'idproducto', 'nomproducto'),
    ];
}

function catalogo(string $tabla, string $campoId, string $campoNom): array {
    return fetchAll("SELECT $campoId AS id, $campoNom AS nombre FROM $tabla ORDER BY $campoNom");
}

// ============================================================
//  DASHBOARD
// ============================================================
function dashboard(): array {
    $db = db();
    $stats = [];
    foreach ([
        'total_clientes'  => "SELECT COUNT(*) FROM cliente",
        'total_visitas'   => "SELECT COUNT(*) FROM visita",
        'desembolsados'   => "SELECT COUNT(*) FROM visita WHERE idestado=3",
        'interesados'     => "SELECT COUNT(*) FROM cliente WHERE idestado=6",
        'visitas_hoy'     => "SELECT COUNT(*) FROM visita WHERE DATE(fecha_visita)=CURDATE()",
        'visitas_semana'  => "SELECT COUNT(*) FROM visita WHERE fecha_visita>=DATE_SUB(NOW(),INTERVAL 7 DAY)",
    ] as $key => $sql) {
        $stats[$key] = (int)$db->query($sql)->fetch_row()[0];
    }
    $stats['reporte_estados']   = reporteEstados();
    $stats['reporte_distritos'] = reporteDistritos();
    return $stats;
}

// ============================================================
//  REPORTES
// ============================================================
function reporteEstados(): array {
    return fetchAll(
        "SELECT e.nomestado AS label, COUNT(c.idcliente) AS valor
         FROM estado e LEFT JOIN cliente c ON c.idestado=e.idestado
         GROUP BY e.idestado ORDER BY valor DESC"
    );
}

function reporteDistritos(): array {
    return fetchAll(
        "SELECT d.nomdistrito AS label, COUNT(c.idcliente) AS valor
         FROM distrito d LEFT JOIN cliente c ON c.iddistrito=d.iddistrito
         GROUP BY d.iddistrito ORDER BY valor DESC LIMIT 10"
    );
}

function reporteVendedor(int $id): array {
    if (!$id) respError('idusuario requerido');
    $stmt = db()->prepare(
        "SELECT * FROM vw_visita_detalle WHERE vendedor=(
            SELECT CONCAT(nombres,' ',apellidos) FROM usuario WHERE idusuario=?
         ) ORDER BY fecha_visita DESC LIMIT 100"
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return fetchResult($stmt->get_result());
}

// ============================================================
//  UTILIDADES
// ============================================================
function fetchAll(string $sql): array {
    $res  = db()->query($sql);
    $rows = [];
    while ($r = $res->fetch_assoc()) $rows[] = $r;
    return $rows;
}

function fetchResult($result): array {
    $rows = [];
    while ($r = $result->fetch_assoc()) $rows[] = $r;
    return $rows;
}

function validar(array $d, array $campos): void {
    foreach ($campos as $campo) {
        if (empty($d[$campo])) respError("Campo requerido: $campo");
    }
}
