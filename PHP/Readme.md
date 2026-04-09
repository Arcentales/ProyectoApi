SitioWeb
├── index.php                ✅ Actualizado (redirige al dashboard)
├── api.php                  ✅ Ya tenías (sin cambios)
├── ver_tabla.php            ✅ Ya tenías (sin cambios)
│
├── config
│   ├── conexion.php         ✅ Ya tenías
│   ├── database.php         🆕 Conexión PDO profesional + compatibilidad mysqli
│   └── config.php           🆕 Constantes, sesión, helpers globales
│
├── includes
│   ├── header.php           🆕 HTML head + Bootstrap
│   ├── navbar.php           🆕 Barra de navegación completa con dropdown
│   └── footer.php           🆕 Pie de página + Bootstrap JS
│
├── views
│   ├── login.php            🆕 Login con validación real y bcrypt
│   ├── register.php         🆕 Registro (solo admin)
│   ├── home.php             🆕 Página de bienvenida
│   ├── dashboard.php        🆕 Stats + gráficas + tabla visitas
│   └── reportes.php         🆕 Tabs estados, distritos, tendencia
│
├── controllers
│   ├── UserController.php   🆕 Login, logout, crear, toggle, cambiar pass
│   └── EventController.php  🆕 Listar, registrar, eliminar visitas
│
├── models
│   ├── User.php             🆕 Clase completa con PDO
│   └── Event.php            🆕 Clase para visitas con SP
│
├── routes
│   └── web.php              🆕 Router central page=X
│
└── public
    ├── cssstyle.css        🆕 Estilos completos (Bootstrap extend)
    └── jsmain.js           🆕 apiGetPost, toast, exportCSV, badges