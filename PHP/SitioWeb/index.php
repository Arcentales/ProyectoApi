<?php
// ============================================================
//  BaseApp — Dashboard Principal
//  PHP 8.2 | MariaDB 10.4 | sin frameworks externos
// ============================================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SitioWeb — Panel de Gestión</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
/* ── RESET & BASE ─────────────────────────────────────────── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --navy:   #0f2744;
  --navy2:  #1a3558;
  --navy3:  #243f66;
  --ice:    #e8f0fb;
  --ice2:   #d0e0f5;
  --white:  #ffffff;
  --slate:  #f4f6fa;
  --text:   #1a2535;
  --muted:  #6b7a99;
  --border: #dde4f0;
  --green:  #17b26a;
  --green2: #d1fae5;
  --amber:  #f59e0b;
  --amber2: #fef3c7;
  --red:    #ef4444;
  --red2:   #fee2e2;
  --blue:   #3b82f6;
  --blue2:  #dbeafe;
  --purple: #7c3aed;
  --purple2:#ede9fe;
  --radius: 10px;
  --radius-lg: 16px;
  --shadow: 0 1px 3px rgba(15,39,68,.08), 0 4px 16px rgba(15,39,68,.06);
  --shadow-lg: 0 8px 32px rgba(15,39,68,.14);
  --sidebar: 220px;
  --topbar:  60px;
  --font: 'DM Sans', system-ui, sans-serif;
  --mono: 'DM Mono', monospace;
}
html,body{height:100%;font-family:var(--font);font-size:14px;color:var(--text);background:var(--slate)}
button{font-family:var(--font);cursor:pointer}
input,select,textarea{font-family:var(--font)}
a{text-decoration:none;color:inherit}

/* ── SIDEBAR ──────────────────────────────────────────────── */
#sidebar{
  position:fixed;top:0;left:0;width:var(--sidebar);height:100vh;
  background:var(--navy);display:flex;flex-direction:column;
  z-index:200;transition:transform .25s ease;
}
.sidebar-brand{padding:18px 20px;display:flex;align-items:center;gap:10px;border-bottom:1px solid rgba(255,255,255,.08)}
.sidebar-logo{width:34px;height:34px;background:var(--blue);border-radius:8px;display:grid;place-items:center}
.sidebar-logo svg{width:18px;height:18px;fill:none;stroke:#fff;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.sidebar-title{color:#fff;font-size:14px;font-weight:600;letter-spacing:.01em;line-height:1.2}
.sidebar-title span{display:block;font-size:10px;font-weight:400;color:rgba(255,255,255,.45);letter-spacing:.08em;text-transform:uppercase}
.sidebar-nav{flex:1;padding:12px 0;overflow-y:auto}
.nav-section{padding:6px 16px;font-size:10px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:rgba(255,255,255,.3);margin-top:8px}
.nav-item{display:flex;align-items:center;gap:10px;padding:9px 16px;color:rgba(255,255,255,.6);font-size:13px;font-weight:400;cursor:pointer;border-radius:0;transition:background .15s,color .15s;position:relative}
.nav-item:hover{background:rgba(255,255,255,.06);color:#fff}
.nav-item.active{background:rgba(59,130,246,.18);color:#fff}
.nav-item.active::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:var(--blue);border-radius:0 3px 3px 0}
.nav-item svg{width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;flex-shrink:0}
.sidebar-footer{padding:16px;border-top:1px solid rgba(255,255,255,.08)}
.user-pill{display:flex;align-items:center;gap:10px}
.user-avatar{width:32px;height:32px;border-radius:50%;background:var(--blue);display:grid;place-items:center;font-size:12px;font-weight:600;color:#fff;flex-shrink:0}
.user-info{color:rgba(255,255,255,.7);font-size:12px;line-height:1.35}
.user-info b{display:block;color:#fff;font-weight:500}

/* ── TOPBAR ───────────────────────────────────────────────── */
#topbar{
  position:fixed;top:0;left:var(--sidebar);right:0;height:var(--topbar);
  background:var(--white);border-bottom:1px solid var(--border);
  display:flex;align-items:center;padding:0 24px;gap:16px;z-index:100;
}
.topbar-title{font-size:15px;font-weight:600;color:var(--text);flex:1}
.topbar-search{display:flex;align-items:center;gap:8px;background:var(--slate);border:1px solid var(--border);border-radius:8px;padding:6px 12px;width:240px}
.topbar-search input{border:none;background:none;outline:none;font-size:13px;color:var(--text);width:100%}
.topbar-search input::placeholder{color:var(--muted)}
.topbar-search svg{width:14px;height:14px;stroke:var(--muted);fill:none;stroke-width:2;flex-shrink:0}
.topbar-actions{display:flex;align-items:center;gap:8px}
.btn-icon{width:34px;height:34px;border:1px solid var(--border);background:var(--white);border-radius:8px;display:grid;place-items:center;transition:background .15s}
.btn-icon:hover{background:var(--slate)}
.btn-icon svg{width:16px;height:16px;fill:none;stroke:var(--muted);stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.avatar-top{width:34px;height:34px;border-radius:50%;background:var(--navy);display:grid;place-items:center;font-size:12px;font-weight:600;color:#fff}
.badge-dot{position:relative}
.badge-dot::after{content:'';position:absolute;top:6px;right:6px;width:7px;height:7px;background:var(--red);border:2px solid var(--white);border-radius:50%}

/* ── MAIN CONTENT ─────────────────────────────────────────── */
#main{margin-left:var(--sidebar);padding-top:var(--topbar);min-height:100vh}
.page{display:none;padding:24px;animation:fadeIn .2s ease}
.page.active{display:block}
@keyframes fadeIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}

/* ── PAGE HEADER ──────────────────────────────────────────── */
.page-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px}
.page-header h1{font-size:20px;font-weight:600;color:var(--text)}
.page-header p{font-size:13px;color:var(--muted);margin-top:2px}
.page-actions{display:flex;gap:8px;align-items:center}

/* ── BUTTONS ──────────────────────────────────────────────── */
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:8px;font-size:13px;font-weight:500;border:1px solid transparent;transition:all .15s}
.btn svg{width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.btn-primary{background:var(--navy);color:#fff;border-color:var(--navy)}
.btn-primary:hover{background:var(--navy2)}
.btn-secondary{background:var(--white);color:var(--text);border-color:var(--border)}
.btn-secondary:hover{background:var(--slate)}
.btn-danger{background:var(--red);color:#fff;border-color:var(--red)}
.btn-danger:hover{opacity:.9}
.btn-sm{padding:5px 10px;font-size:12px}

/* ── STAT CARDS ───────────────────────────────────────────── */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:24px}
.stat-card{background:var(--white);border:1px solid var(--border);border-radius:var(--radius-lg);padding:18px 20px;position:relative;overflow:hidden}
.stat-card::before{content:'';position:absolute;top:0;right:0;width:60px;height:60px;border-radius:50%;opacity:.08;transform:translate(15px,-15px)}
.stat-card.green::before{background:var(--green)}
.stat-card.amber::before{background:var(--amber)}
.stat-card.blue::before{background:var(--blue)}
.stat-card.purple::before{background:var(--purple)}
.stat-card.red::before{background:var(--red)}
.stat-label{font-size:11px;font-weight:500;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px}
.stat-value{font-size:26px;font-weight:600;color:var(--text);line-height:1}
.stat-sub{font-size:11px;color:var(--muted);margin-top:6px}
.stat-icon{position:absolute;bottom:14px;right:14px;width:28px;height:28px;border-radius:7px;display:grid;place-items:center}
.stat-icon svg{width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.stat-card.green .stat-icon{background:var(--green2);color:var(--green)}
.stat-card.amber .stat-icon{background:var(--amber2);color:var(--amber)}
.stat-card.blue .stat-icon{background:var(--blue2);color:var(--blue)}
.stat-card.purple .stat-icon{background:var(--purple2);color:var(--purple)}
.stat-card.red .stat-icon{background:var(--red2);color:var(--red)}

/* ── CARDS / PANELS ───────────────────────────────────────── */
.card{background:var(--white);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden}
.card-header{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.card-header h2{font-size:14px;font-weight:600;color:var(--text)}
.card-header p{font-size:12px;color:var(--muted);margin-top:1px}
.card-body{padding:20px}

/* ── GRID HELPERS ─────────────────────────────────────────── */
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}

/* ── TABLE ────────────────────────────────────────────────── */
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:13px}
thead th{padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;border-bottom:1px solid var(--border);white-space:nowrap}
tbody tr{border-bottom:1px solid var(--border);transition:background .12s}
tbody tr:last-child{border-bottom:none}
tbody tr:hover{background:var(--slate)}
tbody td{padding:11px 14px;color:var(--text);vertical-align:middle}
.td-muted{color:var(--muted);font-size:12px}
.client-initial{width:30px;height:30px;border-radius:7px;display:inline-grid;place-items:center;font-size:11px;font-weight:600;color:var(--navy);background:var(--ice);flex-shrink:0}

/* ── STATUS BADGE ─────────────────────────────────────────── */
.badge{display:inline-flex;align-items:center;gap:5px;padding:3px 8px;border-radius:5px;font-size:11px;font-weight:500}
.badge::before{content:'';width:5px;height:5px;border-radius:50%;flex-shrink:0}
.badge-green{background:var(--green2);color:#0a6e4a}.badge-green::before{background:var(--green)}
.badge-amber{background:var(--amber2);color:#92400e}.badge-amber::before{background:var(--amber)}
.badge-red{background:var(--red2);color:#991b1b}.badge-red::before{background:var(--red)}
.badge-blue{background:var(--blue2);color:#1e40af}.badge-blue::before{background:var(--blue)}
.badge-purple{background:var(--purple2);color:#5b21b6}.badge-purple::before{background:var(--purple)}
.badge-gray{background:#f1f5f9;color:#475569}.badge-gray::before{background:#94a3b8}

/* ── FILTROS / SEARCH ─────────────────────────────────────── */
.filters{display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap;align-items:center}
.filter-input{padding:7px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;outline:none;background:var(--white);color:var(--text)}
.filter-input:focus{border-color:var(--blue)}
.filter-select{padding:7px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;outline:none;background:var(--white);color:var(--text);cursor:pointer}
.filters-right{margin-left:auto;display:flex;gap:8px}

/* ── MODAL ────────────────────────────────────────────────── */
.modal-bg{display:none;position:fixed;inset:0;background:rgba(15,39,68,.45);z-index:500;align-items:center;justify-content:center;padding:20px}
.modal-bg.open{display:flex}
.modal{background:var(--white);border-radius:var(--radius-lg);width:100%;max-width:560px;max-height:90vh;display:flex;flex-direction:column;box-shadow:var(--shadow-lg)}
.modal-header{padding:18px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.modal-header h3{font-size:15px;font-weight:600}
.modal-close{width:28px;height:28px;border:none;background:none;border-radius:6px;cursor:pointer;display:grid;place-items:center;color:var(--muted)}
.modal-close:hover{background:var(--slate)}
.modal-close svg{width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round}
.modal-body{padding:22px;overflow-y:auto;flex:1}
.modal-footer{padding:14px 22px;border-top:1px solid var(--border);display:flex;gap:8px;justify-content:flex-end}

/* ── FORM ─────────────────────────────────────────────────── */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.form-group{display:flex;flex-direction:column;gap:5px}
.form-group.full{grid-column:1/-1}
.form-label{font-size:12px;font-weight:500;color:var(--muted);text-transform:uppercase;letter-spacing:.06em}
.form-control{padding:8px 11px;border:1px solid var(--border);border-radius:8px;font-size:13px;outline:none;color:var(--text);background:var(--white);transition:border-color .15s}
.form-control:focus{border-color:var(--blue)}

/* ── CHART CONTAINER ──────────────────────────────────────── */
.chart-container{position:relative;height:220px}

/* ── BAR CHART (CSS only) ─────────────────────────────────── */
.bar-list{display:flex;flex-direction:column;gap:10px}
.bar-row{display:flex;align-items:center;gap:10px}
.bar-label{font-size:12px;color:var(--muted);width:130px;text-align:right;flex-shrink:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.bar-track{flex:1;height:8px;background:var(--slate);border-radius:4px;overflow:hidden}
.bar-fill{height:100%;border-radius:4px;background:var(--navy);transition:width .6s ease}
.bar-val{font-size:12px;color:var(--text);font-weight:500;width:36px;text-align:right;flex-shrink:0}

/* ── MINI CHART (sparkline) ──────────────────────────────── */
.sparkline{display:flex;align-items:flex-end;gap:3px;height:48px}
.spark-bar{flex:1;background:var(--navy);border-radius:2px 2px 0 0;opacity:.85;min-height:4px;transition:opacity .15s}
.spark-bar:hover{opacity:1}

/* ── TOAST ────────────────────────────────────────────────── */
#toast{position:fixed;bottom:24px;right:24px;z-index:1000;display:flex;flex-direction:column;gap:8px}
.toast-item{padding:12px 16px;border-radius:10px;font-size:13px;font-weight:500;box-shadow:var(--shadow-lg);animation:toastIn .25s ease;display:flex;align-items:center;gap:8px}
.toast-item.success{background:var(--navy);color:#fff}
.toast-item.error{background:var(--red);color:#fff}
@keyframes toastIn{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:none}}

/* ── EMPTY STATE ──────────────────────────────────────────── */
.empty-state{text-align:center;padding:48px 20px;color:var(--muted)}
.empty-state svg{width:40px;height:40px;stroke:var(--muted);fill:none;stroke-width:1.5;margin-bottom:12px;opacity:.5}
.empty-state p{font-size:14px;font-weight:500;margin-bottom:4px;color:var(--text)}
.empty-state span{font-size:12px}

/* ── LOADER ───────────────────────────────────────────────── */
.loader{display:inline-block;width:16px;height:16px;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:spin .6s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}

/* ── TABS ─────────────────────────────────────────────────── */
.tabs{display:flex;gap:0;border-bottom:1px solid var(--border);margin-bottom:20px}
.tab{padding:10px 16px;font-size:13px;font-weight:500;color:var(--muted);cursor:pointer;border-bottom:2px solid transparent;transition:all .15s;margin-bottom:-1px}
.tab:hover{color:var(--text)}
.tab.active{color:var(--navy);border-bottom-color:var(--navy)}

/* ── RESPONSIVE ───────────────────────────────────────────── */
@media(max-width:768px){
  #sidebar{transform:translateX(-100%)}
  #sidebar.open{transform:none}
  #main{margin-left:0}
  #topbar{left:0}
  .grid-2,.grid-3,.form-grid{grid-template-columns:1fr}
  .stats-grid{grid-template-columns:repeat(2,1fr)}
}
</style>
</head>
<body>

<!-- ── SIDEBAR ──────────────────────────────────────────── -->
<aside id="sidebar">
  <div class="sidebar-brand">
    <div class="sidebar-logo">
      <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
    </div>
    <div class="sidebar-title">SitioWeb<span>Enterprise DB</span></div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section">Principal</div>
    <div class="nav-item active" data-page="dashboard">
      <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>Dashboard
    </div>
    <div class="nav-section">Gestión</div>
    <div class="nav-item" data-page="clientes">
      <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>Clientes
    </div>
    <div class="nav-item" data-page="visitas">
      <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>Visitas
    </div>
    <div class="nav-item" data-page="reportes">
      <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>Reportes
    </div>
    <div class="nav-section">Sistema</div>
    <div class="nav-item" data-page="logs">
      <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>Logs
    </div>
  </nav>
  <div class="sidebar-footer">
    <div class="user-pill">
      <div class="user-avatar">AD</div>
      <div class="user-info"><b>Administrador</b>Sistema BaseApp</div>
    </div>
  </div>
</aside>

<!-- ── TOPBAR ────────────────────────────────────────────── -->
<header id="topbar">
  <div class="topbar-title" id="topbar-title">Dashboard</div>
  <div class="topbar-search">
    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input type="text" id="global-search" placeholder="Buscar cliente, DNI..." autocomplete="off">
  </div>
  <div class="topbar-actions">
    <button class="btn-icon badge-dot" title="Notificaciones">
      <svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
    </button>
    <button class="btn-icon" title="Configuración">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
    </button>
    <div class="avatar-top">AD</div>
  </div>
</header>

<!-- ── MAIN ──────────────────────────────────────────────── -->
<main id="main">

  <!-- ── DASHBOARD ─────────────────────────────────────── -->
  <section class="page active" id="page-dashboard">
    <div class="page-header">
      <div>
        <h1>Dashboard</h1>
        <p>Resumen general de la base de datos BaseApp</p>
      </div>
      <div class="page-actions">
        <button class="btn btn-secondary" onclick="loadDashboard()">
          <svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>Actualizar
        </button>
      </div>
    </div>

    <div class="stats-grid" id="stats-grid">
      <div class="stat-card blue"><div class="stat-label">Total Clientes</div><div class="stat-value" id="st-clientes">—</div><div class="stat-sub">en la base de datos</div><div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div></div>
      <div class="stat-card green"><div class="stat-label">Total Visitas</div><div class="stat-value" id="st-visitas">—</div><div class="stat-sub">gestiones registradas</div><div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div></div>
      <div class="stat-card amber"><div class="stat-label">Desembolsados</div><div class="stat-value" id="st-desem">—</div><div class="stat-sub">estado cerrado</div><div class="stat-icon"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div></div>
      <div class="stat-card purple"><div class="stat-label">Interesados</div><div class="stat-value" id="st-inter">—</div><div class="stat-sub">potenciales cierres</div><div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div></div>
      <div class="stat-card green"><div class="stat-label">Visitas Hoy</div><div class="stat-value" id="st-hoy">—</div><div class="stat-sub">del día actual</div><div class="stat-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div></div>
      <div class="stat-card amber"><div class="stat-label">Usuarios Activos</div><div class="stat-value" id="st-users">—</div><div class="stat-sub">vendedores en sistema</div><div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div></div>
    </div>

    <div class="grid-2" style="margin-bottom:16px">
      <div class="card">
        <div class="card-header"><div><h2>Clientes por Estado</h2><p>Distribución actual de la cartera</p></div></div>
        <div class="card-body"><div class="bar-list" id="chart-estados"></div></div>
      </div>
      <div class="card">
        <div class="card-header"><div><h2>Top 10 Distritos</h2><p>Clientes por distrito de Loreto</p></div></div>
        <div class="card-body"><div class="bar-list" id="chart-distritos"></div></div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><div><h2>Visitas últimos 30 días</h2><p>Actividad de campo</p></div></div>
      <div class="card-body">
        <div class="sparkline" id="chart-visitas-mes" style="height:60px"></div>
        <p class="td-muted" style="margin-top:8px;font-size:11px;text-align:right" id="chart-vis-info"></p>
      </div>
    </div>
  </section>

  <!-- ── CLIENTES ──────────────────────────────────────── -->
  <section class="page" id="page-clientes">
    <div class="page-header">
      <div><h1>Clientes</h1><p>Gestión completa de la cartera de clientes</p></div>
      <div class="page-actions">
        <button class="btn btn-primary" onclick="openModal()">
          <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>Nuevo Cliente
        </button>
      </div>
    </div>

    <div class="card">
      <div class="card-body" style="padding-bottom:0">
        <div class="filters">
          <input class="filter-input" id="f-search" placeholder="Buscar nombre, DNI..." oninput="filtrarClientes()" style="width:220px">
          <select class="filter-select" id="f-estado" onchange="filtrarClientes()"><option value="">Todos los estados</option></select>
          <select class="filter-select" id="f-distrito" onchange="filtrarClientes()"><option value="">Todos los distritos</option></select>
          <div class="filters-right">
            <button class="btn btn-secondary btn-sm" onclick="exportCSV()">
              <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>Exportar CSV
            </button>
          </div>
        </div>
      </div>
      <div class="table-wrap">
        <table id="tabla-clientes">
          <thead>
            <tr>
              <th>ID</th><th>Cliente</th><th>DNI</th><th>Teléfono</th>
              <th>Distrito</th><th>Estado</th><th>Registrado</th><th>Acciones</th>
            </tr>
          </thead>
          <tbody id="tbody-clientes">
            <tr><td colspan="8"><div class="empty-state"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><p>Cargando clientes…</p></div></td></tr>
          </tbody>
        </table>
      </div>
      <div style="padding:12px 20px;border-top:1px solid var(--border);font-size:12px;color:var(--muted)" id="tabla-info">Mostrando registros…</div>
    </div>
  </section>

  <!-- ── VISITAS ────────────────────────────────────────── -->
  <section class="page" id="page-visitas">
    <div class="page-header">
      <div><h1>Visitas de Campo</h1><p>Historial de gestiones y visitas registradas</p></div>
    </div>
    <div class="card">
      <div class="table-wrap">
        <table>
          <thead>
            <tr><th>#</th><th>Fecha</th><th>Cliente</th><th>Distrito</th><th>Producto</th><th>Estado</th><th>Vendedor</th></tr>
          </thead>
          <tbody id="tbody-visitas">
            <tr><td colspan="7"><div class="empty-state"><p>Cargando visitas…</p></div></td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- ── REPORTES ───────────────────────────────────────── -->
  <section class="page" id="page-reportes">
    <div class="page-header">
      <div><h1>Reportes</h1><p>Análisis estadístico de la base de datos</p></div>
    </div>
    <div class="tabs">
      <div class="tab active" onclick="switchTab(this,'rep-estados')">Por Estado</div>
      <div class="tab" onclick="switchTab(this,'rep-distritos')">Por Distrito</div>
      <div class="tab" onclick="switchTab(this,'rep-tendencia')">Tendencia</div>
    </div>
    <div id="rep-estados" class="card">
      <div class="card-header"><h2>Distribución por Estado de Gestión</h2></div>
      <div class="card-body"><div class="bar-list" id="rep-bar-estados"></div></div>
    </div>
    <div id="rep-distritos" class="card" style="display:none">
      <div class="card-header"><h2>Top 10 Distritos con más Clientes</h2></div>
      <div class="card-body"><div class="bar-list" id="rep-bar-distritos"></div></div>
    </div>
    <div id="rep-tendencia" class="card" style="display:none">
      <div class="card-header"><h2>Tendencia de Visitas (30 días)</h2></div>
      <div class="card-body">
        <div class="sparkline" id="rep-spark" style="height:80px"></div>
        <p class="td-muted" id="rep-spark-info" style="margin-top:8px;font-size:11px;text-align:right"></p>
      </div>
    </div>
  </section>

  <!-- ── LOGS ──────────────────────────────────────────── -->
  <section class="page" id="page-logs">
    <div class="page-header"><div><h1>Logs del Sistema</h1><p>Actividad y consultas SQL recientes</p></div></div>
    <div class="card">
      <div class="card-body">
        <div id="log-list" style="display:flex;flex-direction:column;gap:6px"></div>
      </div>
    </div>
  </section>

</main>

<!-- ── MODAL CLIENTE ─────────────────────────────────────── -->
<div class="modal-bg" id="modal-cliente">
  <div class="modal">
    <div class="modal-header">
      <h3 id="modal-title">Nuevo Cliente</h3>
      <button class="modal-close" onclick="closeModal()"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="modal-body">
      <div class="form-grid">
        <input type="hidden" id="f-idcliente">
        <div class="form-group"><label class="form-label">Nombres *</label><input class="form-control" id="f-nombres" placeholder="Nombre(s)"></div>
        <div class="form-group"><label class="form-label">Apellidos *</label><input class="form-control" id="f-apellidos" placeholder="Apellido(s)"></div>
        <div class="form-group"><label class="form-label">DNI</label><input class="form-control" id="f-dni" placeholder="12345678" maxlength="8"></div>
        <div class="form-group"><label class="form-label">Teléfono 1</label><input class="form-control" id="f-tel1" placeholder="999 999 999"></div>
        <div class="form-group"><label class="form-label">Teléfono 2</label><input class="form-control" id="f-tel2" placeholder="Opcional"></div>
        <div class="form-group"><label class="form-label">Distrito *</label>
          <select class="form-control" id="f-iddistrito"><option value="">Seleccionar…</option></select></div>
        <div class="form-group"><label class="form-label">Estado *</label>
          <select class="form-control" id="f-idestado"><option value="">Seleccionar…</option></select></div>
        <div class="form-group full"><label class="form-label">Dirección</label><input class="form-control" id="f-direccion" placeholder="Dirección completa"></div>
        <div class="form-group full"><label class="form-label">Referencia</label><input class="form-control" id="f-referencia" placeholder="Referencia del lugar"></div>
        <div class="form-group"><label class="form-label">Latitud</label><input class="form-control" id="f-latitud" placeholder="-3.7437…" type="number" step="any"></div>
        <div class="form-group"><label class="form-label">Longitud</label><input class="form-control" id="f-longitud" placeholder="-73.2516…" type="number" step="any"></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
      <button class="btn btn-primary" id="btn-guardar" onclick="guardarCliente()">
        <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2"><polyline points="20 6 9 17 4 12"/></svg>Guardar
      </button>
    </div>
  </div>
</div>

<!-- ── TOAST ─────────────────────────────────────────────── -->
<div id="toast"></div>

<!-- ── SCRIPT ────────────────────────────────────────────── -->
<script>
// ── CONFIG ────────────────────────────────────────────────
const API = 'api.php';

// ── STATE ─────────────────────────────────────────────────
let clientes = [];
let catalogos = { distritos: [], estados: [], productos: [] };
let logs = [];

// ── NAVIGATION ────────────────────────────────────────────
document.querySelectorAll('.nav-item').forEach(item => {
  item.addEventListener('click', () => {
    const page = item.dataset.page;
    navigate(page);
  });
});

function navigate(page) {
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.querySelector(`.nav-item[data-page="${page}"]`)?.classList.add('active');
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.getElementById(`page-${page}`)?.classList.add('active');
  const titles = { dashboard: 'Dashboard', clientes: 'Clientes', visitas: 'Visitas', reportes: 'Reportes', logs: 'Logs' };
  document.getElementById('topbar-title').textContent = titles[page] || page;

  if (page === 'clientes' && clientes.length === 0) loadClientes();
  if (page === 'visitas') loadVisitas();
  if (page === 'reportes') loadReportes();
  if (page === 'logs') renderLogs();
}

// ── API FETCH ─────────────────────────────────────────────
async function api(action, method = 'GET', body = null) {
  const opts = { method, headers: { 'Content-Type': 'application/json' } };
  if (body) opts.body = JSON.stringify(body);
  const res = await fetch(`${API}?action=${action}`, opts);
  const data = await res.json();
  addLog(action, method, res.status);
  if (data.error) throw new Error(data.error);
  return data;
}

// ── DASHBOARD ─────────────────────────────────────────────
async function loadDashboard() {
  try {
    const [stats, estados, distritos, mes] = await Promise.all([
      api('dashboard.stats'),
      api('reporte.estados'),
      api('reporte.distritos'),
      api('reporte.visitas_mes'),
    ]);
    document.getElementById('st-clientes').textContent = stats.total_clientes.toLocaleString();
    document.getElementById('st-visitas').textContent  = stats.total_visitas.toLocaleString();
    document.getElementById('st-desem').textContent    = stats.desembolsados.toLocaleString();
    document.getElementById('st-inter').textContent    = stats.interesados.toLocaleString();
    document.getElementById('st-hoy').textContent      = stats.visitas_hoy.toLocaleString();
    document.getElementById('st-users').textContent    = stats.usuarios_activos.toLocaleString();
    renderBarList('chart-estados', estados);
    renderBarList('chart-distritos', distritos);
    renderSparkline('chart-visitas-mes', mes, 'chart-vis-info');
  } catch (e) { toast('Error cargando dashboard: ' + e.message, 'error'); }
}

// ── CLIENTES ──────────────────────────────────────────────
async function loadClientes() {
  try {
    clientes = await api('clientes.listar');
    renderClientes(clientes);
  } catch (e) { toast('Error cargando clientes: ' + e.message, 'error'); }
}

function renderClientes(data) {
  const tbody = document.getElementById('tbody-clientes');
  document.getElementById('tabla-info').textContent = `Mostrando ${data.length} registro(s)`;
  if (!data.length) {
    tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state">
      <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
      <p>Sin clientes registrados</p><span>Agrega el primer cliente con el botón "Nuevo Cliente"</span></div></td></tr>`;
    return;
  }
  const colors = ['#1a3558','#17b26a','#7c3aed','#f59e0b','#3b82f6','#ef4444'];
  tbody.innerHTML = data.map((c, i) => {
    const ini = (c.nombres[0] || '') + (c.apellidos[0] || '');
    const color = colors[i % colors.length];
    const badge = badgeEstado(c.nomestado);
    const fecha = c.created_at ? c.created_at.split(' ')[0] : '—';
    return `<tr>
      <td class="td-muted" style="font-family:var(--mono);font-size:11px">#${c.idcliente}</td>
      <td><div style="display:flex;align-items:center;gap:10px">
        <div class="client-initial" style="background:${color}22;color:${color}">${ini}</div>
        <div><div style="font-weight:500">${esc(c.nombres)} ${esc(c.apellidos)}</div>
        <div class="td-muted">${c.direccion ? esc(c.direccion).substring(0,40)+'…' : '—'}</div></div></div></td>
      <td class="td-muted" style="font-family:var(--mono)">${c.dni || '—'}</td>
      <td>${c.telefono1 || '—'}</td>
      <td><span class="badge badge-gray">${esc(c.nomdistrito)}</span></td>
      <td>${badge}</td>
      <td class="td-muted">${fecha}</td>
      <td>
        <button class="btn btn-secondary btn-sm" onclick="editarCliente(${c.idcliente})">Editar</button>
        <button class="btn btn-danger btn-sm" onclick="eliminarCliente(${c.idcliente},'${esc(c.nombres)} ${esc(c.apellidos)}')" style="margin-left:4px">Borrar</button>
      </td>
    </tr>`;
  }).join('');
}

function badgeEstado(nom) {
  const map = {
    'Desembolsado':        'badge-green',
    'Interesado':          'badge-blue',
    'Volver a visitar':    'badge-amber',
    'No desea oferta':     'badge-red',
    'Falleció':            'badge-red',
    'Sin gestión':         'badge-gray',
    'Teléfonos errados':   'badge-purple',
    'No se encontró dirección': 'badge-amber',
  };
  const cls = map[nom] || 'badge-gray';
  return `<span class="badge ${cls}">${esc(nom)}</span>`;
}

function filtrarClientes() {
  const q    = document.getElementById('f-search').value.toLowerCase();
  const est  = document.getElementById('f-estado').value;
  const dist = document.getElementById('f-distrito').value;
  const filtrado = clientes.filter(c => {
    const nombre = `${c.nombres} ${c.apellidos} ${c.dni || ''}`.toLowerCase();
    const okQ    = !q    || nombre.includes(q);
    const okE    = !est  || c.nomestado === est;
    const okD    = !dist || c.nomdistrito === dist;
    return okQ && okE && okD;
  });
  renderClientes(filtrado);
}

// ── MODAL CLIENTE ─────────────────────────────────────────
function openModal(cliente = null) {
  document.getElementById('modal-title').textContent = cliente ? 'Editar Cliente' : 'Nuevo Cliente';
  const ids = ['idcliente','nombres','apellidos','dni','tel1','tel2','iddistrito','idestado','direccion','referencia','latitud','longitud'];
  ids.forEach(id => { const el = document.getElementById('f-'+id); if (el) el.value = ''; });
  if (cliente) {
    document.getElementById('f-idcliente').value   = cliente.idcliente;
    document.getElementById('f-nombres').value     = cliente.nombres;
    document.getElementById('f-apellidos').value   = cliente.apellidos;
    document.getElementById('f-dni').value         = cliente.dni || '';
    document.getElementById('f-tel1').value        = cliente.telefono1 || '';
    document.getElementById('f-tel2').value        = cliente.telefono2 || '';
    document.getElementById('f-iddistrito').value  = cliente.iddistrito || '';
    document.getElementById('f-idestado').value    = cliente.idestado || '';
    document.getElementById('f-direccion').value   = cliente.direccion || '';
    document.getElementById('f-referencia').value  = cliente.referencia || '';
    document.getElementById('f-latitud').value     = cliente.latitud || '';
    document.getElementById('f-longitud').value    = cliente.longitud || '';
  }
  document.getElementById('modal-cliente').classList.add('open');
}
function closeModal() { document.getElementById('modal-cliente').classList.remove('open'); }

function editarCliente(id) {
  const c = clientes.find(x => x.idcliente == id);
  if (c) openModal(c);
}

async function guardarCliente() {
  const id = document.getElementById('f-idcliente').value;
  const payload = {
    idcliente:   id ? parseInt(id) : null,
    nombres:     document.getElementById('f-nombres').value.trim(),
    apellidos:   document.getElementById('f-apellidos').value.trim(),
    dni:         document.getElementById('f-dni').value.trim(),
    telefono1:   document.getElementById('f-tel1').value.trim(),
    telefono2:   document.getElementById('f-tel2').value.trim(),
    iddistrito:  parseInt(document.getElementById('f-iddistrito').value),
    idestado:    parseInt(document.getElementById('f-idestado').value),
    direccion:   document.getElementById('f-direccion').value.trim(),
    referencia:  document.getElementById('f-referencia').value.trim(),
    latitud:     parseFloat(document.getElementById('f-latitud').value) || null,
    longitud:    parseFloat(document.getElementById('f-longitud').value) || null,
  };
  if (!payload.nombres || !payload.apellidos || !payload.iddistrito || !payload.idestado) {
    toast('Completa los campos obligatorios (*)', 'error'); return;
  }
  const btn = document.getElementById('btn-guardar');
  btn.innerHTML = '<span class="loader"></span> Guardando…';
  btn.disabled = true;
  try {
    const action = id ? 'clientes.actualizar' : 'clientes.crear';
    const res = await api(action, 'POST', payload);
    toast(res.mensaje, 'success');
    closeModal();
    clientes = [];
    loadClientes();
  } catch (e) { toast(e.message, 'error'); }
  finally { btn.innerHTML = '<svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2"><polyline points="20 6 9 17 4 12"/></svg>Guardar'; btn.disabled = false; }
}

async function eliminarCliente(id, nombre) {
  if (!confirm(`¿Eliminar a "${nombre}"? Esta acción no se puede deshacer.`)) return;
  try {
    const res = await api('clientes.eliminar', 'DELETE', { idcliente: id });
    toast(res.mensaje, 'success');
    clientes = [];
    loadClientes();
  } catch (e) { toast(e.message, 'error'); }
}

// ── VISITAS ───────────────────────────────────────────────
async function loadVisitas() {
  const tbody = document.getElementById('tbody-visitas');
  tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><p>Cargando…</p></div></td></tr>`;
  try {
    const data = await api('visitas.listar');
    if (!data.length) {
      tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><p>No hay visitas registradas</p></div></td></tr>`;
      return;
    }
    tbody.innerHTML = data.map(v => `<tr>
      <td class="td-muted" style="font-family:var(--mono);font-size:11px">#${v.idvisita}</td>
      <td class="td-muted">${v.fecha_visita ? v.fecha_visita.split(' ')[0] : '—'}</td>
      <td><div style="font-weight:500">${esc(v.cliente || '—')}</div><div class="td-muted">${v.dni || ''}</div></td>
      <td>${esc(v.distrito || '—')}</td>
      <td>${v.producto ? `<span class="badge badge-blue">${esc(v.producto)}</span>` : '<span class="td-muted">—</span>'}</td>
      <td>${badgeEstado(v.estado || '—')}</td>
      <td class="td-muted">${esc(v.vendedor || '—')}</td>
    </tr>`).join('');
  } catch (e) { toast('Error cargando visitas: ' + e.message, 'error'); }
}

// ── REPORTES ──────────────────────────────────────────────
async function loadReportes() {
  try {
    const [estados, distritos, mes] = await Promise.all([
      api('reporte.estados'),
      api('reporte.distritos'),
      api('reporte.visitas_mes'),
    ]);
    renderBarList('rep-bar-estados', estados);
    renderBarList('rep-bar-distritos', distritos);
    renderSparkline('rep-spark', mes, 'rep-spark-info');
  } catch (e) { toast('Error cargando reportes: ' + e.message, 'error'); }
}

function switchTab(tab, sectionId) {
  document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
  tab.classList.add('active');
  ['rep-estados','rep-distritos','rep-tendencia'].forEach(id => {
    document.getElementById(id).style.display = (id === sectionId) ? '' : 'none';
  });
}

// ── CHART HELPERS ─────────────────────────────────────────
function renderBarList(containerId, data) {
  const el = document.getElementById(containerId);
  if (!data || !data.length) { el.innerHTML = `<p class="td-muted">Sin datos</p>`; return; }
  const max = Math.max(...data.map(d => parseInt(d.valor) || 0));
  el.innerHTML = data.map(d => {
    const pct = max > 0 ? Math.round((d.valor / max) * 100) : 0;
    return `<div class="bar-row">
      <span class="bar-label" title="${esc(d.label)}">${esc(d.label)}</span>
      <div class="bar-track"><div class="bar-fill" style="width:${pct}%"></div></div>
      <span class="bar-val">${parseInt(d.valor).toLocaleString()}</span>
    </div>`;
  }).join('');
}

function renderSparkline(containerId, data, infoId) {
  const el = document.getElementById(containerId);
  if (!data || !data.length) { el.innerHTML = `<p class="td-muted">Sin datos en los últimos 30 días</p>`; return; }
  const max = Math.max(...data.map(d => parseInt(d.valor) || 0));
  el.innerHTML = data.map(d => {
    const h = max > 0 ? Math.max(4, Math.round((d.valor / max) * 100)) : 4;
    return `<div class="spark-bar" style="height:${h}%" title="${d.label}: ${d.valor}"></div>`;
  }).join('');
  if (infoId) {
    const total = data.reduce((s, d) => s + (parseInt(d.valor) || 0), 0);
    document.getElementById(infoId).textContent = `Total: ${total.toLocaleString()} visitas en ${data.length} días`;
  }
}

// ── CATÁLOGOS ─────────────────────────────────────────────
async function loadCatalogos() {
  const [dist, est, prod] = await Promise.all([
    api('distritos'), api('estados'), api('productos')
  ]);
  catalogos.distritos = dist;
  catalogos.estados   = est;
  catalogos.productos = prod;

  // Filtros
  const fDist = document.getElementById('f-distrito');
  dist.forEach(d => fDist.innerHTML += `<option value="${esc(d.nombre)}">${esc(d.nombre)}</option>`);
  const fEst = document.getElementById('f-estado');
  est.forEach(e => fEst.innerHTML += `<option value="${esc(e.nombre)}">${esc(e.nombre)}</option>`);

  // Modal
  const mDist = document.getElementById('f-iddistrito');
  dist.forEach(d => mDist.innerHTML += `<option value="${d.id}">${esc(d.nombre)}</option>`);
  const mEst = document.getElementById('f-idestado');
  est.forEach(e => mEst.innerHTML += `<option value="${e.id}">${esc(e.nombre)}</option>`);
}

// ── GLOBAL SEARCH ─────────────────────────────────────────
document.getElementById('global-search').addEventListener('input', function() {
  const q = this.value.toLowerCase();
  if (q.length >= 2) {
    navigate('clientes');
    document.getElementById('f-search').value = q;
    filtrarClientes();
  }
});

// ── EXPORT CSV ────────────────────────────────────────────
function exportCSV() {
  const q    = document.getElementById('f-search').value.toLowerCase();
  const est  = document.getElementById('f-estado').value;
  const dist = document.getElementById('f-distrito').value;
  let data = clientes.filter(c => {
    const n = `${c.nombres} ${c.apellidos} ${c.dni || ''}`.toLowerCase();
    return (!q || n.includes(q)) && (!est || c.nomestado === est) && (!dist || c.nomdistrito === dist);
  });
  const rows = [['ID','Nombres','Apellidos','DNI','Teléfono','Dirección','Distrito','Estado','Registrado']];
  data.forEach(c => rows.push([c.idcliente, c.nombres, c.apellidos, c.dni||'', c.telefono1||'', c.direccion||'', c.nomdistrito, c.nomestado, (c.created_at||'').split(' ')[0]]));
  const csv = rows.map(r => r.map(v => `"${String(v).replace(/"/g,'""')}"`).join(',')).join('\n');
  const blob = new Blob(['\ufeff'+csv], { type: 'text/csv;charset=utf-8' });
  const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = 'clientes_baseapp.csv'; a.click();
  toast('CSV exportado correctamente', 'success');
}

// ── LOGS ──────────────────────────────────────────────────
function addLog(action, method, status) {
  logs.unshift({ time: new Date().toLocaleTimeString(), action, method, status });
  if (logs.length > 50) logs.pop();
}
function renderLogs() {
  const el = document.getElementById('log-list');
  if (!logs.length) { el.innerHTML = `<p class="td-muted">Sin actividad registrada aún.</p>`; return; }
  el.innerHTML = logs.map(l => {
    const ok = l.status < 400;
    return `<div style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:7px;background:var(--slate);font-size:12px">
      <span style="color:var(--muted);font-family:var(--mono);font-size:11px">${l.time}</span>
      <span class="badge ${ok ? 'badge-green' : 'badge-red'}">${l.status}</span>
      <span style="font-family:var(--mono);font-size:11px;color:var(--muted)">${l.method}</span>
      <span style="font-weight:500">${l.action}</span>
    </div>`;
  }).join('');
}

// ── TOAST ─────────────────────────────────────────────────
function toast(msg, type = 'success') {
  const el = document.createElement('div');
  el.className = `toast-item ${type}`;
  el.innerHTML = `<svg style="width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2;flex-shrink:0" viewBox="0 0 24 24">${type === 'success' ? '<polyline points="20 6 9 17 4 12"/>' : '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>'}</svg>${msg}`;
  document.getElementById('toast').appendChild(el);
  setTimeout(() => el.remove(), 3500);
}

// ── HELPERS ───────────────────────────────────────────────
function esc(str) {
  if (!str) return '';
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── INIT ──────────────────────────────────────────────────
(async function init() {
  await loadCatalogos();
  await loadDashboard();
})();
</script>
</body>
</html>
