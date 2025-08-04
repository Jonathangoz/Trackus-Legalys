<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars(\App\Comunes\seguridad\csrf::generarToken(), ENT_QUOTES, 'UTF-8') ?>">
    <meta name="description" content="Sistema de Cobro Coactivo del SENA Regional Santander - Gestión de Procesos Contra los Obligados al Pago en Mora">
    <meta name="keywords" content="SENA, Cobro Coactivo, Obligados al Pago, Procesos Judiciales, Administración de Trámites">
    <title>Obligados al Pago - Sistema Cobro Coactivo</title>
    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="/assets/CSS/Modulo/Deudores/Deudores.css">
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
            <h1>OBLIGADOS AL PAGO</h1>
        </div>  
            <div class="home">
                <a href="/cobrocoactivo" style="text-decoration: none;"><i class="fa-solid fa-house fa-lg" style="color: white;"></i>
                    <div>
                        <span class="text" style="color: white;">Abogados</span>
                    </div>
                </a>
            </div>     
            <div class="home">
                <a href="/cobrocoactivo/formularios" style="text-decoration: none;"><i class="fa-solid fa-house fa-lg" style="color: white;"></i>
                    <div>
                        <span class="text" style="color: white;">Formulario</span>
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
    
    <main class="container">
        <h2 class="main-title">Gestión de Procesos Contra los Obligados al Pago en Mora</h2>
        <section class="search-box">
            <form class="search-form">
                <div class="form-group">
                    <label for="documento">Número de Documento</label>
                    <input type="text" id="documento" placeholder="Ingrese número de documento">
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" placeholder="Ingrese nombre">
                </div>
                <div class="form-group">
                    <label for="tipo-proceso">Tipo de Proceso</label>
                    <select id="tipo-proceso">
                        <option value="">Todos</option>
                        <option value="multa">Multa</option>
                        <option value="contrato">Incumplimiento de Contrato</option>
                        <option value="deuda">Deuda Monetaria</option>
                        <option value="convenio">Convenio</option>
                    </select>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select id="estado">
                        <option value="">Todos</option>
                        <option value="activo">Activo</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="resuelto">Resuelto</option>
                    </select>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="search-buttons">
                    <button type="button" class="btn" id="resetBtn">Limpiar</button>
                    <button type="button" class="btn" id="searchBtn">Buscar</button>
                </div>
            </form>
        </section>
        
        <section class="results-box">
            <div class="results-header">
                <h2 class="results-title">Resultados</h2>
                <span class="results-count">Total: <span id="resultCount">15</span></span>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>Nombre</th>
                            <th>Tipo de Proceso</th>
                            <th>Monto</th>
                            <th>Fecha Inicio</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody id="resultsTable">
                        <!-- Data will be populated dynamically -->
                    </tbody>
                </table>
            </div>
            
            <div class="pagination" id="pagination">
                <!-- Pagination will be populated dynamically -->
            </div>
        </section>
    </main>
        
    <footer>
        <p>Regional Santander - Área de Cobro Coactivo</p>
        <p>Dirección: Calle 16 No. 27-37 Bucaramanga</p>
        <p>© 2025 SENA - Servicio Nacional de Aprendizaje</p>
    </footer>

    <script src="/assets/js/Modulo/Deudores/Deudores.js"></script>
    <!-- Scripts del Boton -->
    <script src="/assets/js/back-to-top.js"></script>
</body>
</html>