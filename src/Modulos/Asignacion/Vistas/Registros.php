<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Recepción - Oficina Jurídica</title>
    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="/assets/CSS/Modulo/Asignacion/Registro.css"/>
    <!-- fontawesome -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="spinner"></div>
        <p>Procesando...</p>
    </div>
    <!-- Botón de volver arriba -->
    <button type="button" id="backToTopBtn" title="Volver arriba">
        <i class="fa-solid fa-arrow-up fa-lg" aria-hidden="true"></i>
    </button>
    <!-- Header -->
    <header>
        <div class="logo-container">
            <img src="/assets/images/sena.blanco.png" class="img-sena" alt="Logo SENA">            
            <p>SENA Regional Santander</p>
        </div>
        <div class="title-container">
            <h1>Procesos Cobro Coactivo</h1>
        </div>       
        <div class="user-home">
            <div class="home">
                <a href="/asignacion" style="text-decoration: none;"><i class="fa-solid fa-house fa-lg" style="color: white;"></i>
                    <div>
                        <span class="text" style="color: white;">Asignacion</span>
                    </div>
                </a>
            </div>
            <div class="home">
                <a href="/crearcasos" style="text-decoration: none;"><i class="fa-solid fa-house fa-lg" style="color: white;"></i>
                    <div>
                        <span class="text" style="color: white;">Crear Casos</span>
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
                    <li><a href="/login" class="sesion"><i class="fa-solid fa-right-from-bracket me-2" style="margin-right: 15px;"></i>Cerrar sesión</a></li>
                </ul>
            </div>
        </div>
    </header>

    <section class="header">
        <div class="container">
            <div class="header-content">
                <nav class="main-nav">
                    <button class="tab-button tab-button--active" data-tab="dashboard">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </button>
                    <button class="tab-button" data-tab="registro">
                        <i class="fas fa-plus-circle"></i> Nuevo Registro
                    </button>
                    <button class="tab-button" data-tab="consulta">
                        <i class="fas fa-search"></i> Consultar
                    </button>
                    <button class="tab-button" data-tab="reportes">
                        <i class="fas fa-chart-bar"></i> Reportes
                    </button>
                </nav>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Dashboard Section -->
            <section id="dashboard" class="tab-content tab-content--active">
                <div class="section-header">
                    <h2>Dashboard - Cobro Coactivo</h2>
                    <p>Gestión integral de títulos ejecutivos y procesos de cobro</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-content">
                            <h3 id="totalTitulos">0</h3>
                            <p>Títulos Registrados</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="stat-content">
                            <h3 id="titulosPendientes">0</h3>
                            <p>Pendientes</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-content">
                            <h3 id="montoTotal">$0</h3>
                            <p>Monto Total</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="stat-content">
                            <h3 id="registrosHoy">0</h3>
                            <p>Registros Hoy</p>
                        </div>
                    </div>
                </div>

                <div class="recent-activities">
                    <h3><i class="fas fa-clock"></i> Actividades Recientes</h3>
                    <div class="activities-list" id="recentActivities">
                        <!-- Activities will be loaded here -->
                    </div>
                </div>
            </section>

            <!-- Registro Section -->
            <section id="registro" class="tab-content">
                <div class="section-header">
                    <h2><i class="fas fa-plus-circle"></i> Registro de Título Ejecutivo</h2>
                    <p>Complete todos los campos requeridos para registrar un nuevo título</p>
                </div>

                <div class="form-container">
                    <form id="tituloForm" class="titulo-form">
                        <!-- Basic Information -->
                        <div class="form-section">
                            <h3><i class="fas fa-info-circle"></i> Información Básica</h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="numeroRadicado">Número de Radicado</label>
                                    <input type="text" id="numeroRadicado" name="numeroRadicado" readonly>
                                    <small>Se genera automáticamente</small>
                                </div>
                                <div class="form-group">
                                    <label for="fechaRecepcion">Fecha de Recepción</label>
                                    <input type="date" id="fechaRecepcion" name="fechaRecepcion" required>
                                </div>
                                <div class="form-group">
                                    <label for="medioRecepcion">Medio de Recepción</label>
                                    <select id="medioRecepcion" name="medioRecepcion" required>
                                        <option value="">Seleccione...</option>
                                        <option value="fisico">Físico</option>
                                        <option value="electronico">Electrónico</option>
                                        <option value="mixto">Mixto</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="responsableEnvio">Responsable del Envío</label>
                                    <select id="responsableEnvio" name="responsableEnvio" required>
                                        <option value="">Seleccione...</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Debtor Information -->
                        <div class="form-section">
                            <h3><i class="fas fa-user"></i> Información del Deudor</h3>
                            <div class="debtor-search">
                                <div class="search-container">
                                    <input type="text" id="deudorSearch" placeholder="Buscar por nombre, cédula o NIT...">
                                    <button type="button" id="searchDeudor" class="btn-search">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                <div class="search-results" id="deudorResults"></div>
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="tipoDeudor">Tipo de Deudor</label>
                                    <select id="tipoDeudor" name="tipoDeudor" required>
                                        <option value="">Seleccione...</option>
                                        <option value="natural">Persona Natural</option>
                                        <option value="juridica">Persona Jurídica</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="nombreDeudor">Nombre/Razón Social</label>
                                    <input type="text" id="nombreDeudor" name="nombreDeudor" required>
                                </div>
                                <div class="form-group">
                                    <label for="documentoDeudor">Cédula/NIT</label>
                                    <input type="text" id="documentoDeudor" name="documentoDeudor" required>
                                </div>
                                <div class="form-group">
                                    <label for="direccionDeudor">Dirección</label>
                                    <input type="text" id="direccionDeudor" name="direccionDeudor" required>
                                </div>
                                <div class="form-group">
                                    <label for="correoDeudor">Correo Electrónico</label>
                                    <input type="email" id="correoDeudor" name="correoDeudor">
                                </div>
                                <div class="form-group">
                                    <label for="telefonoDeudor">Teléfono</label>
                                    <input type="tel" id="telefonoDeudor" name="telefonoDeudor">
                                </div>
                            </div>
                        </div>

                        <!-- Debt Information -->
                        <div class="form-section">
                            <h3><i class="fas fa-dollar-sign"></i> Información de la Deuda</h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="naturalezaObligacion">Naturaleza de la Obligación</label>
                                    <select id="naturalezaObligacion" name="naturalezaObligacion" required>
                                        <option value="">Seleccione...</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="montoCapital">Monto Capital</label>
                                    <input type="number" id="montoCapital" name="montoCapital" step="0.01" required>
                                </div>
                                <div class="form-group">
                                    <label for="montoIntereses">Intereses</label>
                                    <input type="number" id="montoIntereses" name="montoIntereses" step="0.01">
                                </div>
                                <div class="form-group">
                                    <label for="montoMultas">Multas</label>
                                    <input type="number" id="montoMultas" name="montoMultas" step="0.01">
                                </div>
                                <div class="form-group">
                                    <label for="montoTotal">Monto Total</label>
                                    <input type="number" id="montoTotal" name="montoTotal" step="0.01" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="fechaVencimiento">Fecha de Vencimiento Original</label>
                                    <input type="date" id="fechaVencimiento" name="fechaVencimiento" required>
                                </div>
                            </div>
                        </div>

                        <!-- Documents -->
                        <div class="form-section">
                            <h3><i class="fas fa-file-upload"></i> Documentos Anexos</h3>
                            <div class="documents-container">
                                <div class="document-checklist">
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="documentos[]" value="titulo_ejecutivo">
                                        <span class="checkmark"></span>
                                        Título Ejecutivo Original
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="documentos[]" value="acto_administrativo">
                                        <span class="checkmark"></span>
                                        Copia del Acto Administrativo
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="documentos[]" value="factura">
                                        <span class="checkmark"></span>
                                        Factura
                                    </label>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="documentos[]" value="certificacion_deudas">
                                        <span class="checkmark"></span>
                                        Certificación de Deudas
                                    </label>
                                </div>
                                <div class="file-upload">
                                    <input type="file" id="documentFiles" multiple accept=".pdf,.doc,.docx,.jpg,.png">
                                    <label for="documentFiles" class="upload-btn">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        Subir Documentos
                                    </label>
                                    <div class="file-list" id="fileList"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Observations -->
                        <div class="form-section">
                            <h3><i class="fas fa-comment-alt"></i> Observaciones</h3>
                            <textarea id="observaciones" name="observaciones" rows="4" placeholder="Ingrese observaciones adicionales..."></textarea>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="button" id="btnLimpiar" class="btn btn-secondary">
                                <i class="fas fa-eraser"></i> Limpiar
                            </button>
                            <button type="button" id="btnGuardarBorrador" class="btn btn-outline">
                                <i class="fas fa-save"></i> Guardar Borrador
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Registrar Título
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Consulta Section -->
            <section id="consulta" class="tab-content">
                <div class="section-header">
                    <h2><i class="fas fa-search"></i> Consultar Títulos Ejecutivos</h2>
                    <p>Busque y consulte títulos ejecutivos registrados</p>
                </div>

                <div class="search-panel">
                    <div class="search-filters">
                        <div class="filter-group">
                            <label for="filtroRadicado">Número de Radicado</label>
                            <input type="text" id="filtroRadicado" placeholder="Ej: 2025-0001-CC">
                        </div>
                        <div class="filter-group">
                            <label for="filtroDeudor">Deudor</label>
                            <input type="text" id="filtroDeudor" placeholder="Nombre o documento">
                        </div>
                        <div class="filter-group">
                            <label for="filtroFechaInicio">Fecha Inicio</label>
                            <input type="date" id="filtroFechaInicio">
                        </div>
                        <div class="filter-group">
                            <label for="filtroFechaFin">Fecha Fin</label>
                            <input type="date" id="filtroFechaFin">
                        </div>
                        <div class="filter-group">
                            <label for="filtroEstado">Estado</label>
                            <select id="filtroEstado">
                                <option value="">Todos</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="proceso">En Proceso</option>
                                <option value="completado">Completado</option>
                            </select>
                        </div>
                        <button type="button" id="btnBuscar" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>

                <div class="results-container">
                    <div class="table-header">
                        <h3>Resultados de la Búsqueda</h3>
                        <div class="table-actions">
                            <button type="button" id="btnExportar" class="btn btn-outline">
                                <i class="fas fa-download"></i> Exportar
                            </button>
                        </div>
                    </div>
                    <div class="table-container">
                        <table id="titulosTable" class="data-table">
                            <thead>
                                <tr>
                                    <th>Radicado</th>
                                    <th>Deudor</th>
                                    <th>Documento</th>
                                    <th>Monto Total</th>
                                    <th>Fecha Registro</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Results will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination" id="pagination">
                        <!-- Pagination will be loaded here -->
                    </div>
                </div>
            </section>

            <!-- Reportes Section -->
            <section id="reportes" class="tab-content">
                <div class="section-header">
                    <h2><i class="fas fa-chart-bar"></i> Reportes y Estadísticas</h2>
                    <p>Análisis y reportes del sistema de cobro coactivo</p>
                </div>

                <div class="reports-grid">
                    <div class="report-card">
                        <h3>Reporte por Período</h3>
                        <p>Genere reportes filtrados por fechas</p>
                        <button class="btn btn-primary">Generar</button>
                    </div>
                    <div class="report-card">
                        <h3>Reporte por Estado</h3>
                        <p>Estadísticas de títulos por estado</p>
                        <button class="btn btn-primary">Generar</button>
                    </div>
                    <div class="report-card">
                        <h3>Reporte Financiero</h3>
                        <p>Análisis de montos y recuperación</p>
                        <button class="btn btn-primary">Generar</button>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Modal for Details -->
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Detalles del Título Ejecutivo</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close">Cerrar</button>
                <button type="button" id="btnImprimir" class="btn btn-primary">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div id="messageContainer" class="message-container"></div>

    <script src="/assets/js/Modulo/Asignacion/registros.js"></script>
    <script src="/assets/js/backToTop.js"></script>
</body>
</html>