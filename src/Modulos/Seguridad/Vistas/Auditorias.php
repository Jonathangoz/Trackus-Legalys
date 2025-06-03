<?php
  session_start();

  if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
      header("Location: index.html");
      exit;
  }

  if($_SESSION['tipo_rol'] !== 'ADMIN'){
    header("Location: logging.php");
    session_destroy();
    session_unset();
    exit;
  }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Auditorías - Cobro Coactivo SENA</title>
    <!-- Favicon -->
    <link rel="icon" href="img/favicon.ico" type="image/x-icon"/>
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="CSS/Auditorias.css">
    <!-- fontawesome -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
</head>
<body>
    <!-- Botón de volver arriba -->
    <button class="top-button" type="button" id="backToTopBtn" title="Volver arriba">
        <i class="fa-solid fa-arrow-up fa-lg" aria-hidden="true"></i>
    </button>

    <header>
        <div class="logo-container">
            <img src="img/sena.blanco.png" class="img-sena" alt="Logo SENA">            
            <p>SENA Regional Santander</p>
        </div>
        <div class="title-container">
            <h1>AUDITORIA</h1>
        </div>       
        <div class="user-home">
            <div class="home">
                <a href="dashboard.php" style="text-decoration: none;"><i class="fa-solid fa-house fa-lg" style="color: white;"></i>
                    <div>
                        <span class="text" style="color: white;">Dashboard</span>
                    </div>
                </a>
            </div>
            <div class="dropdown" id="dropdownContainer">
                <input type="checkbox" id="userToggle" hidden>
                <label for="userToggle" class="dropdown-toggle">
                    <span class="user-name">Bienvenido, Admin</span>
                    <div class="avatar">
                        <i class="fa-solid fa-user fa-lg" style="margin-right: 6px;"></i>
                        <i class="fa-solid fa-caret-down" style="margin-right: 6px;"></i>
                    </div>
                </label>
                <ul class="dropdown-menu">
                    <li><a href="#" class="drop"><i class="fa-solid fa-user me-2" style="margin-right: 15px;"></i>Perfil</a></li>
                    <li><a href="#" class="drop"><i class="fa-solid fa-gear me-2" style="margin-right: 15px;"></i>Configuración</a></li>
                    <li><hr></li>
                    <li><a href="module_login/logout.php" class="sesion"><i class="fa-solid fa-right-from-bracket me-2" style="margin-right: 15px;"></i>Cerrar sesión</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="header">
        <h1>Sistema de Auditorías - Cobro Coactivo SENA</h1>
        <div class="subtitle">Panel de Control y Seguimiento de Procesos</div>
    </div>

    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" id="totalProcesos">245</div>
                <div class="stat-label">Procesos Activos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="procesosHoy">18</div>
                <div class="stat-label">Actividades Hoy</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="montoTotal">$2.5M</div>
                <div class="stat-label">Monto en Cobranza</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="alertasActivas">7</div>
                <div class="stat-label">Alertas Activas</div>
            </div>
        </div>

        <div class="dashboard">
            <div class="card">
                <div class="card-header">
                    📝 Registro de Auditoría
                </div>
                <div class="card-content">
                    <form id="auditForm">
                        <div class="form-group">
                            <label for="tipoEvento">Tipo de Evento</label>
                            <select class="form-control" id="tipoEvento" required>
                                <option value="">Seleccionar...</option>
                                <option value="acceso">Acceso al Sistema</option>
                                <option value="consulta">Consulta de Expediente</option>
                                <option value="modificacion">Modificación de Datos</option>
                                <option value="notificacion">Envío de Notificación</option>
                                <option value="pago">Registro de Pago</option>
                                <option value="embargo">Proceso de Embargo</option>
                                <option value="suspension">Suspensión de Proceso</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="usuario">Usuario</label>
                            <input type="text" class="form-control" id="usuario" placeholder="ID del usuario" required>
                        </div>
                        <div class="form-group">
                            <label for="expediente">Número de Expediente</label>
                            <input type="text" class="form-control" id="expediente" placeholder="CC-2024-001">
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea class="form-control" id="descripcion" rows="3" placeholder="Detalles del evento..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Registrar Evento</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    🔍 Búsqueda y Filtros
                </div>
                <div class="card-content">
                    <div class="search-box">
                        <span class="search-icon">🔍</span>
                        <input type="text" class="form-control" id="buscarAuditoria" placeholder="Buscar en auditorías...">
                    </div>
                    <div class="form-group">
                        <label for="fechaInicio">Rango de Fechas</label>
                        <input type="date" class="form-control" id="fechaInicio" style="margin-bottom: 0.5rem;">
                        <input type="date" class="form-control" id="fechaFin">
                    </div>
                    <div class="form-group">
                        <label for="filtroUsuario">Usuario</label>
                        <select class="form-control" id="filtroUsuario">
                            <option value="">Todos los usuarios</option>
                            <option value="admin001">admin001</option>
                            <option value="cobrador001">cobrador001</option>
                            <option value="supervisor001">supervisor001</option>
                            <option value="auditor001">auditor001</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filtroTipo">Tipo de Evento</label>
                        <select class="form-control" id="filtroTipo">
                            <option value="">Todos los eventos</option>
                            <option value="acceso">Acceso</option>
                            <option value="consulta">Consulta</option>
                            <option value="modificacion">Modificación</option>
                            <option value="notificacion">Notificación</option>
                            <option value="pago">Pago</option>
                            <option value="embargo">Embargo</option>
                            <option value="suspension">Suspensión</option>
                        </select>
                    </div>
                    <button class="btn btn-primary" onclick="aplicarFiltros()">Aplicar Filtros</button>
                    <button class="btn btn-secondary" onclick="limpiarFiltros()">Limpiar</button>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    🚨 Alertas y Notificaciones
                </div>
                <div class="card-content">
                    <div class="alert alert-danger">
                        <strong>Acceso No Autorizado:</strong> Intento de acceso fallido detectado desde IP 192.168.1.100
                    </div>
                    <div class="alert alert-warning">
                        <strong>Proceso Vencido:</strong> Expediente CC-2024-089 requiere acción inmediata
                    </div>
                    <div class="alert alert-success">
                        <strong>Pago Registrado:</strong> $500,000 abonados al expediente CC-2024-067
                    </div>
                    <button class="btn btn-primary" onclick="verTodasAlertas()">Ver Todas las Alertas</button>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    📊 Generación de Reportes
                </div>
                <div class="card-content">
                    <div class="form-group">
                        <label for="tipoReporte">Tipo de Reporte</label>
                        <select class="form-control" id="tipoReporte">
                            <option value="actividad">Actividad por Usuario</option>
                            <option value="procesos">Estado de Procesos</option>
                            <option value="pagos">Resumen de Pagos</option>
                            <option value="seguridad">Eventos de Seguridad</option>
                            <option value="cumplimiento">Cumplimiento Normativo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="periodoReporte">Período</label>
                        <select class="form-control" id="periodoReporte">
                            <option value="hoy">Hoy</option>
                            <option value="semana">Esta Semana</option>
                            <option value="mes">Este Mes</option>
                            <option value="trimestre">Trimestre</option>
                            <option value="personalizado">Personalizado</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="formatoReporte">Formato</label>
                        <select class="form-control" id="formatoReporte">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                    <button class="btn btn-success" onclick="generarReporte()">Generar Reporte</button>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="card-header">
                📋 Registro de Auditorías Recientes
            </div>
            <table class="table" id="tablaAuditorias">
                <thead>
                    <tr>
                        <th>Fecha/Hora</th>
                        <th>Usuario</th>
                        <th>Tipo</th>
                        <th>Expediente</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="bodyAuditorias">
                    </tbody>
            </table>
        </div>

        <div class="card" style="margin-top: 2rem;">
            <div class="card-header">
                ⏱️ Timeline de Actividades
            </div>
            <div class="card-content">
                <div class="activity-timeline" id="timelineActividades">
                    </div>
            </div>
        </div>
    </div>

    <div id="modalDetalles" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Detalles de Auditoría</h2>
                <span class="close" onclick="cerrarModal()">&times;</span>
            </div>
            <div id="contenidoModal">
                </div>
        </div>
    </div>

    <footer>
        <p>Regional Santander - Área de Cobro Coactivo</p>
        <p>Dirección: Calle 16 No. 27-37 Bucaramanga</p>
        <p>© 2025 SENA - Servicio Nacional de Aprendizaje</p>
    </footer>

    <script src="js/Auditorias.js"></script>
    <!-- Scripts del Boton -->
    <script src="js/back-to-top.js"></script>
</body>
</body>
</html>
