<?php
  session_start();

  if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
      header("Location: logging.php");
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
    <!-- Favicon -->
    <link rel="icon" href="img/favicon.ico" type="image/x-icon"/>
    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Sistema de Gestión de Cobro Coactivo</title>
    <link rel="stylesheet" href="CSS/usuario.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Sistema de Cobro Coactivo</div>
            <ul class="nav-links">
                <li><a href="dashboard.php" id="dashboard-link">Dashboard</a></li>
                <li><a href="Deudores.php" id="casos-link">Deudores</a></li>
                <li><a href="#" id="documentos-link">Reportes</a></li>
                <li><a href="../module_login/logout.php" id="logout-link">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div id="dashboard-view">
            <h2 class="section-title">Panel de Control</h2>
            <div class="dashboard">
                <div class="card">
                    <h3>Resumen de Casos</h3>
                    <div id="case-summary" style="padding: 1rem 0;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                            <span>Casos Activos</span>
                            <span id="active-cases">3</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                            <span>Casos Resueltos</span>
                            <span id="resolved-cases">2</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Total de Casos</span>
                            <span id="total-cases">5</span>
                        </div>
                    </div>
                    <button id="view-all-cases" class="btn btn-primary">Ver todos los casos</button>
                </div>
                <div class="card">
                    <h3>Casos Recientes</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Número de Caso</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="recent-cases">
                            <tr class="case-row" data-id="CC-2023-001">
                                <td>CC-2025-001</td>
                                <td>15/04/2025</td>
                                <td><span class="status-badge status-activo">Activo</span></td>
                                <td><button class="btn btn-secondary view-case">Ver</button></td>
                            </tr>
                            <tr class="case-row" data-id="CC-2023-002">
                                <td>CC-2025-002</td>
                                <td>22/05/2025</td>
                                <td><span class="status-badge status-pendiente">Pendiente</span></td>
                                <td><button class="btn btn-secondary view-case">Ver</button></td>
                            </tr>
                            <tr class="case-row" data-id="CC-2023-003">
                                <td>CC-2025-003</td>
                                <td>10/06/2025</td>
                                <td><span class="status-badge status-resuelto">Resuelto</span></td>
                                <td><button class="btn btn-secondary view-case">Ver</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <h3>Documentos Recientes</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Nombre del Documento</th>
                            <th>Caso</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="recent-documents">
                        <tr>
                            <td>Resolución_001.pdf</td>
                            <td>CC-2025-001</td>
                            <td>18/04/2025</td>
                            <td>
                                <button class="btn btn-secondary download-document" data-file="Resolución_001.pdf">Descargar</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Notificación_002.pdf</td>
                            <td>CC-2025-002</td>
                            <td>25/05/2025</td>
                            <td>
                                <button class="btn btn-secondary download-document" data-file="Notificación_002.pdf">Descargar</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Cierre_Caso_003.pdf</td>
                            <td>CC-2025-003</td>
                            <td>12/06/2025</td>
                            <td>
                                <button class="btn btn-secondary download-document" data-file="Cierre_Caso_003.pdf">Descargar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="casos-view" style="display: none;">
            <h2 class="section-title">Mis Casos de Cobro Coactivo</h2>
            
            <div class="card">
                <div class="search-container">
                    <input type="text" id="search-case" class="search-input" placeholder="Buscar por número de caso, fecha o estado...">
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Número de Caso</th>
                            <th>Fecha de Inicio</th>
                            <th>Concepto</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="all-cases">
                        <tr class="case-row" data-id="CC-2023-001">
                            <td>CC-2023-001</td>
                            <td>15/04/2023</td>
                            <td>Impuesto Predial</td>
                            <td>$2,500,000</td>
                            <td><span class="status-badge status-activo">Activo</span></td>
                            <td><button class="btn btn-secondary view-case">Ver Detalles</button></td>
                        </tr>
                        <tr class="case-row" data-id="CC-2023-002">
                            <td>CC-2023-002</td>
                            <td>22/05/2023</td>
                            <td>Multa de Tránsito</td>
                            <td>$850,000</td>
                            <td><span class="status-badge status-pendiente">Pendiente</span></td>
                            <td><button class="btn btn-secondary view-case">Ver Detalles</button></td>
                        </tr>
                        <tr class="case-row" data-id="CC-2023-003">
                            <td>CC-2023-003</td>
                            <td>10/06/2023</td>
                            <td>Licencia Comercial</td>
                            <td>$1,200,000</td>
                            <td><span class="status-badge status-resuelto">Resuelto</span></td>
                            <td><button class="btn btn-secondary view-case">Ver Detalles</button></td>
                        </tr>
                        <tr class="case-row" data-id="CC-2023-004">
                            <td>CC-2023-004</td>
                            <td>05/07/2023</td>
                            <td>Impuesto de Industria y Comercio</td>
                            <td>$3,200,000</td>
                            <td><span class="status-badge status-activo">Activo</span></td>
                            <td><button class="btn btn-secondary view-case">Ver Detalles</button></td>
                        </tr>
                        <tr class="case-row" data-id="CC-2023-005">
                            <td>CC-2023-005</td>
                            <td>18/08/2023</td>
                            <td>Sanción Ambiental</td>
                            <td>$1,800,000</td>
                            <td><span class="status-badge status-resuelto">Resuelto</span></td>
                            <td><button class="btn btn-secondary view-case">Ver Detalles</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="documentos-view" style="display: none;">
            <h2 class="section-title">Gestión de Documentos</h2>
            
            <div class="card">
                <div class="tab-container">
                    <ul class="tabs">
                        <li class="tab active" data-tab="upload">Subir Documentos</li>
                        <li class="tab" data-tab="download">Descargar Documentos</li>
                    </ul>
                    
                    <div class="tab-content">
                        <div class="tab-panel active" id="upload-tab">
                            <div class="form-group">
                                <label for="case-selector">Seleccione el Caso:</label>
                                <select id="case-selector" class="form-control">
                                    <option value="">-- Seleccione un caso --</option>
                                    <option value="CC-2023-001">CC-2023-001 - Impuesto Predial</option>
                                    <option value="CC-2023-002">CC-2023-002 - Multa de Tránsito</option>
                                    <option value="CC-2023-003">CC-2023-003 - Licencia Comercial</option>
                                    <option value="CC-2023-004">CC-2023-004 - Impuesto de Industria y Comercio</option>
                                    <option value="CC-2023-005">CC-2023-005 - Sanción Ambiental</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="document-type">Tipo de Documento:</label>
                                <select id="document-type" class="form-control">
                                    <option value="">-- Seleccione un tipo --</option>
                                    <option value="recurso">Recurso de Reconsideración</option>
                                    <option value="comprobante">Comprobante de Pago</option>
                                    <option value="acuerdo">Acuerdo de Pago</option>
                                    <option value="prueba">Documento Probatorio</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="document-description">Descripción:</label>
                                <textarea id="document-description" class="form-control" rows="3" placeholder="Breve descripción del documento..."></textarea>
                            </div>
                            
                            <div class="file-upload" id="file-upload-area">
                                <p>Haga clic aquí para seleccionar archivo o arrastre y suelte</p>
                                <p><small>Solo archivos PDF, máximo 10MB</small></p>
                                <input type="file" id="file-input" accept=".pdf">
                            </div>
                            
                            <div id="selected-file" style="display: none; margin-bottom: 1rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; background-color: #f7fafc; border-radius: 4px;">
                                    <span id="file-name"></span>
                                    <button id="remove-file" class="btn btn-danger">Eliminar</button>
                                </div>
                            </div>
                            
                            <button id="upload-document" class="btn btn-primary">Subir Documento</button>
                        </div>
                        
                        <div class="tab-panel" id="download-tab">
                            <div class="search-container">
                                <input type="text" id="search-document" class="search-input" placeholder="Buscar documentos...">
                            </div>
                            
                            <table>
                                <thead>
                                    <tr>
                                        <th>Nombre del Documento</th>
                                        <th>Caso</th>
                                        <th>Tipo</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="document-list">
                                    <tr>
                                        <td>Resolución_001.pdf</td>
                                        <td>CC-2025-001</td>
                                        <td>Resolución</td>
                                        <td>18/04/2025</td>
                                        <td>
                                            <button class="btn btn-secondary download-document" data-file="Resolución_001.pdf">Descargar</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Notificación_002.pdf</td>
                                        <td>CC-2025-002</td>
                                        <td>Notificación</td>
                                        <td>25/05/2025</td>
                                        <td>
                                            <button class="btn btn-secondary download-document" data-file="Notificación_002.pdf">Descargar</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Cierre_Caso_003.pdf</td>
                                        <td>CC-2025-003</td>
                                        <td>Resolución de Cierre</td>
                                        <td>12/06/2025</td>
                                        <td>
                                            <button class="btn btn-secondary download-document" data-file="Cierre_Caso_003.pdf">Descargar</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Mandamiento_004.pdf</td>
                                        <td>CC-2025-004</td>
                                        <td>Mandamiento de pago</td>
                                        <td>08/07/2025</td>
                                        <td>
                                            <button class="btn btn-secondary download-document" data-file="Mandamiento_004.pdf">Descargar</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Comprobante_Pago_005.pdf</td>
                                        <td>CC-2025-005</td>
                                        <td>Comprobante de Pago</td>
                                        <td>20/08/2025</td>
                                        <td>
                                            <button class="btn btn-secondary download-document" data-file="Comprobante_Pago_005.pdf">Descargar</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de Detalles de Caso -->
    <div id="case-detail-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modal-case-title">Detalles del Caso</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="case-detail">
                    <h3>Información General</h3>
                    <div class="detail-item">
                        <span class="detail-label">Número de Caso:</span>
                        <span id="detail-case-number"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Fecha de Inicio:</span>
                        <span id="detail-start-date"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Concepto:</span>
                        <span id="detail-concept"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Monto:</span>
                        <span id="detail-amount"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Estado:</span>
                        <span id="detail-status"></span>
                    </div>
                </div>

                <div class="case-detail">
                    <h3>Documentos del Caso</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Documento</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="case-documents">
                            <!-- Documentos dinámicos -->
                        </tbody>
                    </table>
                </div>

                <div class="case-detail">
                    <h3>Cronología del Caso</h3>
                    <div class="timeline" id="case-timeline">
                        <!-- Línea de tiempo dinámica -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary close-modal-btn">Cerrar</button>
                <button class="btn btn-primary" id="upload-case-document">Subir Documento</button>
            </div>
        </div>
    </div>

    <!-- Notificación -->
    <div id="notification" class="notification"></div>
    <script src="js/usuario.js"></script>
</body>
</html>