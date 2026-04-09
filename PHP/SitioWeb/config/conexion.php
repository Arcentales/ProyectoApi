<?php
// ============================================================
//  BaseApp - Configuración de conexión
//  Ajusta host, puerto y credenciales según tu entorno
// ============================================================

define('DB_HOST',     'localhost');
define('DB_PORT',     '3307');          //  puerto MariaDB
define('DB_USER',     'root');
define('DB_PASS',     '170524');        //  contraseña
define('DB_NAME',     'baseapp');       // nombre BD
define('DB_CHARSET',  'utf8mb4');

function getConexion(): mysqli {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, (int)DB_PORT);
        if ($conn->connect_error) {
            http_response_code(500);
            die(json_encode(['error' => 'Error de conexión: ' . $conn->connect_error]));
        }
        $conn->set_charset(DB_CHARSET);
    }
    return $conn;
}
