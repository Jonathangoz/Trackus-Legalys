<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Cobro Coactivo - SENA Regional Santander</title>
    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico" type="image/x-icon"/>
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="/assets/CSS/dashboard.css">
    <!-- fontawesome -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
</head>
<body>
    <!-- Botón de volver arriba -->
    <button class="top-button" type="button" id="backToTopBtn" title="Volver arriba">
        <i class="fa-solid fa-arrow-up fa-lg" aria-hidden="true"></i>
    </button>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="/assets/images/sena.blanco.png" class="img-sena" alt="Logo SENA">
            <div class="logo">
                
                <h1>Regional Santander</h1>
            </div>
            <button type="button" title="boton retractil" class="toggle-btn" id="toggleBtn">
                <span id="toggleIcon"><i class="fa-solid fa-chevron-left"></i></span>
            </button>
        </div>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="#inicio" class="nav-link active">
                    <div class="nav-icon-dashboard">
                        <i class="fa-solid fa-gauge-high me-2 fa-lg"></i>
                    </div>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="procesos.php" class="nav-link">
                    <div class="nav-icon">
                        <i class="fa-solid fa-copy me-2 fa-lg"></i>
                    </div>
                    <span class="nav-text">Procesos</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="Deudores.php" class="nav-link">
                    <div class="nav-icon">
                        <i class="fa-solid fa-users me-2 fa-lg"></i>
                    </div>
                    <span class="nav-text">Deudores</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#contacto" class="nav-link">
                    <div class="nav-icon">
                        <i class="fa-solid fa-chart-line me-2 fa-lg"></i>
                    </div>
                    <span class="nav-text">Reportes</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="usuario.php" class="nav-link">
                    <div class="nav-icon">
                        <i class="fa-solid fa-user fa-lg"></i>
                    </div>
                    <span class="nav-text">Usuarios</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="Auditorias.php" class="nav-link">
                    <div class="nav-icon">
                        <i class="fa-solid fa-clipboard-check fa-lg"></i>
                    </div>
                    <span class="nav-text">Auditoría</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Container -->
    <div class="main-container">
        <main class="content">
            <!-- Hero Section -->
            <section id="inicio" class="hero">
                <div class="hero-content">
                <h1>Dashboard Cobro Coactivo</h1>
                </div>
                <div class="user-home">
                    <div class="user-home-content">
                        <a class="a-tigger" href="#" id="calendarTrigger">
                            <div class="user-home-icon">
                                <div class="icon-user">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <span class="text-sidebar-calendar">Calendario</span>
                            </div>
                        </a>
                    </div>
                    <div class="dropdown" id="dropdownContainer">
                        <input type="checkbox" id="userToggle" hidden>
                        <label for="userToggle" class="dropdown-toggle">
                            <span class="user-name">Bienvenido, Admin</span>
                            <div class="avatar">
                                <i class="fa-solid fa-user fa-lg"></i>
                                <i class="fa-solid fa-caret-down"></i>
                            </div>
                        </label>
                        <ul class="dropdown-menu">
                            <li><a href="#" class="drop"><i class="fa-solid fa-user me-2"></i>Perfil</a></li>
                            <li><a href="#" class="drop"><i class="fa-solid fa-gear me-2""></i>Configuración</a></li>
                            <li><hr></li>
                            <li><a href="/login" class="sesion"><i class="fa-solid fa-right-from-bracket me-2"></i>Cerrar sesión</a></li>
                        </ul>
                    </div>
                </div>
                <div id="calendarModalContainer"></div>
                <div id="userModalContainer"></div>
            </section>
            
            <!-- Card Section -->
            <section class="section-card">
                <div class="projects-row">
                    <!-- Estadísticas -->
                    <div class="row-content">
                        <div class="cuadros">
                            <div class="card-1 stat-card">
                                <div class="card-body">
                                    <div>
                                        <i class="fas fa-solid fa-bank fa-xl"></i>
                                    </div>
                                    <div>
                                        <h5>Bancos</h5>
                                        <p>124</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="cuadros">
                            <div class="card-1 stat-card">
                                <div class="card-body">
                                    <div>
                                        <i class="fas fa-solid fa-traffic-light fa-xl"></i>
                                    </div>
                                    <div>
                                        <h5>Transito</h5>
                                        <p>$825.4M</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="cuadros">
                            <div class="card-1 stat-card">
                                <div class="card-body">
                                    <div>
                                        <i class="fas fa-solid fa-handshake fa-xl"></i>
                                    </div>
                                    <div>
                                        <h5>Camara de Comercio</h5>
                                        <p>18</p>                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="cuadros">
                            <div class="card-1 stat-card">
                                <div class="card-body">
                                    <div>
                                        <i class="fas fa-solid fa-sync-alt fa-xl"></i>
                                    </div>
                                    <div>
                                        <h5>Tramites Activos</h5>
                                        <p>$56.2M</p>                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Process Section -->
            <section class="section">
                <div class="card-2">
                    <div class="card-header">
                        <h4 class="mb-0">Procesos recientes</h4>
                        <div class="card-h">
                            <input type="text" class="form-control" placeholder="Buscar proceso...">
                            <button type="button" class="btn-new-user">
                                <i class="fa-solid fa-plus-circle"></i> 
                                Nuevo proceso 
                            </button>
                        </div>
                    </div>
                </div>
                <div>
                    <table class="pricing-table">
                        <thead class="table-head">
                            <tr>
                                <th>No. Proceso</th>
                                <th>Deudor</th>
                                <th>Valor</th>
                                <th>Fecha inicio</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>CC-2025-0150</td>
                                <td>Empresa ABC S.A.S</td>
                                <td>$16,450,000</td>
                                <td>15/03/2024</td>
                                <td><span class="status-badge status-activo">Activo</span></td>
                                <td>
                                    <button type="button" class="btn-sm" aria-label="Ver detalles"><i class="fa-solid fa-eye me-2" style="color: #198754;"></i></button>
                                    <button type="button" class="btn-sm" aria-label="Editar"><i class="fa-solid fa-pen me-2" style="color: #0d6efd;"></i></button>
                                    <button type="button" class="btn-sm" aria-label="Descargar"><i class="fa-solid fa-download" style="color:rgb(10, 69, 156);"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>CC-2025-0165</td>
                                <td>Empresa 123 S.A.S</td>
                                <td>$2,450,000</td>
                                <td>15/06/2025</td>
                                <td><span class="status-badge status-activo">Activo</span></td>
                                <td>
                                    <button type="button" class="btn-sm" aria-label="Ver detalles"><i class="fa-solid fa-eye me-2" style="color: #198754;"></i></button>
                                    <button type="button" class="btn-sm" aria-label="Editar"><i class="fa-solid fa-pen me-2" style="color: #0d6efd;"></i></button>
                                    <button type="button" class="btn-sm" aria-label="Descargar"><i class="fa-solid fa-download" style="color:rgb(10, 69, 156);"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>CC-2025-0167</td>
                                <td>Empresa ghj S.A.S</td>
                                <td>952,450,000</td>
                                <td>11/08/2023</td>
                                <td><span class="status-badge status-activo">Activo</span></td>
                                <td>
                                    <button type="button" class="btn-sm" aria-label="Ver detalles"><i class="fa-solid fa-eye me-2" style="color: #198754;"></i></button>
                                    <button type="button" class="btn-sm" aria-label="Editar"><i class="fa-solid fa-pen me-2" style="color: #0d6efd;"></i></button>
                                    <button type="button" class="btn-sm" aria-label="Descargar"><i class="fa-solid fa-download" style="color:rgb(10, 69, 156);"></i></button>
                                </td>
                            </tr>
                            <!-- Más filas de la tabla... -->
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- User Section -->
            <section class="section">                
                <div class="card-2">
                    <div class="card-header">
                        <h4 class="mb-0">Administración de Usuarios</h4>
                        <div class="card-h">
                            <button type="button" class="btn-new-user" id="userTrigger">
                                <i class="fa-solid fa-user-plus"></i>
                                 Nuevo usuario
                            </button>
                        </div>
                    </div>
                </div>
                <div>
                    <table class="pricing-table">
                        <thead class="table-head">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Administrador Sistema</td>
                                <td>admin@sena.edu.co</td>
                                <td>Administrador</td>
                                <td><span class="status-badge status-activo">Activo</span></td>
                                <td>
                                    <button type="button" class="btn-sm" aria-label="ver detalles"><i class="fa-solid fa-pen" style="color: #0d6efd;"></i></button>
                                    <button type="button" class="btn-sm" aria-label="ver detalles"><i class="fa-solid fa-lock" style="color: #6c757d;"></i></button>
                                    <button type="button" class="btn-sm" aria-label="ver detalles"><i class="fa-solid fa-trash" style="color: #dc3545;"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Abogado</td>
                                <td>abogado@sena.edu.co</td>
                                <td>Abogado</td>
                                <td><span class="status-badge status-activo">Activo</span></td>
                                <td>
                                    <button type="button" class="btn-sm" aria-label="ver detalles"><i class="fa-solid fa-pen" style="color: #0d6efd;"></i></button>
                                    <button type="button" class="btn-sm" aria-label="ver detalles"><i class="fa-solid fa-lock" style="color: #6c757d;"></i></button>
                                    <button type="button" class="btn-sm" aria-label="ver detalles"><i class="fa-solid fa-trash" style="color: #dc3545;"></i></button>
                                </td>
                            </tr>
                            <!-- Más filas de la tabla... -->
                        </tbody>
                    </table>
                </div>
                
            </section>
        </main>

    <!-- Footer -->
    <footer>
        <p>Regional Santander - Área de Cobro Coactivo</p>
        <p>Dirección: Calle 16 No. 27-37 Bucaramanga</p>
        <p>© 2025 SENA - Servicio Nacional de Aprendizaje</p>
    </footer>
    </div>

<!-- Scripts del Boton -->
<script src="/assets/js/back-to-top.js"></script>
<!-- Scripts dashboard -->
<script src="/assets/js/dashaboard.js"></script>
<!-- calendario -->
<script src="/assets/js/calendar-event.js"></script>
<!-- sidebar -->
<script src="/assets/js/script.js"></script>
</body>
</html>