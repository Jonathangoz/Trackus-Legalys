<?php
  session_start();

  if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
      header("Location: index.html");
      exit;
  }

  if($_SESSION['tipo_rol'] !== 'ADMIN'){
    header("Location: index.html");
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
    <title>Sistema de Cobro Coactivo - SENA Regional Santander</title>
    <!-- Favicon -->
    <link rel="icon" href="img/favicon.ico" type="image/x-icon"/>
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="CSS/dashboard.css">
    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
</head>
<body>
    <!-- Botón de volver arriba -->
    <button type="button" id="backToTopBtn" title="Volver arriba">
        <i class="fa-solid fa-arrow-up fa-lg" aria-hidden="true"></i>
    </button>
    <!-- Encabezado del gobierno -->
    <div class="top">
        <a href="https://www.gov.co" target="_blank" alt="Gov.co" rel="noopener noreferrer">
            <img class="gov" src="https://css.mintic.gov.co/mt/mintic/img/header_govco.png" alt="Gov Co">
        </a>
    </div>
    <!-- Encabezado --> 
    <header>
        <div class="logo-container">
            <img src="img/sena.blanco.png" class="img-sena" alt="Logo SENA">            
            <p>SENA Regional Santander</p>
        </div>
        <div class="title-container">
            <h1>Sistema de Cobro Coactivo</h1>
        </div> 
        <div class="user-home">
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

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="sidebar">
                <div class="position">
                    <ul class="nav">
                        <li class="nav-item">
                            <a href="dashboard.php" style="text-decoration: none;">
                                <i class="fa-solid fa-gauge-high me-2" style="color: white; margin-right: 18px;"></i>
                                    <span class="text" style="color: white;">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="procesos.php" style="text-decoration: none;"><i class="fa-solid fa-copy me-2" style="color: white; margin-right: 19px;"></i>
                                <span class="text" style="color: white;">Procesos</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="Deudores.php" style="text-decoration: none;"><i class="fa-solid fa-users me-2" style="color: white; margin-right: 15px;"></i>
                                <span class="text" style="color: white;">Deudores</span>
                            </a>
                        </li>                    
                        <li class="nav-item">
                            <a href="#" style="text-decoration: none;"><i class="fa-solid fa-chart-line me-2" style="color: white; margin-right: 18px;"></i>
                                <span class="text" style="color: white;">Reportes</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" style="text-decoration: none;"><i class="fa-solid fa-cog me-2" style="color: white; margin-right: 18px;"></i>
                                <span class="text" style="color: white;">Configuración</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Contenido principal -->
            <main class="main-content">
                <h2>Dashboard de Cobro Coactivo</h2>
                
                <!-- Estadísticas -->
                <div class="row-content">
                    <div class="cuadros">
                        <div class="card-1 stat-card">
                            <div class="card-body">
                                <h5 class="card-title">Bancos</h5>
                                <p class="card-text display-6">124</p>
                            </div>
                        </div>
                    </div>
                    <div class="cuadros">
                        <div class="card-1 stat-card">
                            <div class="card-body">
                                <h5 class="card-title">Transito</h5>
                                <p class="card-text display-6">$825.4M</p>
                            </div>
                        </div>
                    </div>
                    <div class="cuadros">
                        <div class="card-1 stat-card">
                            <div class="card-body">
                                <h5 class="card-title">Camara de Comercio</h5>
                                <p class="card-text display-6">18</p>
                            </div>
                        </div>
                    </div>
                    <div class="cuadros">
                        <div class="card-1 stat-card">
                            <div class="card-body">
                                <h5 class="card-title">Tramites Activos</h5>
                                <p class="card-text display-6">$56.2M</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tabla de procesos -->
                <div class="card-2">
                    <div class="card-header">
                        <h4 class="mb-0">Procesos recientes</h4>
                        <div class="card-h">
                            <input type="text" class="form-control" placeholder="Buscar proceso..." style="width: 200px;">
                            <button class="btn btn-new-user">
                                <i class="fa-solid fa-plus-circle"></i> Nuevo proceso
                            </button>
                        </div>
                    </div>
                    <div class="card-body-table">
                        <div>
                            <table class="table">
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
                                        <td>CC-2025-0142</td>
                                        <td>Empresa XYZ S.A.S</td>
                                        <td>$52,450,000</td>
                                        <td>15/03/2025</td>
                                        <td><span class="status-badge status-activo">Activo</span></td>
                                        <td>
                                            <button type="button" class="btn btn-sm" aria-label="Ver detalles"><i class="fa-solid fa-eye me-2" style="color: #198754;"></i></button>
                                            <button type="button" class="btn btn-sm" aria-label="Editar"><i class="fa-solid fa-pen me-2" style="color: #0d6efd;"></i></button>
                                            <button type="button" class="btn-sm" aria-label="Descargar"><i class="fa-solid fa-download" style="color:rgb(10, 69, 156);"></i></button>
                                        </td>
                                    </tr>
                                    <!-- Más filas de la tabla... -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Sección de usuarios -->
                <div class="card-3">
                    <div class="card-header">
                        <h4>Administración de Usuarios</h4>
                        <div class="card-h">
                            <button class="btn btn-new-user" data-bs-toggle="modal" data-bs-target="#userModal">
                                <i class="fa-solid fa-user-plus"></i> Nuevo usuario
                            </button>
                        </div>
                    </div>
                    <div class="card-body-table">
                        <div>
                            <table class="table">
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
                                            <button type="button" class="btn btn-sm" aria-label="ver detalles"><i class="fa-solid fa-pen" style="color: #0d6efd;"></i></button>
                                            <button type="button" class="btn btn-sm" aria-label="ver detalles"><i class="fa-solid fa-lock" style="color: #6c757d;"></i></button>
                                            <button type="button" class="btn btn-sm" aria-label="ver detalles"><i class="fa-solid fa-trash" style="color: #dc3545;"></i></button>
                                        </td>
                                    </tr>
                                    <!-- Más filas de la tabla... -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal para creación de usuarios 
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-custom text-white">
                    <h5 class="modal-title" id="userModalLabel">Crear nuevo usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="userForm" class="row g-3">
                        <div class="col-md-6">
                            <label for="userName" class="form-label">Nombre completo *</label>
                            <input type="text" class="form-control" id="userName" required>
                        </div>
                        <div class="col-md-6">
                            <label for="userIdentification" class="form-label">Número de identificación *</label>
                            <input type="text" class="form-control" id="userIdentification" required>
                        </div>
                        <div class="col-md-6">
                            <label for="userEmail" class="form-label">Correo electrónico *</label>
                            <input type="email" class="form-control" id="userEmail" required>
                        </div>
                        <div class="col-md-6">
                            <label for="userPhone" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="userPhone">
                        </div>
                        <div class="col-md-6">
                            <label for="userRole" class="form-label">Rol *</label>
                            <select class="form-select" id="userRole" required>
                                <option value="">Seleccione un rol</option>
                                <option value="admin">Administrador</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="cobrador">Cobrador</option>
                                <option value="consulta">Consulta</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="userPassword" class="form-label">Contraseña *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="userPassword" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="userConfirmPassword" class="form-label">Confirmar contraseña *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="userConfirmPassword" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div id="passwordError" class="text-danger small mt-1 d-none">Las contraseñas no coinciden</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="saveUser">Guardar</button>
                </div>
            </div>
        </div>
    </div> -->
    <!-- Footer -->
    <footer class="footer">
        <div class="container text-center">
            <p class="mb-0">© 2025 SENA - Servicio Nacional de Aprendizaje | Regional Santander</p>
        </div>
    </footer>     
</body>
</html>
<!-- Scripts del Boton -->
<script src="js/back-to-top.js"></script>
<!-- Scripts dashboard -->
<script src="js/dashaboard.js"></script>