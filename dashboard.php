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
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="CSS/dashboar.css">
    
    
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Encabezado -->
    <header class="navbar navbar-dark navbar-custom sticky-top">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <img src="img/sena.blanco.png" alt="Logo SENA" height="40" class="me-3">
                <span class="navbar-brand mb-0 h1">Sistema de Cobro Coactivo - Regional Santander</span>
            </div>
            
            <div class="dropdown">
                <button class="btn dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background: none; border: none; color: white;">
                  <span class="me-2">Bienvenido, Admin</span>
                  <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">A</div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                  <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Perfil</a></li>
                  <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Configuración</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item text-danger" href="module_login/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a></li>
                </ul>
              </div>
        </div>
    </header>

    <div class="container-fluid flex-grow-1">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./procesos.html">
                                <i class="bi bi-files me-2"></i> Procesos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./Deudores.html">
                                <i class="bi bi-people-fill me-2"></i> Deudores
                            </a>
                        </li>                    
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-graph-up me-2"></i> Reportes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-gear-fill me-2"></i> Configuración
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Contenido principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 main-content">
                <h2 class="h3 mb-4">Dashboard de Cobro Coactivo</h2>
                
                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card h-100 stat-card">
                            <div class="card-body">
                                <h5 class="card-title">Bancos</h5>
                                <p class="card-text display-6">124</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card h-100 stat-card">
                            <div class="card-body">
                                <h5 class="card-title">Transito</h5>
                                <p class="card-text display-6">$825.4M</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card h-100 stat-card">
                            <div class="card-body">
                                <h5 class="card-title">Camara de Comercio</h5>
                                <p class="card-text display-6">18</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card h-100 stat-card">
                            <div class="card-body">
                                <h5 class="card-title">Tramites Activos</h5>
                                <p class="card-text display-6">$56.2M</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tabla de procesos -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Procesos recientes</h5>
                        <div class="d-flex">
                            <input type="text" class="form-control me-2" placeholder="Buscar proceso..." style="width: 200px;">
                            <button class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Nuevo proceso
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
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
                                            <button type="button" class="btn btn-sm btn-outline-primary" aria-label="Ver detalles"><i class="bi bi-eye"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" aria-label="Ver detalles"><i class="bi bi-pencil"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-info" aria-label="Ver detalles"><i class="bi bi-file-earmark"></i></button>
                                        </td>
                                    </tr>
                                    <!-- Más filas de la tabla... -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Sección de usuarios -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Administración de Usuarios</h5>
                        <button class="btn btn-new-user" data-bs-toggle="modal" data-bs-target="#userModal">
                            <i class="bi bi-person-plus"></i> Nuevo usuario
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
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
                                            <button type="button" class="btn btn-sm btn-outline-secondary" aria-label="ver detalles"><i class="bi bi-pencil"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-warning" aria-label="ver detalles"><i class="bi bi-lock"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" aria-label="ver detalles"><i class="bi bi-trash"></i></button>
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

    <!-- Modal para creación de usuarios -->
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
    </div>

    <!-- Footer -->
    <footer class="mt-auto bg-dark text-white py-3">
        <div class="container text-center">
            <p class="mb-0">© 2025 SENA - Servicio Nacional de Aprendizaje | Regional Santander</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dashaboart.js"></script>
    <!-- Scripts personalizados -->
     
</body>
</html>