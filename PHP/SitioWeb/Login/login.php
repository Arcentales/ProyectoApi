<?php
session_start();
header('Content-Type: application/json');

require_once "../config/conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(["status" => "error", "msg" => "Campos vacíos"]);
    exit;
}

$conn = getConexion();

// 🔐 Consulta segura
$stmt = $conn->prepare("SELECT idusuario, nombres, password_hash 
                        FROM usuario 
                        WHERE email = ? AND activo = 1");

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error"]);
    exit;
}

$user = $result->fetch_assoc();

// 🔐 Verificar contraseña
if (!password_verify($password, $user['password_hash'])) {
    echo json_encode(["status" => "error"]);
    exit;
}

// ✅ Crear sesión
$_SESSION['usuario'] = [
    "id" => $user['idusuario'],
    "nombre" => $user['nombres']
];

echo json_encode([
    "status" => "ok",
    "usuario" => $user['nombres']
]);