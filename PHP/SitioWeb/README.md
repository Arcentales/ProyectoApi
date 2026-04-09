# BaseApp — Sitio Web PHP + MySQL
## Instrucciones de instalación

### Requisitos
- XAMPP / WAMP / Laragon (PHP 8.0+, MariaDB 10.4+)
- La base de datos `baseapp` ya creada (usa el script `baseapp.sql`)

---

### Pasos

**1. Copiar archivos**
Pega la carpeta `baseapp_web/` dentro de tu servidor web:
- XAMPP → `C:/xampp/htdocs/baseapp_web/`
- WAMP  → `C:/wamp64/www/baseapp_web/`

**2. Configurar conexión**
Edita `config/conexion.php`:
```php
define('DB_HOST', 'localhost');
define('DB_PORT', '3307');      // tu puerto (XAMPP suele ser 3306, WAMP 3307)
define('DB_USER', 'root');
define('DB_PASS', '170524');    // tu contraseña
define('DB_NAME', 'baseapp');
```

**3. Abrir en el navegador**
```
http://localhost/baseapp_web/
```

---

### Estructura de archivos
```
baseapp_web/
├── index.php          ← Dashboard principal (UI completa)
├── api.php            ← API REST (todos los endpoints)
├── config/
│   └── conexion.php   ← Configuración de base de datos
└── README.md
```

---

### Endpoints de la API (`api.php?action=...`)

| Acción | Método | Descripción |
|--------|--------|-------------|
| `clientes.listar` | GET | Lista todos los clientes |
| `clientes.crear` | POST | Crea un cliente nuevo |
| `clientes.actualizar` | POST | Actualiza datos del cliente |
| `clientes.eliminar` | DELETE | Elimina un cliente |
| `visitas.listar` | GET | Lista últimas 100 visitas |
| `distritos` | GET | Catálogo de distritos |
| `estados` | GET | Catálogo de estados |
| `productos` | GET | Catálogo de productos |
| `dashboard.stats` | GET | Estadísticas generales |
| `reporte.estados` | GET | Clientes agrupados por estado |
| `reporte.distritos` | GET | Top 10 distritos |
| `reporte.visitas_mes` | GET | Visitas últimos 30 días |

---

### Desde la app móvil (Flutter/React Native)
Usa la misma `api.php` con peticiones HTTP:
```javascript
// Ejemplo fetch desde la app
const res = await fetch('http://TU_IP/baseapp_web/api.php?action=clientes.listar');
const clientes = await res.json();
```
