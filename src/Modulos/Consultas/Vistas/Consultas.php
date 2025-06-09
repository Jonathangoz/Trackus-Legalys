<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Consultas - Sistema Cobro Coactivo</title>
        <link rel="stylesheet" href="/assets/CSS/Consultas.css">
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
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
                <img src="/assets/images/sena.blanco.png" class="img-sena" alt="Logo SENA">            
                <p>SENA Regional Santander</p>
            </div>
            <div class="title-container">
                <h1>CONSULTAS</h1>
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
                        <li><a href="/login" class="sesion"><i class="fa-solid fa-right-from-bracket me-2" style="margin-right: 15px;"></i>Cerrar sesión</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="container">
                <div class="section-title">
                    <h2>Consulta de Procesos</h2>
                    <p>Consulte el estado actual de su proceso, utilizando su número de identificación o el número del proceso.</p>
                </div>
                <div class="form-container">
                    <h3>Formulario de Consulta</h3>
                    <form>
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="tipo-documento">Tipo de Documento</label>
                                    <select id="tipo-documento" required>
                                        <option value="">Seleccione una opción</option>
                                        <option value="cc">Cédula de Ciudadanía</option>
                                        <option value="nit">NIT</option>
                                        <option value="ce">Cédula de Extranjería</option>
                                        <option value="pp">Pasaporte</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="numero-documento">Número de Documento</label>
                                    <input type="text" id="numero-documento" placeholder="Ingrese su número de documento" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="num-proceso">Número de Proceso (Opcional)</label>
                            <input type="text" id="num-proceso" placeholder="Si conoce el número de proceso, ingréselo aquí">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn">Consultar</button>
                        </div>
                    </form>
                </div>
            </div>

    <!-- Scripts del Boton -->
    <script src="/assets/js/back-to-top.js"></script>
    </body>
</html>