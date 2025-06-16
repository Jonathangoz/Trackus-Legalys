<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars(\App\Comunes\seguridad\csrf::generarToken(), ENT_QUOTES, 'UTF-8') ?>">
    <meta name="description" content="Sistema de Cobro Coactivo del SENA Regional Santander - Gestión de Procesos de Cobro Coactivo">
    <meta name="keywords" content="SENA, Cobro Coactivo, Procesos Judiciales, Administración de Trámites, Abogados">
    <title>Mi Bandeja - Sistema de Cobros</title>
    <link rel="stylesheet" href="/assets/CSS/Modulo/Deudores/abogado.css"/>
    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico" type="image/x-icon"/>
    <!-- fontawesome -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <!-- Botón de volver arriba -->
    <button type="button" id="backToTopBtn" title="Volver arriba">
        <i class="fa-solid fa-arrow-up fa-lg" aria-hidden="true"></i>
    </button>
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
                <a href="/cobrocoactivo/formularios" style="text-decoration: none;"><i class="fa-solid fa-house fa-lg" style="color: white;"></i>
                    <div>
                        <span class="text" style="color: white;">Formulario</span>
                    </div>
                </a>
            </div>
            <div class="home">
                <a href="/deudores" style="text-decoration: none;"><i class="fa-solid fa-house fa-lg" style="color: white;"></i>
                    <div>
                        <span class="text" style="color: white;">Deudores</span>
                    </div>
                </a>
            </div>
            <div class="home">
                <a href="/deudores/obligados" style="text-decoration: none;"><i class="fa-solid fa-house fa-lg" style="color: white;"></i>
                    <div>
                        <span class="text" style="color: white;">Obligados al Pago</span>
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
                    <li><a href="/logout" class="sesion"><i class="fa-solid fa-right-from-bracket me-2" style="margin-right: 15px;"></i>Cerrar sesión</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="abogado-container">
        <!-- Header -->
        <header class="abogado-header">
            <div class="header-content">
                <div class="logo-section">
                    <i class="fas fa-balance-scale"></i>
                    <h1>Área Legal</h1>
                </div>
                <div class="user-section">
                    <div class="notifications">
                        <button class="notification-btn" id="notificationBtn">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge">3</span>
                        </button>
                        <div class="notification-dropdown" id="notificationDropdown">
                            <div class="notification-header">
                                <h3>Notificaciones</h3>
                                <button class="mark-read-btn">Marcar todas como leídas</button>
                            </div>
                            <div class="notification-list">
                                <div class="notification-item unread">
                                    <i class="fas fa-file-plus"></i>
                                    <div class="notification-content">
                                        <p><strong>Nuevo caso asignado</strong></p>
                                        <span>RAD-2025-001234</span>
                                        <small>Hace 15 min</small>
                                    </div>
                                </div>
                                <div class="notification-item unread">
                                    <i class="fas fa-clock"></i>
                                    <div class="notification-content">
                                        <p><strong>Caso próximo a vencer</strong></p>
                                        <span>RAD-2025-001180</span>
                                        <small>Hace 2 horas</small>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <i class="fas fa-money-bill"></i>
                                    <div class="notification-content">
                                        <p><strong>Pago registrado</strong></p>
                                        <span>RAD-2025-001156</span>
                                        <small>Hace 1 día</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <span class="user-name">Dra. María González</span>
                    <button class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- Navigation -->
        <nav class="abogado-nav">
            <div class="nav-container">
                <button class="nav-btn active" data-section="bandeja">
                    <i class="fas fa-inbox"></i>
                    <span>Mi Bandeja</span>
                </button>
                <button class="nav-btn" data-section="detalle">
                    <i class="fas fa-file-alt"></i>
                    <span>Detalle de Caso</span>
                </button>
                <button class="nav-btn" data-section="documentos">
                    <i class="fas fa-file-pdf"></i>
                    <span>Documentos</span>
                </button>
                <button class="nav-btn" data-section="reportes">
                    <i class="fas fa-chart-bar"></i>
                    <span>Mis Reportes</span>
                </button>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="abogado-main">
            <!-- Bandeja de Casos Section -->
            <section id="bandeja" class="content-section active">
                <div class="section-header">
                    <h2>Mi Bandeja de Casos</h2>
                    <div class="header-stats">
                        <div class="stat-item">
                            <i class="fas fa-tasks"></i>
                            <span>Total: <strong id="totalCases">0</strong></span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Urgentes: <strong id="urgentCases">0</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="filters-container">
                    <div class="filter-group">
                        <label>Tipo de Trámite</label>
                        <select class="filter-select" id="tipoTramite">
                            <option value="">Todos</option>
                            <option value="cobro-persuasivo">Cobro Persuasivo</option>
                            <option value="cobro-coactivo">Cobro Coactivo</option>
                            <option value="embargo">Embargo</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Estado</label>
                        <select class="filter-select" id="estadoCaso">
                            <option value="">Todos</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="en-analisis">En Análisis</option>
                            <option value="en-proceso">En Proceso</option>
                            <option value="completado">Completado</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Fecha Desde</label>
                        <input type="date" class="filter-input" id="fechaDesde">
                    </div>
                    <div class="filter-group">
                        <label>Fecha Hasta</label>
                        <input type="date" class="filter-input" id="fechaHasta">
                    </div>
                    <div class="filter-actions">
                        <button class="filter-btn" id="aplicarFiltros">
                            <i class="fas fa-filter"></i>
                            Filtrar
                        </button>
                        <button class="clear-btn" id="limpiarFiltros">
                            <i class="fas fa-times"></i>
                            Limpiar
                        </button>
                    </div>
                </div>

                <!-- Tabla de Casos -->
                <div class="table-container">
                    <div class="table-header">
                        <div class="table-title">
                            <h3>Lista de Casos</h3>
                        </div>
                        <div class="table-actions">
                            <button class="export-btn">
                                <i class="fas fa-download"></i>
                                Exportar
                            </button>
                        </div>
                    </div>
                    <div class="table-wrapper">
                        <table class="cases-table" id="casesTable">
                            <thead>
                                <tr>
                                    <th class="sortable" data-column="radicado">
                                        Radicado
                                        <i class="fas fa-sort"></i>
                                    </th>
                                    <th class="sortable" data-column="deudor">
                                        Deudor
                                        <i class="fas fa-sort"></i>
                                    </th>
                                    <th class="sortable" data-column="tipo">
                                        Tipo
                                        <i class="fas fa-sort"></i>
                                    </th>
                                    <th class="sortable" data-column="estado">
                                        Estado
                                        <i class="fas fa-sort"></i>
                                    </th>
                                    <th class="sortable" data-column="monto">
                                        Monto
                                        <i class="fas fa-sort"></i>
                                    </th>
                                    <th class="sortable" data-column="fecha">
                                        Última Actualización
                                        <i class="fas fa-sort"></i>
                                    </th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="casesTableBody">
                                <!-- Los datos se cargarán dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="pagination-container">
                        <div class="pagination-info">
                            <span>Mostrando <strong id="showingFrom">0</strong> - <strong id="showingTo">0</strong> de <strong id="totalRecords">0</strong> registros</span>
                        </div>
                        <div class="pagination-controls">
                            <button class="pagination-btn" id="prevPage" disabled>
                                <i class="fas fa-chevron-left"></i>
                                Anterior
                            </button>
                            <div class="pagination-numbers" id="paginationNumbers">
                                <!-- Se generarán dinámicamente -->
                            </div>
                            <button class="pagination-btn" id="nextPage">
                                Siguiente
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Detalle de Caso Section -->
            <section id="detalle" class="content-section">
                <div class="section-header">
                    <h2>Detalle del Caso</h2>
                    <div class="case-selector">
                        <select class="case-select" id="caseSelector">
                            <option value="">Seleccionar caso...</option>
                        </select>
                        <button class="refresh-btn" id="refreshCase">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>

                <div class="case-detail-container" id="caseDetailContainer">
                    <div class="no-case-selected">
                        <i class="fas fa-file-alt"></i>
                        <h3>Selecciona un caso para ver los detalles</h3>
                        <p>Utiliza el selector superior o haz clic en un caso desde la bandeja</p>
                    </div>
                </div>
            </section>

            <!-- Documentos Section -->
            <section id="documentos" class="content-section">
                <div class="section-header">
                    <h2>Generación de Documentos</h2>
                    <div class="document-stats">
                        <span>Caso seleccionado: <strong id="selectedCaseDoc">Ninguno</strong></span>
                    </div>
                </div>

                <div class="documents-container">
                    <div class="document-templates">
                        <h3>Plantillas Disponibles</h3>
                        <div class="templates-grid">
                            <div class="template-card" data-template="mandamiento">
                                <div class="template-icon">
                                    <i class="fas fa-gavel"></i>
                                </div>
                                <div class="template-info">
                                    <h4>Mandamiento de Pago</h4>
                                    <p>Documento oficial para exigir el pago de la deuda</p>
                                </div>
                                <button class="generate-doc-btn" data-type="mandamiento">
                                    Generar
                                </button>
                            </div>
                            <div class="template-card" data-template="embargo">
                                <div class="template-icon">
                                    <i class="fas fa-ban"></i>
                                </div>
                                <div class="template-info">
                                    <h4>Oficio de Embargo</h4>
                                    <p>Solicitud de embargo de bienes del deudor</p>
                                </div>
                                <button class="generate-doc-btn" data-type="embargo">
                                    Generar
                                </button>
                            </div>
                            <div class="template-card" data-template="acuerdo">
                                <div class="template-icon">
                                    <i class="fas fa-handshake"></i>
                                </div>
                                <div class="template-info">
                                    <h4>Acuerdo de Pago</h4>
                                    <p>Documento de convenio de pago voluntario</p>
                                </div>
                                <button class="generate-doc-btn" data-type="acuerdo">
                                    Generar
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="generated-docs">
                        <h3>Documentos Generados</h3>
                        <div class="docs-table-container">
                            <table class="docs-table">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Caso</th>
                                        <th>Fecha Generación</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="generatedDocsBody">
                                    <!-- Se cargarán dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Reportes Section -->
            <section id="reportes" class="content-section">
                <div class="section-header">
                    <h2>Mis Reportes</h2>
                    <div class="report-period">
                        <select class="period-select">
                            <option value="semana">Esta Semana</option>
                            <option value="mes" selected>Este Mes</option>
                            <option value="trimestre">Este Trimestre</option>
                            <option value="ano">Este Año</option>
                        </select>
                    </div>
                </div>

                <div class="reports-grid">
                    <div class="report-card">
                        <div class="report-header">
                            <h3>Casos Asignados</h3>
                            <i class="fas fa-inbox"></i>
                        </div>
                        <div class="report-value">
                            <span class="big-number" id="casosAsignados">0</span>
                            <span class="trend positive">+12%</span>
                        </div>
                        <div class="report-subtitle">Total este mes</div>
                    </div>

                    <div class="report-card">
                        <div class="report-header">
                            <h3>Casos Completados</h3>
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="report-value">
                            <span class="big-number" id="casosCompletados">0</span>
                            <span class="trend positive">+8%</span>
                        </div>
                        <div class="report-subtitle">Finalizados este mes</div>
                    </div>

                    <div class="report-card">
                        <div class="report-header">
                            <h3>Documentos Generados</h3>
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="report-value">
                            <span class="big-number" id="documentosGenerados">0</span>
                            <span class="trend negative">-3%</span>
                        </div>
                        <div class="report-subtitle">Este mes</div>
                    </div>

                    <div class="report-card">
                        <div class="report-header">
                            <h3>Tiempo Promedio</h3>
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="report-value">
                            <span class="big-number" id="tiempoPromedio">0</span>
                            <span class="unit">días</span>
                        </div>
                        <div class="report-subtitle">Por caso completado</div>
                    </div>
                </div>

                <div class="charts-section">
                    <div class="chart-container">
                        <h3>Casos por Estado</h3>
                        <canvas id="estadosChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h3>Productividad Mensual</h3>
                        <canvas id="productividadChart"></canvas>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Modals -->
    <!-- Modal de Preview de Documento -->
    <div id="docPreviewModal" class="modal">
        <div class="modal-content doc-modal">
            <div class="modal-header">
                <h3 id="docPreviewTitle">Vista Previa del Documento</h3>
                <button class="close-btn" id="closeDocPreview">&times;</button>
            </div>
            <div class="modal-body">
                <div class="doc-preview-container">
                    <iframe id="docPreviewFrame" src="" width="100%" height="600"></iframe>
                </div>
            </div>
            <div class="modal-footer">
                <button class="cancel-btn" id="cancelDocPreview">Cancelar</button>
                <button class="download-btn" id="downloadDoc">
                    <i class="fas fa-download"></i>
                    Descargar PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Cargando...</p>
        </div>
    </div>
    <!-- Scripts -->
    <script src="/assets/js/Modulo/Deudores/abogado.js"></script>
    <script src="/assets/js/Modulo/Deudores/abogado-ajax.js"></script>
</body>
</html>