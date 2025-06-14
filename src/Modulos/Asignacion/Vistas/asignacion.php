<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Trámites – Asignación de Casos</title>
    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="/assets/CSS/Modulo/Asignacion/asignacion.css"/>
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
                <a href="/registros" style="text-decoration: none;"><i class="fa-solid fa-house fa-lg" style="color: white;"></i>
                    <div>
                        <span class="text" style="color: white;">Registros</span>
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
        <h1 class="header__title">Panel Admin Trámites</h1>
        <nav class="header__nav">
            <button class="tab-button tab-button--active" data-tab="pendientes">Casos Pendientes</button>
            <button class="tab-button" data-tab="correcciones">Correcciones a Realizar</button>
        </nav>
    </section>

    <main class="main-content">

        <!-- TAB: Casos Pendientes -->
        <section id="pendientes" class="tab-content tab-content--active">
            <div class="card-2">
                <div class="card-header">
                    <h2 class="section-title">Casos Pendientes de Asignación</h2>
                    <div class="card-h">
                        <input type="text" class="form-control" placeholder="Buscar Por Radicado...">
                        <button type="button" class="btn-new-user">
                            <i class="fa-solid fa-plus-circle"></i> 
                            Buscar Casos 
                        </button>
                    </div>
                </div>
            </div>        
            <table class="responsive-table">
            <thead>
                <tr>
                <th>No Radicado</th>
                <th>Deudor</th>
                <th>Tipo Trámite</th>
                <th>Fecha Recepción</th>
                <th>Asignar a</th>
                <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="pendientes-body">
                <!--  Tabla Dinamica  -->
            </tbody>
            </table>
        </section>

        <!-- TAB: Correcciones de Abogados -->
        <section id="correcciones" class="tab-content">
            <div class="card-2">
                <div class="card-header">
                    <h2 class="section-title">Casos con Correcciones de Abogados</h2>
                    <div class="card-h">
                        <input type="text" class="form-control" placeholder="Buscar Por Radicado...">
                        <button type="button" class="btn-new-user">
                            <i class="fa-solid fa-plus-circle"></i> 
                            Buscar Casos 
                        </button>
                    </div>
                </div>
            </div>
            <table class="responsive-table">
            <thead>
                <tr>
                <th>No Radicado</th>
                <th>Deudor</th>
                <th>Comentario Error</th>
                <th>Acción Admin</th>
                </tr>
            </thead>
            <tbody id="correcciones-body">
                <!-- Tabla Dinamica  -->
            </tbody>
            </table>
        </section>
    </main>

    <!-- Modal para Modificar Caso -->
    <div id="modal-edit" class="modal hidden">
    <div class="modal__container">
        <h3 class="modal__title">Modificar Caso</h3>
        <form id="form-edit">
        <input type="hidden" name="caso_id" id="edit-caso-id" />
        <label for="edit-monto">Monto Original (COP):</label>
        <input type="number" id="edit-monto" name="monto_original" required />

        <label for="edit-intereses">Intereses Acumulados (COP):</label>
        <input type="number" id="edit-intereses" name="intereses_acumulados" required />

        <label for="edit-costos">Costos Administrativos (COP):</label>
        <input type="number" id="edit-costos" name="costos_administrativos" required />

        <div class="modal__actions">
            <button type="submit" class="btn btn--save">Guardar</button>
            <button type="button" class="btn btn--cancel" id="btn-cancel-edit">Cancelar</button>
        </div>
        </form>
    </div>
    </div>

    <script src="/assets/js/Modulo/Asignacion/asignacion.js"></script> 
    <script src="/assets/js/Modulo/Asignacion/asignacion.js" type="module"></script>
    <script src="/assets/js/Modulo/Asignacion/modal.js" type="module"></script>
    <script src="/assets/js/Modulo/Asignacion/backToTop.js" type="module"></script>
</body>
</html>