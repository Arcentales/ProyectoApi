Aquí tienes un **README.md profesional** basado en tu script SQL 👇

---

# 📦 BaseApp - Sistema de Gestión de Clientes y Visitas

Sistema backend basado en **MySQL 8+** diseñado para integrarse con:

* 🌐 API REST (PHP)
* 📱 Aplicación móvil (Android / iOS)
* 📊 Reportes y análisis de datos

---

## 🚀 Características principales

* Gestión de clientes
* Registro de visitas de campo
* Control de usuarios y roles
* Estados de gestión comercial
* Catálogo de productos
* Autenticación con tokens (JWT)
* Reportes mediante vistas y procedimientos almacenados
* Auditoría automática con triggers
* Optimización con índices

---

## 🧱 Estructura de la Base de Datos

### 📌 Tablas principales

| Tabla       | Descripción                                  |
| ----------- | -------------------------------------------- |
| `usuario`   | Usuarios del sistema (admin, vendedor, etc.) |
| `cliente`   | Información de clientes                      |
| `visita`    | Registro de visitas comerciales              |
| `producto`  | Productos ofertados                          |
| `estado`    | Estado de gestión                            |
| `rol`       | Roles del sistema                            |
| `distrito`  | Distritos (Loreto)                           |
| `api_token` | Tokens para autenticación                    |

---

## 🔗 Relaciones clave

* Un **usuario** tiene un **rol**
* Un **cliente** pertenece a:

  * un distrito
  * un estado
* Una **visita** está relacionada con:

  * cliente
  * usuario
  * estado
  * producto (opcional)

---

## 👁️ Vistas (Views)

### 📊 `vw_visita_detalle`

Vista completa para consumo en app móvil:

* Cliente
* Distrito
* Producto
* Estado
* Vendedor
* Coordenadas GPS

### 📈 `vw_resumen_vendedor`

* Total de visitas
* Desembolsados
* Interesados
* Fechas de actividad

### 🌍 `vw_resumen_distrito`

* Total de clientes
* Total de visitas
* Desembolsos por zona

---

## ⚙️ Procedimientos almacenados

### 🔐 `sp_login`

Autenticación de usuario desde la API

### 📝 `sp_registrar_visita`

* Inserta visita
* Actualiza estado del cliente
* Maneja transacciones

### 📍 `sp_clientes_por_distrito`

* Listado paginado
* Ideal para app móvil

### 📅 `sp_reporte_periodo`

* Reporte por rango de fechas
* Filtro por usuario opcional

---

## 🧠 Funciones

* `fn_total_visitas_cliente` → Total de visitas por cliente
* `fn_ultimo_estado_cliente` → Último estado registrado

---

## 🔄 Triggers

* ✅ Validación: evita registrar visitas con usuarios inactivos
* 🧾 Auditoría: registra cambios de estado automáticamente

---

## ⏱️ Eventos programados

* `ev_revocar_tokens_expirados`

  * Se ejecuta cada hora
  * Revoca tokens expirados automáticamente

---

## 🔐 Seguridad

### 👤 Usuario API

Permisos limitados:

* SELECT / INSERT / UPDATE en tablas clave
* EXECUTE en procedimientos

### 📊 Usuario Reportes

* Solo lectura (SELECT)

---

## 🌱 Datos iniciales incluidos

* Roles:

  * Administrador
  * Supervisor
  * Vendedor
  * Reportes

* Distritos de Loreto

* Estados de gestión

* Productos

* Usuario administrador inicial

---

## 🧪 Ejemplos de consultas

Incluye consultas avanzadas como:

* Clientes interesados no visitados
* Ranking de vendedores
* Estadísticas por producto
* Datos geográficos para mapas

---

## ⚙️ Requisitos

* MySQL 8.0+
* Charset: `utf8mb4`
* Motor: `InnoDB`

---

## ▶️ Instalación

```bash
# Ejecutar el script en MySQL
mysql -u root -p < Mysql.base-datos..sql
```

---

## 📌 Notas importantes

* Reemplazar el `password_hash` del usuario admin
* Cambiar credenciales por seguridad
* Ajustar permisos según entorno (dev / producción)

---

## 🏗️ Arquitectura recomendada

```
[ App Móvil ] 
      ↓
   [ API REST - PHP ]
      ↓
   [ MySQL BaseApp ]
```

---

## 📄 Licencia

Uso académico / empresarial bajo responsabilidad del desarrollador.

---

el siguiente:

* ✅ Hacer el README específico para tu repo de GitHub
* ✅ Agregar badges (estado, versión, etc.)
* ✅ Integrarlo con tu proyecto Flutter 
* ✅ Generar endpoints de API basados en esta BD

